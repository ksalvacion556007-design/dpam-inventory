<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CashierReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : now()->startOfMonth();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : now()->endOfDay();

        $method = $request->input('payment_method');

        /*
        |--------------------------------------------------------------------------
        | Payment Query
        |--------------------------------------------------------------------------
        */

        $paymentsQuery = Payment::query()
            ->with([
                'customerOrder',
                'processedBy',
            ])
            ->whereBetween('payment_date', [
                $dateFrom->toDateString(),
                $dateTo->toDateString(),
            ])
            ->when($method, function ($query) use ($method) {
                $query->where('payment_method', $method);
            })
            ->where('status', '!=', 'voided');

        $payments = $paymentsQuery
            ->latest('payment_date')
            ->latest('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Actual Collections
        |--------------------------------------------------------------------------
        |
        | Credit is excluded because credit creates an A/R obligation.
        | Pending checks/PDCs are also not treated as cleared collections.
        |--------------------------------------------------------------------------
        */

        $collections = $payments->filter(function ($payment) {
            return in_array($payment->payment_method, [
                'cash',
                'gcash',
                'maya',
                'bank_transfer',
            ]) && $payment->status === 'paid';
        });

        /*
        |--------------------------------------------------------------------------
        | Check / PDC
        |--------------------------------------------------------------------------
        */

        $checks = $payments->filter(function ($payment) {
            return in_array($payment->payment_method, [
                'check',
                'pdc',
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Credit / Utang
        |--------------------------------------------------------------------------
        */

        $creditPayments = $payments->filter(function ($payment) {
            return $payment->payment_method === 'credit';
        });

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalCollected = $collections->sum('amount');

        $cashCollected = $collections
            ->where('payment_method', 'cash')
            ->sum('amount');

        $gcashCollected = $collections
            ->where('payment_method', 'gcash')
            ->sum('amount');

        $mayaCollected = $collections
            ->where('payment_method', 'maya')
            ->sum('amount');

        $bankTransferCollected = $collections
            ->where('payment_method', 'bank_transfer')
            ->sum('amount');

        $pendingChecks = $checks
            ->whereIn('status', ['pending', 'unpaid'])
            ->sum('amount');

        $clearedChecks = $checks
            ->where('status', 'cleared')
            ->sum('amount');

        $creditCreated = $creditPayments->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Accounts Receivable
        |--------------------------------------------------------------------------
        |
        | For every customer order:
        |
        | Order Total
        | - Actual payments applied
        | = Remaining A/R
        |
        | The original "credit" record is not treated as a collection.
        |--------------------------------------------------------------------------
        */

        $orders = CustomerOrder::query()
            ->with([
                'items',
                'payments',
            ])
            ->whereHas('payments', function ($query) {
                $query->where('payment_method', 'credit');
            })
            ->get();

        $totalReceivable = 0;

        foreach ($orders as $order) {
            $orderTotal = $order->items->sum('subtotal');

            $actualPayments = $order->payments
                ->where('status', '!=', 'voided')
                ->where('payment_method', '!=', 'credit')
                ->sum('amount');

            $balance = max(0, $orderTotal - $actualPayments);

            $totalReceivable += $balance;
        }

        /*
        |--------------------------------------------------------------------------
        | Report counts
        |--------------------------------------------------------------------------
        */

        $paymentCount = $payments->count();
        $collectionCount = $collections->count();
        $creditCount = $creditPayments->count();
        $checkCount = $checks->count();

        return view('cashier.cashier-reports', compact(
            'payments',
            'collections',
            'checks',
            'creditPayments',
            'dateFrom',
            'dateTo',
            'method',
            'totalCollected',
            'cashCollected',
            'gcashCollected',
            'mayaCollected',
            'bankTransferCollected',
            'pendingChecks',
            'clearedChecks',
            'creditCreated',
            'totalReceivable',
            'paymentCount',
            'collectionCount',
            'creditCount',
            'checkCount'
        ));
    }
}