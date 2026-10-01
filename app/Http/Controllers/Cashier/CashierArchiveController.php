<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\CustomerOrder;
use App\Models\Payment;
use Illuminate\Http\Request;

class CashierArchiveController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        $search = $request->input('q');
        $type = $request->input('type');
        $year = $request->input('year');


        /*
        |--------------------------------------------------------------------------
        | Archived Payments / Receipts
        |--------------------------------------------------------------------------
        */

        $paymentsQuery = Payment::query()
            ->with([
                'customerOrder',
                'processedBy',
            ])
            ->where('status', '!=', 'voided')
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('receipt_number', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhere('check_number', 'like', "%{$search}%")
                        ->orWhereHas('customerOrder', function ($orderQuery) use ($search) {

                            $orderQuery->where('order_number', 'like', "%{$search}%")
                                ->orWhere('customer_name', 'like', "%{$search}%")
                                ->orWhere('customer_contact', 'like', "%{$search}%");

                        });

                });

            })
            ->when($year, function ($query) use ($year) {
                $query->whereYear('payment_date', $year);
            })
            ->when($type === 'payment', function ($query) {
                $query->whereIn('payment_method', [
                    'cash',
                    'gcash',
                    'maya',
                    'bank_transfer',
                ]);
            })
            ->when($type === 'credit', function ($query) {
                $query->where('payment_method', 'credit');
            })
            ->when($type === 'check', function ($query) {
                $query->whereIn('payment_method', [
                    'check',
                    'pdc',
                ]);
            })
            ->latest('payment_date')
            ->latest('id');

        /*
        |--------------------------------------------------------------------------
        | Paginated Payment Records
        |--------------------------------------------------------------------------
        */

        $payments = $paymentsQuery
            ->paginate(15)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Credit / Utang Records
        |--------------------------------------------------------------------------
        |
        | These are the original credit records.
        | They represent an A/R obligation, not actual cash collected.
        |--------------------------------------------------------------------------
        */

        $creditPayments = Payment::query()
            ->with([
                'customerOrder',
                'processedBy',
            ])
            ->where('payment_method', 'credit')
            ->where('status', '!=', 'voided')
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('receipt_number', 'like', "%{$search}%")
                        ->orWhereHas('customerOrder', function ($orderQuery) use ($search) {

                            $orderQuery->where('order_number', 'like', "%{$search}%")
                                ->orWhere('customer_name', 'like', "%{$search}%")
                                ->orWhere('customer_contact', 'like', "%{$search}%");

                        });

                });

            })
            ->when($year, function ($query) use ($year) {
                $query->whereYear('payment_date', $year);
            })
            ->latest('payment_date')
            ->latest('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Check / PDC Records
        |--------------------------------------------------------------------------
        */

        $checks = Payment::query()
            ->with([
                'customerOrder',
                'processedBy',
            ])
            ->whereIn('payment_method', [
                'check',
                'pdc',
            ])
            ->where('status', '!=', 'voided')
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('receipt_number', 'like', "%{$search}%")
                        ->orWhere('check_number', 'like', "%{$search}%")
                        ->orWhere('bank_name', 'like', "%{$search}%")
                        ->orWhereHas('customerOrder', function ($orderQuery) use ($search) {

                            $orderQuery->where('order_number', 'like', "%{$search}%")
                                ->orWhere('customer_name', 'like', "%{$search}%");

                        });

                });

            })
            ->when($year, function ($query) use ($year) {
                $query->whereYear('payment_date', $year);
            })
            ->latest('payment_date')
            ->latest('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Settled Credit Accounts
        |--------------------------------------------------------------------------
        |
        | A credit account is considered settled when:
        |
        | Order Total - Actual Payments = 0
        |
        | The original "credit" record is NOT counted as a payment.
        |--------------------------------------------------------------------------
        */

        $settledAccounts = collect();


        /*
        |--------------------------------------------------------------------------
        | Get Customer Orders With Credit
        |--------------------------------------------------------------------------
        */

        $creditOrders = CustomerOrder::query()
            ->with([
                'items',
                'payments',
            ])
            ->whereHas('payments', function ($query) {
                $query->where('payment_method', 'credit')
                    ->where('status', '!=', 'voided');
            })
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_contact', 'like', "%{$search}%");

                });

            })
            ->when($year, function ($query) use ($year) {
                $query->whereYear('order_date', $year);
            })
            ->latest('order_date')
            ->latest('id')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Calculate Settled Accounts
        |--------------------------------------------------------------------------
        */

        foreach ($creditOrders as $order) {

            $orderTotal = $order->items->sum(function ($item) {
                return (float) $item->subtotal;
            });


            /*
            | Actual payments that reduce A/R.
            |
            | Credit itself is excluded because credit creates
            | the receivable rather than settling it.
            */

            $actualPayments = $order->payments
                ->where('status', '!=', 'voided')
                ->where('payment_method', '!=', 'credit')
                ->sum(function ($payment) {
                    return (float) $payment->amount;
                });


            $balance = max(
                0,
                $orderTotal - $actualPayments
            );


            /*
            | Only fully settled accounts appear in Archive.
            */

            if ($orderTotal > 0 && $balance <= 0.01) {

                $settledAccounts->push((object) [

                    'id' => $order->id,

                    'order_number' => $order->order_number,

                    'customer_name' => $order->customer_name,

                    'order_date' => $order->order_date,

                    'order_total' => $orderTotal,

                    'amount_paid' => $actualPayments,

                ]);

            }

        }


        /*
        |--------------------------------------------------------------------------
        | If "Settled Accounts" filter is selected
        |--------------------------------------------------------------------------
        */

        if ($type === 'settled') {

            /*
            | Payment table is still retained because the Blade
            | expects it to exist.
            |
            | The actual displayed settled records come from
            | $settledAccounts.
            */

            $settledAccounts = $settledAccounts
                ->values();

        }


        /*
        |--------------------------------------------------------------------------
        | Summary Counts
        |--------------------------------------------------------------------------
        */

        $creditCount = Payment::query()
            ->where('payment_method', 'credit')
            ->where('status', '!=', 'voided')
            ->when($year, function ($query) use ($year) {
                $query->whereYear('payment_date', $year);
            })
            ->count();


        $checkCount = Payment::query()
            ->whereIn('payment_method', [
                'check',
                'pdc',
            ])
            ->where('status', '!=', 'voided')
            ->when($year, function ($query) use ($year) {
                $query->whereYear('payment_date', $year);
            })
            ->count();


        $settledCount = $settledAccounts->count();


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view('cashier.cashier-archive', [

            'payments' => $payments,

            'creditPayments' => $creditPayments,

            'checks' => $checks,

            'settledAccounts' => $settledAccounts,

            'creditCount' => $creditCount,

            'checkCount' => $checkCount,

            'settledCount' => $settledCount,

        ]);
    }
}