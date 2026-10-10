<?php

namespace App\Services;

use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\InventoryMovement;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesService
{
    public const OPEN_STATUSES = ['confirmed', 'partially_fulfilled'];

    public const PAYMENT_METHODS = [
        'cash', 'gcash', 'maya', 'bank_transfer', 'check', 'pdc', 'credit',
    ];

    public function __construct(private DocumentNumberService $numbers)
    {
    }

    /*
    |--------------------------------------------------------------------------
    | DIRECT SALE (CHECKOUT)
    |--------------------------------------------------------------------------
    | Order + items + stock deduction + movements + payment, all-or-nothing.
    */
    public function checkout(array $data, ?int $userId): array
    {
        return DB::transaction(function () use ($data, $userId) {

            // Double-submit protection
            if (!empty($data['checkout_token'])) {
                $existing = CustomerOrder::where('checkout_token', $data['checkout_token'])->first();

                if ($existing) {
                    return ['order' => $existing, 'payment' => null, 'change' => 0.0, 'duplicate' => true];
                }
            }

            $lines = $this->prepareLines($data['items']);
            $products = $this->loadSellableProducts($lines);

            // Lock inventory rows in ascending product_id order
            $inventories = $this->lockInventories($lines->keys());

            // Re-check stock after the lock
            $short = $this->shortages($lines, $products, $inventories, null);

            if ($short) {
                throw ValidationException::withMessages(['items' => $short]);
            }

            // Total computed from DATABASE prices only
            $total = 0.0;

            foreach ($lines as $productId => $quantity) {
                $total += round($quantity * $this->money($products[$productId]->unit_price), 2);
            }

            $total = round($total, 2);

            $pay = $total > 0.004
                ? $this->normalizePayment($data['payment_method'], (float) ($data['amount_paid'] ?? 0), $total)
                : null;

            $receipt = $pay ? $this->resolveReceiptNumber($data['receipt_number'] ?? null) : null;

            $order = $this->createOrder($data, $lines, $products, [
                'status' => 'fulfilled',
                'check' => 'available',
                'decision' => 'confirmed',
                'reserve' => false,
                'fulfill' => true,
                'notes' => 'Direct sale: stock verified and released at checkout.',
            ], $userId);

            // Deduct stock + movement per item
            foreach ($lines as $productId => $quantity) {
                $inventory = $inventories[$productId];
                $price = $this->money($products[$productId]->unit_price);

                $before = (int) $inventory->current_stock;
                $after = $before - $quantity;

                $inventory->update(['current_stock' => $after]);

                InventoryMovement::create([
                    'product_id' => $productId,
                    'user_id' => $userId,
                    'customer_order_id' => $order->id,
                    'movement_type' => 'stock_out',
                    'transaction_date' => $data['order_date'],
                    'quantity' => -$quantity,
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'supplier_customer' => $order->customer_name,
                    'unit_cost' => null,
                    'unit_price' => $price,
                    'amount' => round($quantity * $price, 2),
                    'reason' => 'Direct sale',
                    'customer_order_reference' => $order->order_number,
                    'receipt_number' => $receipt,
                    'received_by' => $data['received_by'] ?? null,
                    'reference' => null,
                ]);
            }

            $payment = $pay
                ? $this->createPayment($order, $receipt, $data, $pay, $userId, $data['order_date'])
                : null;

            return [
                'order' => $order,
                'payment' => $payment,
                'change' => $pay['change'] ?? 0.0,
                'duplicate' => false,
            ];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | OPEN ORDER (reserve only)
    |--------------------------------------------------------------------------
    | Enough stock for the WHOLE order -> confirmed + reserved.
    | Otherwise -> for_purchasing, NOTHING reserved.
    */
    public function saveOpenOrder(array $data, ?int $userId): array
    {
        return DB::transaction(function () use ($data, $userId) {

            if (!empty($data['checkout_token'])) {
                $existing = CustomerOrder::where('checkout_token', $data['checkout_token'])->first();

                if ($existing) {
                    return ['order' => $existing, 'reserved' => $existing->status === 'confirmed', 'shortages' => [], 'duplicate' => true];
                }
            }

            $lines = $this->prepareLines($data['items']);
            $products = $this->loadSellableProducts($lines);
            $inventories = $this->lockInventories($lines->keys());

            $short = $this->shortages($lines, $products, $inventories, null);

            if ($short) {
                $order = $this->createOrder($data, $lines, $products, [
                    'status' => 'for_purchasing',
                    'check' => 'insufficient',
                    'decision' => 'for_purchasing',
                    'reserve' => false,
                    'fulfill' => false,
                    'notes' => implode(' | ', $short),
                ], $userId);

                return ['order' => $order, 'reserved' => false, 'shortages' => $short, 'duplicate' => false];
            }

            $order = $this->createOrder($data, $lines, $products, [
                'status' => 'confirmed',
                'check' => 'available',
                'decision' => 'confirmed',
                'reserve' => true,
                'fulfill' => false,
                'notes' => 'Open order: stock reserved. Physical stock not deducted yet.',
            ], $userId);

            return ['order' => $order, 'reserved' => true, 'shortages' => [], 'duplicate' => false];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RECHECK
    |--------------------------------------------------------------------------
    */
    public function recheck(CustomerOrder $customerOrder, ?int $userId): array
    {
        return DB::transaction(function () use ($customerOrder, $userId) {

            $order = CustomerOrder::with('items')->lockForUpdate()->findOrFail($customerOrder->id);

            if (!in_array($order->status, ['pending_inventory_check', 'for_purchasing'], true)) {
                throw ValidationException::withMessages([
                    'order' => 'Only orders waiting for inventory check or marked For Purchasing can be rechecked.',
                ]);
            }

            [$lines, $products] = $this->linesFromOrder($order);
            $inventories = $this->lockInventories($lines->keys());
            $short = $this->shortages($lines, $products, $inventories, $order->id);

            $data = [
                'inventory_check_status' => $short ? 'insufficient' : 'available',
                'inventory_checked_by' => $userId,
                'inventory_checked_at' => now(),
                'inventory_check_notes' => $short
                    ? implode(' | ', $short)
                    : 'Rechecked: enough available stock for the whole order.',
            ];

            if ($short) {
                $data['status'] = 'for_purchasing';
                $data['owner_decision'] = 'for_purchasing';
            }

            $order->update($data);

            return ['order' => $order, 'available' => !$short, 'shortages' => $short];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIRM (reserve whole order, all-or-nothing)
    |--------------------------------------------------------------------------
    */
    public function confirm(CustomerOrder $customerOrder, ?int $userId): CustomerOrder
    {
        return DB::transaction(function () use ($customerOrder, $userId) {

            $order = CustomerOrder::with('items')->lockForUpdate()->findOrFail($customerOrder->id);

            if (!in_array($order->status, ['pending_inventory_check', 'for_purchasing'], true)) {
                throw ValidationException::withMessages([
                    'order' => 'Only pending or For Purchasing orders can be confirmed.',
                ]);
            }

            [$lines, $products] = $this->linesFromOrder($order);
            $inventories = $this->lockInventories($lines->keys());
            $short = $this->shortages($lines, $products, $inventories, $order->id);

            if ($short) {
                throw ValidationException::withMessages(['order' => $short]);
            }

            foreach ($order->items as $item) {
                $item->update(['reserved_quantity' => (int) $item->quantity]);
            }

            $order->update([
                'status' => 'confirmed',
                'owner_decision' => 'confirmed',
                'inventory_check_status' => 'available',
                'inventory_checked_by' => $userId,
                'inventory_checked_at' => now(),
                'inventory_check_notes' => 'Confirmed by Owner. Stock reserved; physical stock not deducted.',
            ]);

            return $order;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL (nothing released, nothing paid)
    |--------------------------------------------------------------------------
    */
    public function cancel(CustomerOrder $customerOrder): CustomerOrder
    {
        return DB::transaction(function () use ($customerOrder) {

            $order = CustomerOrder::with(['items', 'payments'])->lockForUpdate()->findOrFail($customerOrder->id);

            if ($order->status === 'cancelled') {
                throw ValidationException::withMessages(['order' => 'This order is already cancelled.']);
            }

            if ($order->items->sum('fulfilled_quantity') > 0) {
                throw ValidationException::withMessages([
                    'order' => 'Some items were already released. Use Void to reverse the transaction.',
                ]);
            }

            if ($order->payments->where('status', '!=', 'voided')->count() > 0) {
                throw ValidationException::withMessages([
                    'order' => 'This order already has payments. Use Void to reverse the transaction.',
                ]);
            }

            // Release reservations
            $order->items()->update(['reserved_quantity' => 0]);

            $order->update([
                'status' => 'cancelled',
                'owner_decision' => 'cancelled',
            ]);

            return $order;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELEASE (open order -> physical stock deducted)
    |--------------------------------------------------------------------------
    | $quantities = [order_item_id => quantity]; null = release everything remaining.
    */
    public function release(CustomerOrder $customerOrder, ?array $quantities, string $receivedBy, string $date, ?int $userId): CustomerOrder
    {
        return DB::transaction(function () use ($customerOrder, $quantities, $receivedBy, $date, $userId) {

            $order = CustomerOrder::with('items.product')->lockForUpdate()->findOrFail($customerOrder->id);

            if (!in_array($order->status, self::OPEN_STATUSES, true)) {
                throw ValidationException::withMessages([
                    'order' => 'Only confirmed or partially fulfilled orders can be released.',
                ]);
            }

            $releases = [];

            foreach ($order->items as $item) {
                $remaining = max(0, (int) $item->quantity - (int) $item->fulfilled_quantity);

                $qty = $quantities === null ? $remaining : (int) ($quantities[$item->id] ?? 0);

                if ($qty <= 0) {
                    continue;
                }

                if ($qty > $remaining) {
                    throw ValidationException::withMessages([
                        'quantities' => "{$item->product->product_name}: only {$remaining} unit(s) remaining to release.",
                    ]);
                }

                $releases[$item->id] = $qty;
            }

            if (!$releases) {
                throw ValidationException::withMessages(['quantities' => 'Enter at least one quantity to release.']);
            }

            $releasingItems = $order->items->whereIn('id', array_keys($releases));

            $inventories = $this->lockInventories($releasingItems->pluck('product_id'));

            // Validate physical stock, accounting for OTHER orders' reservations only
            foreach ($releasingItems as $item) {
                $qty = $releases[$item->id];
                $stock = (int) $inventories[$item->product_id]->current_stock;
                $others = $this->reservedByOthers($item->product_id, $order->id);

                if ($qty > $stock) {
                    throw ValidationException::withMessages([
                        'quantities' => "{$item->product->product_name}: not enough physical stock (stock {$stock}, releasing {$qty}).",
                    ]);
                }

                if ($qty > $stock - $others) {
                    throw ValidationException::withMessages([
                        'quantities' => "{$item->product->product_name}: remaining stock is reserved for other orders (stock {$stock}, reserved for others {$others}).",
                    ]);
                }
            }

            foreach ($releasingItems as $item) {
                $qty = $releases[$item->id];
                $inventory = $inventories[$item->product_id];

                $before = (int) $inventory->current_stock;
                $after = $before - $qty;

                $inventory->update(['current_stock' => $after]);

                $item->update([
                    'fulfilled_quantity' => (int) $item->fulfilled_quantity + $qty,
                    'reserved_quantity' => max(0, (int) $item->reserved_quantity - $qty),
                ]);

                InventoryMovement::create([
                    'product_id' => $item->product_id,
                    'user_id' => $userId,
                    'customer_order_id' => $order->id,
                    'movement_type' => 'stock_out',
                    'transaction_date' => $date,
                    'quantity' => -$qty,
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'supplier_customer' => $order->customer_name,
                    'unit_cost' => null,
                    'unit_price' => $item->unit_price,
                    'amount' => round($qty * (float) $item->unit_price, 2),
                    'reason' => 'Open order release',
                    'customer_order_reference' => $order->order_number,
                    'receipt_number' => null,
                    'received_by' => $receivedBy,
                    'reference' => null,
                ]);
            }

            $allFulfilled = $order->items->every(function ($item) {
                return (int) $item->fulfilled_quantity >= (int) $item->quantity;
            });

            $order->update([
                'status' => $allFulfilled ? 'fulfilled' : 'partially_fulfilled',
            ]);

            return $order;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENT (later / partial payments) - NEVER touches inventory
    |--------------------------------------------------------------------------
    */
    public function recordPayment(CustomerOrder $customerOrder, array $data, ?int $userId): array
    {
        return DB::transaction(function () use ($customerOrder, $data, $userId) {

            $order = CustomerOrder::with(['items', 'payments'])->lockForUpdate()->findOrFail($customerOrder->id);

            if ($order->voided_at || !in_array($order->status, ['confirmed', 'partially_fulfilled', 'fulfilled'], true)) {
                throw ValidationException::withMessages([
                    'customer_order_id' => 'Payments can only be recorded for confirmed, partially fulfilled or fulfilled orders.',
                ]);
            }

            $tendered = (float) ($data['amount'] ?? $data['amount_paid'] ?? 0);

            $pay = $this->normalizePayment($data['payment_method'], $tendered, $order->payable_amount);

            $receipt = $this->resolveReceiptNumber($data['receipt_number'] ?? null);

            $data['payment_notes'] = $data['notes'] ?? null;

            $payment = $this->createPayment(
                $order,
                $receipt,
                $data,
                $pay,
                $userId,
                $data['payment_date'] ?? now()->toDateString()
            );

            return ['payment' => $payment, 'change' => $pay['change']];
        });
    }

    /*
    |--------------------------------------------------------------------------
    | VOID (whole order)
    |--------------------------------------------------------------------------
    */
    public function void(CustomerOrder $customerOrder, string $reason, ?int $userId): CustomerOrder
    {
        return DB::transaction(function () use ($customerOrder, $reason, $userId) {

            $order = CustomerOrder::with(['items', 'payments'])->lockForUpdate()->findOrFail($customerOrder->id);

            if ($order->voided_at) {
                throw ValidationException::withMessages(['order' => 'This order has already been voided.']);
            }

            if ($order->status === 'cancelled') {
                throw ValidationException::withMessages(['order' => 'A cancelled order has nothing to void.']);
            }

            // Non-reversed stock_out movements (also finds older records by order number)
            $movements = InventoryMovement::where('movement_type', 'stock_out')
                ->where(function ($q) use ($order) {
                    $q->where('customer_order_id', $order->id)
                        ->orWhere('customer_order_reference', $order->order_number);
                })
                ->whereDoesntHave('reversal')
                ->orderBy('product_id')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $inventories = $this->lockInventories($movements->pluck('product_id'));

            foreach ($movements as $movement) {
                $inventory = $inventories[$movement->product_id];

                $qty = abs((int) $movement->quantity);
                $before = (int) $inventory->current_stock;
                $after = $before + $qty;

                $inventory->update(['current_stock' => $after]);

                InventoryMovement::create([
                    'product_id' => $movement->product_id,
                    'user_id' => $userId,
                    'customer_order_id' => $order->id,
                    'movement_type' => 'void_reversal',
                    'transaction_date' => now()->toDateString(),
                    'quantity' => $qty,
                    'stock_before' => $before,
                    'stock_after' => $after,
                    'supplier_customer' => $movement->supplier_customer,
                    'unit_cost' => null,
                    'unit_price' => $movement->unit_price,
                    'amount' => $movement->amount,
                    'reason' => 'Void: ' . $reason,
                    'customer_order_reference' => $order->order_number,
                    'receipt_number' => $movement->receipt_number,
                    'received_by' => null,
                    'reference' => $order->order_number,
                    'reversal_of_id' => $movement->id,
                ]);
            }

            $order->payments()->where('status', '!=', 'voided')->update(['status' => 'voided']);

            $order->items()->update([
                'reserved_quantity' => 0,
                'fulfilled_quantity' => 0,
            ]);

            $order->update([
                'status' => 'cancelled',
                'owner_decision' => 'cancelled',
                'voided_at' => now(),
                'voided_by' => $userId,
                'void_reason' => $reason,
            ]);

            return $order;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | LOCKING HELPER (also used by Stock In / Adjust)
    |--------------------------------------------------------------------------
    | Locks inventory rows in ASCENDING product_id order to avoid deadlocks.
    | Returns a collection keyed by product_id.
    */
    public function lockInventories($productIds): Collection
    {
        $ids = collect($productIds)->map(fn ($id) => (int) $id)->unique()->sort()->values();

        $locked = collect();

        foreach ($ids as $id) {
            $inventory = \App\Models\Inventory::where('product_id', $id)->lockForUpdate()->first();

            if (!$inventory) {
                DB::table('inventories')->insertOrIgnore([
                    'product_id' => $id,
                    'current_stock' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $inventory = \App\Models\Inventory::where('product_id', $id)->lockForUpdate()->first();
            }

            $locked->put($id, $inventory);
        }

        return $locked;
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    private function money($value): float
    {
        return round((float) $value, 2);
    }

    /* Merge duplicate products, sort ascending: [product_id => quantity] */
    private function prepareLines(array $items): Collection
    {
        $merged = [];

        foreach ($items as $item) {
            $id = (int) $item['product_id'];
            $merged[$id] = ($merged[$id] ?? 0) + (int) $item['quantity'];
        }

        ksort($merged);

        return collect($merged);
    }

    private function loadSellableProducts(Collection $lines): Collection
    {
        $products = Product::whereIn('id', $lines->keys())->orderBy('id')->get()->keyBy('id');

        foreach ($lines as $productId => $quantity) {
            $product = $products->get($productId);

            if (!$product || $product->status !== 'active') {
                throw ValidationException::withMessages([
                    'items' => 'Inactive or missing products cannot be sold.',
                ]);
            }
        }

        return $products;
    }

    /* [lines, products] built from an existing order's items */
    private function linesFromOrder(CustomerOrder $order): array
    {
        $lines = [];

        foreach ($order->items as $item) {
            $lines[(int) $item->product_id] = ($lines[(int) $item->product_id] ?? 0) + (int) $item->quantity;
        }

        ksort($lines);

        $lines = collect($lines);
        $products = Product::whereIn('id', $lines->keys())->get()->keyBy('id');

        return [$lines, $products];
    }

    private function reservedByOthers(int $productId, ?int $excludeOrderId): int
    {
        return (int) CustomerOrderItem::where('product_id', $productId)
            ->when($excludeOrderId, function ($q) use ($excludeOrderId) {
                $q->where('customer_order_id', '!=', $excludeOrderId);
            })
            ->whereHas('customerOrder', function ($q) {
                $q->whereIn('status', self::OPEN_STATUSES);
            })
            ->sum('reserved_quantity');
    }

    /* Human readable shortage messages (empty = enough stock for everything) */
    private function shortages(Collection $lines, Collection $products, Collection $inventories, ?int $excludeOrderId): array
    {
        $messages = [];

        foreach ($lines as $productId => $quantity) {
            $stock = (int) $inventories[$productId]->current_stock;
            $reserved = $this->reservedByOthers($productId, $excludeOrderId);
            $available = $stock - $reserved;

            if ($available < $quantity) {
                $name = $products[$productId]->product_name ?? "Product #{$productId}";
                $available = max(0, $available);

                $messages[] = "{$name}: requested {$quantity}, available {$available} (stock {$stock}, reserved for other orders {$reserved}).";
            }
        }

        return $messages;
    }

    private function createOrder(array $data, Collection $lines, Collection $products, array $state, ?int $userId): CustomerOrder
    {
        $order = CustomerOrder::create([
            'order_number' => $this->numbers->next('customer_order', 'CO'),
            'customer_name' => trim((string) ($data['customer_name'] ?? '')) ?: 'Walk-in Customer',
            'customer_contact' => $data['customer_contact'] ?? null,
            'order_date' => $data['order_date'],
            'status' => $state['status'],
            'inventory_check_status' => $state['check'],
            'inventory_checked_by' => $userId,
            'inventory_checked_at' => now(),
            'inventory_check_notes' => $state['notes'],
            'owner_decision' => $state['decision'],
            'notes' => $data['notes'] ?? null,
            'user_id' => $userId,
            'checkout_token' => $data['checkout_token'] ?? null,
        ]);

        foreach ($lines as $productId => $quantity) {
            // Price snapshot comes from the DATABASE, never from the browser
            $price = $this->money($products[$productId]->unit_price);

            $order->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'reserved_quantity' => $state['reserve'] ? $quantity : 0,
                'fulfilled_quantity' => $state['fulfill'] ? $quantity : 0,
                'unit_price' => $price,
                'subtotal' => round($quantity * $price, 2),
            ]);
        }

        return $order->load('items');
    }

    /*
     * Decides how much is recorded, the payment status and the change.
     */
    private function normalizePayment(string $method, float $tendered, float $payable): array
    {
        $payable = round($payable, 2);

        if ($payable <= 0.004) {
            throw ValidationException::withMessages([
                'amount_paid' => 'This order has no remaining balance to pay.',
            ]);
        }

        // Credit / utang: the whole remaining amount is put on credit
        if ($method === 'credit') {
            return ['amount' => $payable, 'status' => 'unpaid', 'change' => 0.0];
        }

        $tendered = round($tendered, 2);

        if ($tendered < 0.01) {
            throw ValidationException::withMessages(['amount_paid' => 'Enter the amount received.']);
        }

        if ($method !== 'cash' && $tendered > $payable + 0.004) {
            throw ValidationException::withMessages([
                'amount_paid' => 'Non-cash payments cannot exceed the remaining balance of ₱' . number_format($payable, 2) . '.',
            ]);
        }

        return [
            'amount' => min($tendered, $payable),
            'status' => in_array($method, ['check', 'pdc'], true) ? 'pending' : 'paid',
            'change' => $method === 'cash' ? round(max(0, $tendered - $payable), 2) : 0.0,
        ];
    }

    private function resolveReceiptNumber(?string $manual): string
    {
        $manual = trim((string) $manual);

        if ($manual !== '') {
            if (Payment::where('receipt_number', $manual)->exists()) {
                throw ValidationException::withMessages([
                    'receipt_number' => 'This receipt number is already used.',
                ]);
            }

            return $manual;
        }

        return $this->numbers->next('receipt', 'OR');
    }

    private function createPayment(CustomerOrder $order, string $receipt, array $data, array $pay, ?int $userId, $date): Payment
    {
        return Payment::create([
            'customer_order_id' => $order->id,
            'processed_by' => $userId,
            'receipt_number' => $receipt,
            'payment_date' => $date,
            'payment_method' => $data['payment_method'],
            'amount' => $pay['amount'],
            'status' => $pay['status'],
            'reference_number' => $data['reference_number'] ?? null,
            'check_number' => $data['check_number'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'check_date' => $data['check_date'] ?? null,
            'maturity_date' => $data['maturity_date'] ?? null,
            'notes' => $data['payment_notes'] ?? null,
        ]);
    }
}