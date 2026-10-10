<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Payment;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CashierPaymentController extends Controller
{
    public function __construct(private SalesService $sales)
    {
    }

    public function index(Request $request)
    {
        $payments = Payment::query()
            ->with(['customerOrder', 'processedBy'])
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
                $query->where('payment_method', $request->payment_method);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest('payment_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        /*
         * Orders that can still receive a payment:
         * confirmed, partially fulfilled AND fulfilled (direct sales / credit)
         * with a remaining payable amount.
         */
        $confirmedOrders = CustomerOrder::query()
            ->with(['items', 'payments'])
            ->whereIn('status', ['confirmed', 'partially_fulfilled', 'fulfilled'])
            ->latest('order_date')
            ->get()
            ->filter(fn ($order) => $order->payable_amount > 0.004)
            ->values();

        $todayTotal = Payment::query()
            ->whereDate('payment_date', today())
            ->whereIn('status', ['paid', 'cleared', 'pending'])
            ->sum('amount');

        $todayCount = Payment::query()
            ->whereDate('payment_date', today())
            ->where('status', '!=', 'voided')
            ->count();

        /*
         * Receivables are DERIVED per order (total - collected),
         * not the sum of every unpaid/pending row.
         */
        $pendingTotal = CustomerOrder::query()
            ->whereIn('status', ['confirmed', 'partially_fulfilled', 'fulfilled'])
            ->withSum('items as items_total', 'subtotal')
            ->withSum(['payments as collected_total' => function ($q) {
                $q->whereIn('status', ['paid', 'cleared']);
            }], 'amount')
            ->get()
            ->sum(fn ($o) => max(0, (float) $o->items_total - (float) $o->collected_total));

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
            'customer_order_id' => ['required', 'exists:customer_orders,id'],
            'receipt_number' => ['nullable', 'string', 'max:100', 'unique:payments,receipt_number'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', Rule::in(SalesService::PAYMENT_METHODS)],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'check_number' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'check_date' => ['nullable', 'date'],
            'maturity_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = CustomerOrder::findOrFail($data['customer_order_id']);

        // Same payment rules as the Owner module. Inventory is never touched.
        $result = $this->sales->recordPayment($order, $data, auth()->id());

        $message = "Payment recorded. Receipt {$result['payment']->receipt_number}.";

        if ($result['change'] > 0) {
            $message .= ' Change: ₱' . number_format($result['change'], 2);
        }

        return redirect()
            ->route('cashier.payments')
            ->with('success', $message);
    }
}