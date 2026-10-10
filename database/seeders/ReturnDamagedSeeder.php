<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReturnDamagedSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::where('role', 'owner')->first();

        if (!$owner) {
            $this->command->warn('Owner user not found. ReturnDamagedSeeder skipped.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN 1
        |--------------------------------------------------------------------------
        | Customer returned 5 units of Shell Rimula R4 X.
        | The returned items are usable, so stock increases.
        */

        $this->recordMovement(
            productName: 'Shell Rimula R4 X',
            quantity: 5,
            movementType: 'return',
            reason: 'Customer return - items are usable and restocked.',
            supplierCustomer: 'ABC Industrial Customer',
            reference: 'RET-2026-0001',
            user: $owner,
        );

        /*
        |--------------------------------------------------------------------------
        | RETURN 2
        |--------------------------------------------------------------------------
        | Customer returned 2 units of Mobil Delvac MX.
        */

        $this->recordMovement(
            productName: 'Mobil Delvac MX',
            quantity: 2,
            movementType: 'return',
            reason: 'Customer return - unopened and resalable.',
            supplierCustomer: 'Davao Equipment',
            reference: 'RET-2026-0002',
            user: $owner,
        );

        /*
        |--------------------------------------------------------------------------
        | DAMAGED 1
        |--------------------------------------------------------------------------
        | 3 units of Caltex Delo Gold were found damaged.
        | Damaged stock is removed from usable inventory.
        */

        $this->recordMovement(
            productName: 'Caltex Delo Gold',
            quantity: 3,
            movementType: 'damaged',
            reason: 'Damaged containers discovered during inventory inspection.',
            supplierCustomer: null,
            reference: 'DMG-2026-0001',
            user: $owner,
        );

        /*
        |--------------------------------------------------------------------------
        | DAMAGED 2
        |--------------------------------------------------------------------------
        | 1 unit of Long Life Coolant was damaged.
        */

        $this->recordMovement(
            productName: 'Long Life Coolant',
            quantity: 1,
            movementType: 'damaged',
            reason: 'Container damaged and item is no longer usable.',
            supplierCustomer: null,
            reference: 'DMG-2026-0002',
            user: $owner,
        );
    }

    private function recordMovement(
        string $productName,
        int $quantity,
        string $movementType,
        string $reason,
        ?string $supplierCustomer,
        string $reference,
        User $user,
    ): void {
        $product = Product::where('product_name', $productName)->first();

        if (!$product) {
            $this->command->warn(
                "Product '{$productName}' not found. Movement {$reference} skipped."
            );

            return;
        }

        $inventory = Inventory::firstOrCreate(
            ['product_id' => $product->id],
            ['current_stock' => 0]
        );

        $before = (int) $inventory->current_stock;

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        | Usable returned products go back into stock.
        |
        | DAMAGED
        |--------------------------------------------------------------------------
        | Damaged products are removed from usable stock.
        */

        if ($movementType === 'return') {
            $after = $before + $quantity;
            $movementQuantity = $quantity;
        } else {
            $after = max(0, $before - $quantity);
            $movementQuantity = -$quantity;
        }

        $inventory->update([
            'current_stock' => $after,
        ]);

        InventoryMovement::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'customer_order_id' => null,

            'movement_type' => $movementType,

            'transaction_date' => now()->toDateString(),

            'quantity' => $movementQuantity,

            'stock_before' => $before,

            'stock_after' => $after,

            'supplier_customer' => $supplierCustomer,

            'unit_cost' => null,

            'unit_price' => $product->unit_price,

            'amount' => round(
                abs($movementQuantity) * (float) $product->unit_price,
                2
            ),

            'reason' => $reason,

            'customer_order_reference' => null,

            'receipt_number' => null,

            'received_by' => $user->name,

            'reference' => $reference,
        ]);
    }
}