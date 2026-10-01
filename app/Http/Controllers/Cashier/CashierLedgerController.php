<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Payment;
use Illuminate\Http\Request;

class CashierLedgerController extends Controller
{
    public function index(Request $request)
    {
        $orders = CustomerOrder::query()
            ->with([
                'items.product',
                'payments.processedBy',
            ])
            ->whereHas('payments', function ($query) {
                $query->where('payment_method', 'credit')
                    ->orWhereIn('status', [
                        'unpaid',
                        'pending',
                        'cleared',
                    ]);
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->q;

                $query->where(function ($q) use ($search) {
                    $q->where(
                        'order_number',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'customer_name',
                        'like',
                        "%{$search}%"
                    );
                });
            })
            ->latest('order_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $ledger = $orders->through(function ($order) {
            return $this->buildLedgerRow($order);
        });

        $totalReceivable = $this->calculateTotalReceivable();

        $totalCollected = Payment::query()
            ->where('payment_method', 'credit')
            ->whereNotIn('status', ['voided'])
            ->sum('amount');

        $pendingPayments = Payment::query()
            ->whereIn('status', ['pending', 'unpaid'])
            ->sum('amount');

        $customerCount = CustomerOrder::query()
            ->whereHas('payments', function ($query) {
                $query->where('payment_method', 'credit');
            })
            ->distinct('customer_name')
            ->count('customer_name');

        return view('cashier.cashier-ledger', [
            'ledger' => $ledger,
            'totalReceivable' => $totalReceivable,
            'totalCollected' => $totalCollected,
            'pendingPayments' => $pendingPayments,
            'customerCount' => $customerCount,
        ]);
    }

    public function show(CustomerOrder $customerOrder)
    {
        $customerOrder->load([
            'items.product',
            'payments.processedBy',
        ]);

        $ledgerRow = $this->buildLedgerRow($customerOrder);

        return view('cashier.cashier-ledger', [
            'ledger' => collect([$ledgerRow]),
            'selectedOrder' => $customerOrder,
            'totalReceivable' => $this->calculateTotalReceivable(),
            'totalCollected' => Payment::query()
                ->where('payment_method', 'credit')
                ->whereNotIn('status', ['voided'])
                ->sum('amount'),
            'pendingPayments' => Payment::query()
                ->whereIn('status', ['pending', 'unpaid'])
                ->sum('amount'),
            'customerCount' => CustomerOrder::query()
                ->whereHas('payments', function ($query) {
                    $query->where('payment_method', 'credit');
                })
                ->distinct('customer_name')
                ->count('customer_name'),
        ]);
    }

    private function buildLedgerRow(CustomerOrder $order): array
    {
        $orderTotal = (float) $order->items->sum(function ($item) {
            return (float) $item->subtotal;
        });

        $payments = $order->payments
            ->where('status', '!=', 'voided');

        $totalPaid = (float) $payments->sum('amount');

        $balance = max(
            0,
            $orderTotal - $totalPaid
        );

        if ($balance <= 0) {
            $status = 'settled';
        } elseif ($totalPaid > 0) {
            $status = 'partially_paid';
        } else {
            $status = 'outstanding';
        }

        return [
            'order' => $order,
            'order_total' => $orderTotal,
            'total_paid' => $totalPaid,
            'balance' => $balance,
            'status' => $status,
            'payments' => $payments,
        ];
    }

    private function calculateTotalReceivable(): float
    {
        return CustomerOrder::query()
            ->with([
                'items',
                'payments',
            ])
            ->whereHas('payments', function ($query) {
                $query->where('payment_method', 'credit');
            })
            ->get()
            ->sum(function ($order) {
                $orderTotal = (float) $order->items->sum(
                    fn ($item) => (float) $item->subtotal
                );

                $paid = (float) $order->payments
                    ->where('status', '!=', 'voided')
                    ->sum('amount');

                return max(
                    0,
                    $orderTotal - $paid
                );
            });
    }
}