<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierPaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::query()
            ->with([
                'customerOrder',
                'processedBy',
            ])
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->q;

                $query->where(function ($q) use ($search) {
                    $q->where('receipt_number', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('customerOrder', function ($order) use ($search) {
                            $order->where('customer_name', 'like', "%{$search}%")
                                ->orWhere('order_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('payment_method'), function ($query) use ($request) {
                $query->where(
                    'payment_method',
                    $request->payment_method
                );
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest('payment_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $confirmedOrders = CustomerOrder::query()
            ->with('items.product')
            ->where('status', 'confirmed')
            ->latest('order_date')
            ->get();

        $todayTotal = Payment::query()
            ->whereDate('payment_date', today())
            ->whereNotIn('status', ['voided', 'unpaid'])
            ->sum('amount');

        $todayCount = Payment::query()
            ->whereDate('payment_date', today())
            ->count();

        $pendingTotal = Payment::query()
            ->whereIn('status', ['pending', 'unpaid'])
            ->sum('amount');

        return view('cashier.cashier-payments', [
            'payments' => $payments,
            'confirmedOrders' => $confirmedOrders,
            'todayTotal' => $todayTotal,
            'todayCount' => $todayCount,
            'pendingTotal' => $pendingTotal,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_order_id' => [
                'required',
                'exists:customer_orders,id',
            ],

            'receipt_number' => [
                'required',
                'string',
                'max:100',
                'unique:payments,receipt_number',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'payment_method' => [
                'required',
                'in:cash,gcash,maya,bank_transfer,check,pdc,credit',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'reference_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'check_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'bank_name' => [
                'nullable',
                'string',
                'max:150',
            ],

            'check_date' => [
                'nullable',
                'date',
            ],

            'maturity_date' => [
                'nullable',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $order = CustomerOrder::with('items')->findOrFail(
            $data['customer_order_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Only confirmed customer orders can proceed to payment processing.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $order->status === 'confirmed',
            422,
            'Only confirmed customer orders can be processed for payment.'
        );

        /*
        |--------------------------------------------------------------------------
        | Determine payment status.
        |--------------------------------------------------------------------------
        */

        $status = match ($data['payment_method']) {
            'check', 'pdc' => 'pending',
            'credit' => 'unpaid',
            default => 'paid',
        };

        /*
        |--------------------------------------------------------------------------
        | Create payment record.
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($data, $status) {
            Payment::create([
                'customer_order_id' => $data['customer_order_id'],
                'processed_by' => auth()->id(),
                'receipt_number' => $data['receipt_number'],
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'amount' => $data['amount'],
                'status' => $status,
                'reference_number' => $data['reference_number'] ?? null,
                'check_number' => $data['check_number'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'check_date' => $data['check_date'] ?? null,
                'maturity_date' => $data['maturity_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('cashier.payments')
            ->with('success', 'Payment recorded successfully.');
    }
}