<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Archive | DPAM IMS</title>

    <style>
        :root{--bg:#f1f5f9;--ink:#0f172a;--muted:#64748b;--line:#e2e8f0;--sidebar:#0b1220;--accent:#2563eb;--accent-dark:#1d4ed8;--danger:#dc2626;--success:#059669}
        *{box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{margin:0;font-family:"Segoe UI",system-ui,-apple-system,Roboto,Arial,sans-serif;background:var(--bg);color:var(--ink)}
        a{color:inherit;text-decoration:none}
        .layout{display:flex;min-height:100vh}

        .sidebar{width:256px;flex-shrink:0;background:var(--sidebar);color:#fff;padding:24px 14px;display:flex;flex-direction:column}
        .brand{font-size:20px;font-weight:700;padding:0 12px}
        .subtitle{font-size:12px;color:#94a3b8;line-height:1.5;padding:0 12px;margin:4px 0 26px}
        .menu-title{font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin:6px 12px 8px}
        .menu a,.logout-button{display:flex;align-items:center;gap:11px;width:100%;padding:11px 12px;margin-bottom:4px;border-radius:8px;color:#cbd5e1;background:transparent;border:none;font-size:14px;font-family:inherit;cursor:pointer;text-align:left;transition:background .15s,color .15s}
        .menu a svg,.logout-button svg{width:18px;height:18px;flex-shrink:0}
        .menu a:hover,.logout-button:hover{background:#1e293b;color:#fff}
        .menu a.active{background:var(--accent);color:#fff;font-weight:600}
        .logout-form{margin-top:auto;padding-top:24px}

        .main{flex:1;min-width:0;padding:30px 34px}
        .topbar{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:26px}
        .page-title{margin:0;font-size:26px;font-weight:700}
        .page-description{margin-top:5px;color:var(--muted);font-size:14px}
        .admin-badge{background:#dbeafe;color:#1e40af;padding:7px 14px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.05em}

        .summary{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:20px}
        .summary-card{display:block;background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s}
        .summary-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--card-color,var(--accent))}
        .summary-card:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(15,23,42,.09)}
        .summary-label{color:var(--muted);font-size:13px;margin-bottom:8px}
        .summary-value{font-size:28px;font-weight:700}

        .filter-panel,.section{background:#fff;border:1px solid var(--line);border-radius:14px}
        .filter-panel{padding:18px;margin-bottom:20px}
        .filter-form{display:grid;grid-template-columns:2fr 1fr 1fr auto auto;gap:10px;align-items:end}
        .field{display:flex;flex-direction:column;gap:6px}
        .field label{font-size:12px;color:var(--muted);font-weight:600}
        input,select{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:13px;font-family:inherit;color:var(--ink);transition:border-color .15s,box-shadow .15s}
        input:focus,select:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(37,99,235,.15)}

        .button{display:inline-block;padding:10px 16px;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;font-family:inherit;text-align:center;transition:background .15s}
        .button-primary{background:var(--accent);color:#fff}.button-primary:hover{background:var(--accent-dark)}
        .button-secondary{background:#e2e8f0;color:#334155}.button-secondary:hover{background:#cbd5e1}

        .section{margin-bottom:20px;overflow:hidden;scroll-margin-top:20px}
        .section-header{padding:18px 20px;border-bottom:1px solid var(--line)}
        .section-header h2{margin:0;font-size:17px}
        .table-wrapper{overflow-x:auto}
        table{width:100%;min-width:800px;border-collapse:collapse}
        th,td{padding:13px 16px;border-bottom:1px solid var(--line);text-align:left;font-size:13px;white-space:nowrap}
        th{color:var(--muted);font-weight:600;background:#f8fafc}
        tbody tr:hover td{background:#f8fafc}
        tr:last-child td{border-bottom:none}
        .amount{text-align:right;font-weight:600}
        .empty{padding:36px;text-align:center;color:var(--muted);font-size:13px}
        .pagination-container{padding:16px 20px}

        .status{display:inline-block;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase}
        .s-green{background:#dcfce7;color:#166534}.s-blue{background:#dbeafe;color:#1e40af}
        .s-amber{background:#fef3c7;color:#92400e}.s-red{background:#fee2e2;color:#991b1b}

        @media(max-width:1100px){.summary{grid-template-columns:repeat(2,1fr)}.filter-form{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:800px){.layout{flex-direction:column}.sidebar{width:100%}.main{padding:20px}.topbar{flex-direction:column;align-items:flex-start}.summary,.filter-form{grid-template-columns:1fr}}
    </style>
</head>

<body>

<div class="layout">

    <aside class="sidebar">
        <div class="brand">DPAM IMS</div>
        <div class="subtitle">Inventory Management System</div>
        <div class="menu-title">Cashier</div>

        <nav class="menu">
            <a href="{{ route('cashier.dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                Dashboard
            </a>
            <a href="{{ route('cashier.customer-orders') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4h6v3H9z"/><path d="M7 5H5v16h14V5h-2"/><path d="M9 12h6M9 16h6"/></svg>
                Customer Orders
            </a>
            <a href="{{ route('cashier.payments') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19M6 15h4"/></svg>
                Payments
            </a>
            <a href="{{ route('cashier.ledger') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h12a2 2 0 012 2v14H7a2 2 0 01-2-2z"/><path d="M9 9h6M9 13h6"/></svg>
                Accounts Receivable / Ledger
            </a>
            <a href="{{ route('cashier.reports') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V4M4 20h16"/><path d="M8 16v-5M12 16V8M16 16v-3"/></svg>
                Reports
            </a>
            <a href="{{ route('cashier.archive') }}" class="active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="5" rx="1"/><path d="M5 9v10h14V9M10 13h4"/></svg>
                Archive
            </a>
        </nav>

        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4H5v16h4M16 8l4 4-4 4M20 12H9"/></svg>
                Logout
            </button>
        </form>
    </aside>


    <main class="main">

        <div class="topbar">
            <div>
                <h1 class="page-title">Cashier Archive</h1>
                <div class="page-description">Historical payment, credit, check and settled account records (read-only).</div>
            </div>
            <div class="admin-badge">CASHIER</div>
        </div>


        {{-- SUMMARY (jump to section) --}}
        <div class="summary">

            <a href="#sec-payments" class="summary-card" style="--card-color:#2563eb">
                <div class="summary-label">Archived Payments</div>
                <div class="summary-value">{{ $payments->total() ?? $payments->count() }}</div>
            </a>

            <a href="#sec-credit" class="summary-card" style="--card-color:#f59e0b">
                <div class="summary-label">Credit Records</div>
                <div class="summary-value">{{ $creditCount }}</div>
            </a>

            <a href="#sec-checks" class="summary-card" style="--card-color:#7c3aed">
                <div class="summary-label">Check / PDC Records</div>
                <div class="summary-value">{{ $checkCount }}</div>
            </a>

            <a href="#sec-settled" class="summary-card" style="--card-color:#059669">
                <div class="summary-label">Settled Accounts</div>
                <div class="summary-value">{{ $settledCount }}</div>
            </a>

        </div>


        {{-- FILTER --}}
        <div class="filter-panel">
            <form method="GET" action="{{ route('cashier.archive') }}" class="filter-form">

                <div class="field">
                    <label for="q">Search</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Receipt no., order no., customer...">
                </div>

                <div class="field">
                    <label for="type">Record Type</label>
                    <select name="type" id="type">
                        <option value="">All Records</option>
                        <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payments / Receipts</option>
                        <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="check" {{ request('type') === 'check' ? 'selected' : '' }}>Checks / PDC</option>
                        <option value="settled" {{ request('type') === 'settled' ? 'selected' : '' }}>Settled Accounts</option>
                    </select>
                </div>

                <div class="field">
                    <label for="year">Year</label>
                    <select name="year" id="year">
                        <option value="">All Years</option>
                        @for($year = now()->year; $year >= now()->year - 10; $year--)
                            <option value="{{ $year }}" {{ (string) request('year') === (string) $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="button button-primary">Search</button>

                <a href="{{ route('cashier.archive') }}" class="button button-secondary">Reset</a>

            </form>
        </div>


        {{-- ARCHIVED PAYMENTS --}}
        <section class="section" id="sec-payments">

            <div class="section-header"><h2>Archived Payments / Receipts</h2></div>

            <div class="table-wrapper">
                @if($payments->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Receipt No.</th>
                                <th>Customer</th>
                                <th>Order No.</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th class="amount">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date?->format('M d, Y') ?? '—' }}</td>
                                <td>{{ $payment->receipt_number }}</td>
                                <td>{{ $payment->customerOrder->customer_name ?? '—' }}</td>
                                <td>{{ $payment->customerOrder->order_number ?? '—' }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td>
                                    @php
                                        $statusClass = match($payment->status) {
                                            'paid' => 's-green',
                                            'cleared' => 's-blue',
                                            'pending' => 's-amber',
                                            'unpaid' => 's-red',
                                            default => 's-amber',
                                        };
                                    @endphp
                                    <span class="status {{ $statusClass }}">{{ $payment->status }}</span>
                                </td>
                                <td class="amount">₱{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">No archived payment records found.</div>
                @endif
            </div>

            @if(method_exists($payments, 'links'))
                <div class="pagination-container">
                    {{ $payments->links() }}
                </div>
            @endif

        </section>


        {{-- CREDIT / UTANG --}}
        <section class="section" id="sec-credit">

            <div class="section-header"><h2>Archived Credit</h2></div>

            <div class="table-wrapper">
                @if($creditPayments->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Receipt No.</th>
                                <th>Customer</th>
                                <th>Order No.</th>
                                <th class="amount">Credit Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($creditPayments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date?->format('M d, Y') ?? '—' }}</td>
                                <td>{{ $payment->receipt_number }}</td>
                                <td>{{ $payment->customerOrder->customer_name ?? '—' }}</td>
                                <td>{{ $payment->customerOrder->order_number ?? '—' }}</td>
                                <td class="amount">₱{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">No archived credit / utang records found.</div>
                @endif
            </div>

        </section>


        {{-- CHECK / PDC --}}
        <section class="section" id="sec-checks">

            <div class="section-header"><h2>Archived Check / PDC Records</h2></div>

            <div class="table-wrapper">
                @if($checks->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Receipt No.</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Check No.</th>
                                <th>Bank</th>
                                <th>Check Date</th>
                                <th>Status</th>
                                <th class="amount">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($checks as $payment)
                            <tr>
                                <td>{{ $payment->payment_date?->format('M d, Y') ?? '—' }}</td>
                                <td>{{ $payment->receipt_number }}</td>
                                <td>{{ $payment->customerOrder->customer_name ?? '—' }}</td>
                                <td>{{ strtoupper($payment->payment_method) }}</td>
                                <td>{{ $payment->check_number ?? '—' }}</td>
                                <td>{{ $payment->bank_name ?? '—' }}</td>
                                <td>{{ $payment->check_date?->format('M d, Y') ?? '—' }}</td>
                                <td>
                                    @php
                                        $statusClass = match($payment->status) {
                                            'paid' => 's-green',
                                            'cleared' => 's-blue',
                                            'pending' => 's-amber',
                                            'unpaid' => 's-red',
                                            default => 's-amber',
                                        };
                                    @endphp
                                    <span class="status {{ $statusClass }}">{{ $payment->status }}</span>
                                </td>
                                <td class="amount">₱{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">No archived check or PDC records found.</div>
                @endif
            </div>

        </section>


        {{-- SETTLED ACCOUNTS --}}
        <section class="section" id="sec-settled">

            <div class="section-header"><h2>Archived Settled Accounts</h2></div>

            <div class="table-wrapper">
                @if($settledAccounts->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>Customer</th>
                                <th>Order Date</th>
                                <th class="amount">Order Total</th>
                                <th class="amount">Amount Paid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($settledAccounts as $account)
                            <tr>
                                <td>{{ $account->order_number }}</td>
                                <td>{{ $account->customer_name }}</td>
                                <td>{{ $account->order_date?->format('M d, Y') ?? '—' }}</td>
                                <td class="amount">₱{{ number_format($account->order_total, 2) }}</td>
                                <td class="amount">₱{{ number_format($account->amount_paid, 2) }}</td>
                                <td><span class="status s-green">Settled</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">No settled accounts found.</div>
                @endif
            </div>

        </section>

    </main>
</div>

</body>
</html>