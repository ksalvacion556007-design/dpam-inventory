<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Reports | DPAM IMS</title>

    <style>
        :root{--bg:#f1f5f9;--ink:#0f172a;--muted:#64748b;--line:#e2e8f0;--sidebar:#0b1220;--accent:#2563eb;--accent-dark:#1d4ed8;--danger:#dc2626;--success:#059669}
        *{box-sizing:border-box}
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
        .top-actions{display:flex;align-items:center;gap:10px}
        .admin-badge{background:#dbeafe;color:#1e40af;padding:7px 14px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.05em}

        .toolbar{background:#fff;border:1px solid var(--line);border-radius:14px;padding:18px;margin-bottom:20px}
        .filter-form{display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:10px;align-items:end}
        .field{display:flex;flex-direction:column;gap:6px}
        .field label{font-size:12px;color:var(--muted);font-weight:600}
        .filter-actions{display:flex;gap:8px}
        input,select{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:14px;font-family:inherit;color:var(--ink);transition:border-color .15s,box-shadow .15s}
        input:focus,select:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(37,99,235,.15)}

        .button{display:inline-flex;align-items:center;gap:8px;justify-content:center;padding:10px 16px;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;font-family:inherit;text-align:center;transition:background .15s}
        .button svg{width:16px;height:16px}
        .button-primary{background:var(--accent);color:#fff}.button-primary:hover{background:var(--accent-dark)}
        .button-secondary{background:#e2e8f0;color:#334155}.button-secondary:hover{background:#cbd5e1}

        .cards{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:22px}
        .card{display:block;background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;position:relative;overflow:hidden;cursor:pointer;transition:transform .15s,box-shadow .15s,border-color .15s}
        .card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--card-color,var(--accent))}
        .card:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(15,23,42,.09);border-color:#cbd5e1}
        .card-label{font-size:13px;color:var(--muted);margin-bottom:10px}
        .card-value{font-size:26px;font-weight:700}
        .card-link{margin-top:10px;font-size:12px;font-weight:600;color:var(--card-color,var(--accent))}

        .chart-grid{display:grid;grid-template-columns:2fr 1fr;gap:18px;margin-bottom:22px}
        .panel,.report-section{background:#fff;border:1px solid var(--line);border-radius:14px}
        .panel{padding:22px}
        .panel-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
        .panel h2{margin:0;font-size:17px}
        .panel-link{font-size:13px;font-weight:600;color:var(--accent);cursor:pointer}
        .panel-link:hover{text-decoration:underline}

        .bar-list{display:flex;flex-direction:column;gap:10px}
        .bar-item{display:block;padding:10px 12px;border-radius:10px;cursor:pointer;transition:background .15s}
        .bar-item:hover{background:#f1f5f9}
        .bar-top{display:flex;justify-content:space-between;gap:10px;font-size:13px;margin-bottom:7px}
        .bar-track{height:10px;background:#e2e8f0;border-radius:999px;overflow:hidden}
        .bar-fill{height:100%;border-radius:999px;background:var(--bar-color,var(--accent));min-width:3px}

        .donut-wrap{display:flex;flex-direction:column;align-items:center;gap:16px}
        .donut{width:170px;height:170px}
        .donut a circle{transition:stroke-width .15s;cursor:pointer}
        .donut a:hover circle{stroke-width:26}
        .donut-total{font-size:26px;font-weight:700;fill:var(--ink)}
        .donut-sub{font-size:11px;fill:var(--muted)}
        .legend{width:100%;display:flex;flex-direction:column;gap:6px}
        .legend a{display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:8px;font-size:13px;cursor:pointer;transition:background .15s}
        .legend a:hover{background:#f1f5f9}
        .legend .dot{width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:8px}

        .report-section{margin-bottom:22px;overflow:hidden}
        .section-header{padding:18px 20px;border-bottom:1px solid var(--line)}
        .section-header h2{margin:0;font-size:17px}
        .table-wrapper{overflow-x:auto}
        table{width:100%;min-width:700px;border-collapse:collapse}
        th,td{padding:13px 16px;border-bottom:1px solid var(--line);text-align:left;font-size:13px;white-space:nowrap}
        th{color:var(--muted);font-weight:600;background:#f8fafc}
        tbody tr:hover td{background:#f8fafc}
        tr:last-child td{border-bottom:none}
        .amount{text-align:right;font-weight:600}
        .empty{padding:32px;text-align:center;color:var(--muted);font-size:13px}
        .summary-table{min-width:0}
        .summary-table td:first-child{font-weight:600;color:#334155}

        .status{display:inline-block;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase}
        .s-green{background:#dcfce7;color:#166534}.s-blue{background:#dbeafe;color:#1e40af}
        .s-amber{background:#fef3c7;color:#92400e}.s-red{background:#fee2e2;color:#991b1b}

        .print-only{display:none}

        .data-modal{display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);align-items:center;justify-content:center;padding:20px;z-index:1000}
        .data-modal.show{display:flex}
        .data-modal-box{background:#fff;width:100%;max-width:1000px;max-height:90vh;display:flex;flex-direction:column;border-radius:16px;box-shadow:0 25px 60px rgba(0,0,0,.3);overflow:hidden}
        .data-modal-header{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:18px 22px;border-bottom:1px solid var(--line)}
        .data-modal-header h2{margin:0;font-size:18px}
        .data-modal-actions{display:flex;align-items:center;gap:10px}
        .full-link{font-size:13px;font-weight:600;color:var(--accent);padding:7px 12px;border-radius:8px;background:#eff6ff}
        .full-link:hover{background:#dbeafe}
        .close-button{border:none;background:transparent;font-size:26px;line-height:1;cursor:pointer;color:var(--muted);border-radius:6px;width:34px;height:34px}
        .close-button:hover{background:#f1f5f9;color:var(--ink)}
        .data-modal-body{overflow:auto}
        .modal-loading{padding:50px;text-align:center;color:var(--muted);font-size:14px}
        .data-modal-body .report-section,.data-modal-body .table-panel{border:none;border-radius:0;margin:0}
        .data-modal-body .section-header,.data-modal-body .table-header{padding:14px 22px}
        .data-modal-body .section-header h2,.data-modal-body .table-header h2{font-size:15px}
        .data-modal-body .table-header{border-bottom:1px solid var(--line)}
        .data-modal-body .pagination{padding:16px 22px}

        @media(max-width:1100px){.cards{grid-template-columns:repeat(2,1fr)}.chart-grid{grid-template-columns:1fr}.filter-form{grid-template-columns:1fr 1fr}}
        @media(max-width:800px){.layout{flex-direction:column}.sidebar{width:100%}.main{padding:20px}.topbar{flex-direction:column;align-items:flex-start}.cards,.filter-form{grid-template-columns:1fr}}

        @media print{
            @page{margin:14mm}
            body{background:#fff}
            .sidebar,.toolbar,.top-actions,.panel-link,.card-link,.data-modal,.chart-grid{display:none !important}
            .layout{display:block}
            .main{padding:0}
            .print-only{display:block;margin-bottom:16px}
            .print-only h2{margin:0 0 4px;font-size:18px}
            .print-only div{font-size:12px;color:#475569}
            .cards{grid-template-columns:repeat(4,1fr);gap:10px}
            .card,.report-section{box-shadow:none;break-inside:avoid;border:1px solid #cbd5e1}
            .card::before{display:none}
            table{min-width:0}
            th,td{padding:8px 10px;font-size:11px}
            .status{border:1px solid #cbd5e1;background:#fff !important;color:#111 !important}
        }
    </style>
</head>

<body>

@php
    $methodBars = [
        ['label' => 'Cash',                 'value' => (float) $cashCollected,         'color' => '#059669'],
        ['label' => 'GCash',                'value' => (float) $gcashCollected,        'color' => '#2563eb'],
        ['label' => 'Maya',                 'value' => (float) $mayaCollected,         'color' => '#10b981'],
        ['label' => 'Bank Transfer',        'value' => (float) $bankTransferCollected, 'color' => '#0ea5e9'],
        ['label' => 'Cleared Check / PDC',  'value' => (float) $clearedChecks,         'color' => '#7c3aed'],
    ];
    $methodMax = max(max(array_column($methodBars, 'value')), 1);

    $checkDonut = [
        ['label' => 'Pending Checks / PDC', 'value' => (float) $pendingChecks, 'color' => '#f59e0b'],
        ['label' => 'Cleared Checks / PDC', 'value' => (float) $clearedChecks, 'color' => '#059669'],
    ];
    $checkTotal = array_sum(array_column($checkDonut, 'value'));
    $radius = 60;
    $circumference = 2 * M_PI * $radius;
    $offset = 0;
@endphp

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
            <a href="{{ route('cashier.reports') }}" class="active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V4M4 20h16"/><path d="M8 16v-5M12 16V8M16 16v-3"/></svg>
                Reports
            </a>
            <a href="{{ route('cashier.archive') }}">
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
                <h1 class="page-title">Cashier Reports</h1>
                <div class="page-description">Collections, payments, credit, receivables, checks and PDC.</div>
            </div>

            <div class="top-actions">
                <button type="button" class="button button-primary" onclick="window.print()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="9" rx="2"/><path d="M7 14h10v7H7z"/></svg>
                    Print PDF
                </button>
                <div class="admin-badge">CASHIER</div>
            </div>
        </div>


        {{-- Shown only on the printed PDF --}}
        <div class="print-only">
            <h2>DPAM IMS - Cashier Report</h2>
            <div>
                Period: {{ request('date_from', $dateFrom->toDateString()) }} to {{ request('date_to', $dateTo->toDateString()) }}
                @if($method) | Method: {{ ucwords(str_replace('_', ' ', $method)) }} @endif
                | Prepared by: {{ auth()->user()->name }}
            </div>
        </div>


        {{-- FILTER --}}
        <div class="toolbar">
            <form method="GET" action="{{ route('cashier.reports') }}" class="filter-form">

                <div class="field">
                    <label for="date_from">Date From</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from', $dateFrom->toDateString()) }}">
                </div>

                <div class="field">
                    <label for="date_to">Date To</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to', $dateTo->toDateString()) }}">
                </div>

                <div class="field">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" id="payment_method">
                        <option value="">All Payment Methods</option>
                        <option value="cash" {{ $method === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="gcash" {{ $method === 'gcash' ? 'selected' : '' }}>GCash</option>
                        <option value="maya" {{ $method === 'maya' ? 'selected' : '' }}>Maya</option>
                        <option value="bank_transfer" {{ $method === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="check" {{ $method === 'check' ? 'selected' : '' }}>Check</option>
                        <option value="pdc" {{ $method === 'pdc' ? 'selected' : '' }}>PDC</option>
                        <option value="credit" {{ $method === 'credit' ? 'selected' : '' }}>Credit / Utang</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="button button-primary">Generate</button>
                    <a href="{{ route('cashier.reports') }}" class="button button-secondary">Reset</a>
                </div>

            </form>
        </div>


        {{-- KPI CARDS (popup) --}}
        <div class="cards">

            <a href="#sec-payments" class="card js-modal" data-target="sec-payments" data-title="Payment / Receipt Report" style="--card-color:#059669">
                <div class="card-label">Total Collected</div>
                <div class="card-value">₱{{ number_format($totalCollected, 2) }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="#sec-breakdown" class="card js-modal" data-target="sec-breakdown" data-title="Collection Breakdown" style="--card-color:#2563eb">
                <div class="card-label">Cash Collected</div>
                <div class="card-value">₱{{ number_format($cashCollected, 2) }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="#sec-credit" class="card js-modal" data-target="sec-credit" data-title="Credit / Utang Report" style="--card-color:#f59e0b">
                <div class="card-label">Credit / Utang Created</div>
                <div class="card-value">₱{{ number_format($creditCreated, 2) }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="{{ route('cashier.ledger') }}" class="card js-modal" data-title="Accounts Receivable" style="--card-color:#dc2626">
                <div class="card-label">Outstanding A/R</div>
                <div class="card-value">₱{{ number_format($totalReceivable, 2) }}</div>
                <div class="card-link">View details</div>
            </a>

        </div>


        {{-- GRAPHS (popup) --}}
        <div class="chart-grid">

            <section class="panel">
                <div class="panel-head">
                    <h2>Collections by Payment Method</h2>
                    <a href="#sec-breakdown" class="panel-link js-modal" data-target="sec-breakdown" data-title="Collection Breakdown">Open</a>
                </div>

                <div class="bar-list">
                    @foreach($methodBars as $bar)
                        <a href="#sec-breakdown" class="bar-item js-modal" data-target="sec-breakdown" data-title="Collection Breakdown" style="--bar-color:{{ $bar['color'] }}">
                            <div class="bar-top"><span>{{ $bar['label'] }}</span><strong>₱{{ number_format($bar['value'], 2) }}</strong></div>
                            <div class="bar-track"><div class="bar-fill" style="width: {{ ($bar['value'] / $methodMax) * 100 }}%"></div></div>
                        </a>
                    @endforeach
                </div>
            </section>


            <section class="panel">
                <div class="panel-head">
                    <h2>Check / PDC Status</h2>
                    <a href="#sec-checks" class="panel-link js-modal" data-target="sec-checks" data-title="Check / PDC Report">Open</a>
                </div>

                <div class="donut-wrap">
                    <svg class="donut" viewBox="0 0 160 160">
                        <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="#e2e8f0" stroke-width="22"/>
                        @if($checkTotal > 0)
                            @foreach($checkDonut as $slice)
                                @if($slice['value'] > 0)
                                    @php $length = ($slice['value'] / $checkTotal) * $circumference; @endphp
                                    <a href="#sec-checks" class="js-modal" data-target="sec-checks" data-title="Check / PDC Report">
                                        <title>{{ $slice['label'] }}: ₱{{ number_format($slice['value'], 2) }}</title>
                                        <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="{{ $slice['color'] }}" stroke-width="22"
                                            stroke-dasharray="{{ $length }} {{ $circumference - $length }}"
                                            stroke-dashoffset="{{ -$offset }}" transform="rotate(-90 80 80)"/>
                                    </a>
                                    @php $offset += $length; @endphp
                                @endif
                            @endforeach
                        @endif
                        <text x="80" y="82" text-anchor="middle" class="donut-total">{{ $checkCount }}</text>
                        <text x="80" y="98" text-anchor="middle" class="donut-sub">Records</text>
                    </svg>

                    <div class="legend">
                        @foreach($checkDonut as $slice)
                            <a href="#sec-checks" class="js-modal" data-target="sec-checks" data-title="Check / PDC Report">
                                <span><span class="dot" style="background:{{ $slice['color'] }}"></span>{{ $slice['label'] }}</span>
                                <strong>₱{{ number_format($slice['value'], 2) }}</strong>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>

        </div>


        {{-- COLLECTION BREAKDOWN --}}
        <section class="report-section" id="sec-breakdown">
            <div class="section-header"><h2>Collection Breakdown</h2></div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th>Number of Transactions</th>
                            <th class="amount">Amount Collected</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Cash</td>
                            <td>{{ $collections->where('payment_method', 'cash')->count() }}</td>
                            <td class="amount">₱{{ number_format($cashCollected, 2) }}</td>
                        </tr>
                        <tr>
                            <td>GCash</td>
                            <td>{{ $collections->where('payment_method', 'gcash')->count() }}</td>
                            <td class="amount">₱{{ number_format($gcashCollected, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Maya</td>
                            <td>{{ $collections->where('payment_method', 'maya')->count() }}</td>
                            <td class="amount">₱{{ number_format($mayaCollected, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Bank Transfer</td>
                            <td>{{ $collections->where('payment_method', 'bank_transfer')->count() }}</td>
                            <td class="amount">₱{{ number_format($bankTransferCollected, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Cleared Check / PDC</td>
                            <td>{{ $checks->where('status', 'cleared')->count() }}</td>
                            <td class="amount">₱{{ number_format($clearedChecks, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>


        {{-- PAYMENT / RECEIPT REPORT --}}
        <section class="report-section" id="sec-payments">
            <div class="section-header"><h2>Payment / Receipt Report</h2></div>

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
                    <div class="empty">No payment transactions found for the selected period.</div>
                @endif
            </div>
        </section>


        {{-- CREDIT / UTANG REPORT --}}
        <section class="report-section" id="sec-credit">
            <div class="section-header"><h2>Credit / Utang Report</h2></div>

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
                    <div class="empty">No credit / utang transactions found.</div>
                @endif
            </div>
        </section>


        {{-- CHECK / PDC REPORT --}}
        <section class="report-section" id="sec-checks">
            <div class="section-header"><h2>Check / PDC Report</h2></div>

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
                    <div class="empty">No check or PDC transactions found.</div>
                @endif
            </div>
        </section>


        {{-- REPORT SUMMARY --}}
        <section class="report-section" id="sec-summary">
            <div class="section-header"><h2>Report Summary</h2></div>

            <div class="table-wrapper">
                <table class="summary-table">
                    <tbody>
                        <tr><td>Total Payment Transactions</td><td class="amount">{{ $paymentCount }}</td></tr>
                        <tr><td>Actual Collection Transactions</td><td class="amount">{{ $collectionCount }}</td></tr>
                        <tr><td>Credit Transactions</td><td class="amount">{{ $creditCount }}</td></tr>
                        <tr><td>Check / PDC Transactions</td><td class="amount">{{ $checkCount }}</td></tr>
                        <tr><td>Pending Checks / PDC</td><td class="amount">₱{{ number_format($pendingChecks, 2) }}</td></tr>
                        <tr><td>Cleared Checks / PDC</td><td class="amount">₱{{ number_format($clearedChecks, 2) }}</td></tr>
                        <tr><td>Outstanding Accounts Receivable</td><td class="amount">₱{{ number_format($totalReceivable, 2) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

    </main>
</div>


{{-- DATA POPUP MODAL --}}
<div id="dataModal" class="data-modal">
    <div class="data-modal-box">
        <div class="data-modal-header">
            <h2 id="dataModalTitle">Details</h2>
            <div class="data-modal-actions">
                <a href="#" id="dataModalFull" class="full-link">Open full page</a>
                <button type="button" class="close-button" onclick="closeDataModal()" aria-label="Close">&times;</button>
            </div>
        </div>
        <div id="dataModalBody" class="data-modal-body"></div>
    </div>
</div>


<script>
    const dataModal = document.getElementById('dataModal');
    const dataModalTitle = document.getElementById('dataModalTitle');
    const dataModalBody = document.getElementById('dataModalBody');
    const dataModalFull = document.getElementById('dataModalFull');

    function cleanModalContent(panel)
    {
        panel.querySelectorAll('script, form, button').forEach(function (el) { el.remove(); });

        panel.querySelectorAll('table').forEach(function (table)
        {
            const headers = Array.from(table.querySelectorAll('thead th'));

            const index = headers.findIndex(function (th)
            {
                const text = th.textContent.trim().toLowerCase();
                return text === 'action' || text === 'actions';
            });

            if (index === -1) return;

            table.querySelectorAll('tr').forEach(function (row)
            {
                if (row.children[index]) row.children[index].remove();
            });
        });
    }

    function openLocalModal(id, title)
    {
        const source = document.getElementById(id);
        if (!source) return;

        dataModalTitle.textContent = title || 'Details';
        dataModalFull.style.display = 'none';

        const copy = source.cloneNode(true);
        copy.removeAttribute('id');
        cleanModalContent(copy);

        dataModalBody.innerHTML = '';
        dataModalBody.appendChild(copy);
        dataModal.classList.add('show');
    }

    async function loadModalData(url, title)
    {
        dataModalTitle.textContent = title || 'Details';
        dataModalFull.href = url;
        dataModalFull.style.display = '';
        dataModalBody.innerHTML = '<div class="modal-loading">Loading...</div>';
        dataModal.classList.add('show');

        try
        {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Request failed');

            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            const panel = doc.querySelector('.table-panel');

            if (!panel) throw new Error('No data');

            cleanModalContent(panel);

            dataModalBody.innerHTML = '';
            dataModalBody.appendChild(document.importNode(panel, true));
        }
        catch (error)
        {
            dataModalBody.innerHTML = '<div class="modal-loading">Unable to load data.</div>';
        }
    }

    function closeDataModal()
    {
        dataModal.classList.remove('show');
        dataModalBody.innerHTML = '';
    }

    document.addEventListener('click', function (event)
    {
        const trigger = event.target.closest('.js-modal');

        if (trigger && !dataModal.contains(trigger))
        {
            event.preventDefault();

            if (trigger.dataset.target)
            {
                openLocalModal(trigger.dataset.target, trigger.dataset.title);
            }
            else
            {
                loadModalData(trigger.getAttribute('href'), trigger.dataset.title);
            }

            return;
        }

        const pageLink = event.target.closest('#dataModalBody a[href]');

        if (pageLink)
        {
            event.preventDefault();
            loadModalData(pageLink.href, dataModalTitle.textContent);
            return;
        }

        if (event.target === dataModal) closeDataModal();
    });

    document.addEventListener('keydown', function (event)
    {
        if (event.key === 'Escape' && dataModal.classList.contains('show')) closeDataModal();
    });
</script>

</body>
</html>