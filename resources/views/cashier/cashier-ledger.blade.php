<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts Receivable / Ledger | Cashier | DPAM IMS</title>

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
        .admin-badge{background:#dbeafe;color:#1e40af;padding:7px 14px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.05em}

        .summary{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:22px}
        .summary-card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;position:relative;overflow:hidden}
        .summary-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--card-color,var(--accent))}
        .summary-label{font-size:13px;color:var(--muted);margin-bottom:8px}
        .summary-value{font-size:26px;font-weight:700}

        .toolbar,.table-panel,.details-card{background:#fff;border:1px solid var(--line);border-radius:14px}
        .toolbar{padding:18px;margin-bottom:20px}
        .filter-form{display:grid;grid-template-columns:1fr auto auto;gap:10px}
        input{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:14px;font-family:inherit;color:var(--ink);transition:border-color .15s,box-shadow .15s}
        input:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(37,99,235,.15)}

        .button{display:inline-block;padding:10px 16px;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;font-family:inherit;text-align:center;transition:background .15s}
        .button-primary{background:var(--accent);color:#fff}.button-primary:hover{background:var(--accent-dark)}
        .button-secondary{background:#e2e8f0;color:#334155}.button-secondary:hover{background:#cbd5e1}
        .button-small{padding:7px 12px;font-size:12px}

        .table-panel{overflow:hidden}
        .table-header{padding:18px 20px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;gap:12px}
        .table-header h2{margin:0;font-size:17px}
        .table-wrapper{overflow-x:auto}
        table{width:100%;border-collapse:collapse}
        th,td{padding:13px 16px;border-bottom:1px solid var(--line);text-align:left;font-size:13px;white-space:nowrap}
        th{color:var(--muted);font-weight:600;background:#f8fafc}
        tbody tr:hover td{background:#f8fafc}
        tr:last-child td{border-bottom:none}
        .customer{font-weight:600}
        .order-number{font-weight:700}
        .money{font-weight:600}
        .balance{font-weight:700;color:#b91c1c}
        .empty{padding:40px;text-align:center;color:var(--muted);font-size:13px}

        .status{display:inline-block;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700}
        .s-green{background:#dcfce7;color:#166534}.s-amber{background:#fef3c7;color:#92400e}.s-red{background:#fee2e2;color:#991b1b}

        .details-body{padding:22px}
        .detail-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:25px}
        .detail-label{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted);margin-bottom:5px}
        .detail-value{font-size:14px;font-weight:600}
        .payment-title{font-size:14px;font-weight:700;margin-bottom:10px}

        @media(max-width:1100px){.summary,.detail-grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:800px){.layout{flex-direction:column}.sidebar{width:100%}.main{padding:20px}.topbar{flex-direction:column;align-items:flex-start}.summary,.filter-form,.detail-grid{grid-template-columns:1fr}}
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
            <a href="{{ route('cashier.ledger') }}" class="active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h12a2 2 0 012 2v14H7a2 2 0 01-2-2z"/><path d="M9 9h6M9 13h6"/></svg>
                Accounts Receivable / Ledger
            </a>
            <a href="{{ route('cashier.reports') }}">
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
                <h1 class="page-title">Accounts Receivable / Ledger</h1>
                <div class="page-description">Customer credit, payments and outstanding balances.</div>
            </div>
            <div class="admin-badge">CASHIER</div>
        </div>


        @isset($selectedOrder)

            @php
                $selectedTotal = (float) $selectedOrder->items->sum(fn ($item) => (float) $item->subtotal);

                $selectedPaid = (float) $selectedOrder->payments
                    ->where('status', '!=', 'voided')
                    ->sum('amount');

                $selectedBalance = max(0, $selectedTotal - $selectedPaid);
            @endphp

            {{-- LEDGER DETAILS (with back) --}}
            <section class="details-card">

                <div class="table-header">
                    <h2>Ledger: {{ $selectedOrder->order_number }}</h2>

                    <a href="{{ route('cashier.ledger') }}" class="button button-secondary button-small">
                        &larr; Back to Ledger
                    </a>
                </div>

                <div class="details-body">

                    <div class="detail-grid">
                        <div>
                            <div class="detail-label">Customer</div>
                            <div class="detail-value">{{ $selectedOrder->customer_name }}</div>
                        </div>
                        <div>
                            <div class="detail-label">Order Number</div>
                            <div class="detail-value">{{ $selectedOrder->order_number }}</div>
                        </div>
                        <div>
                            <div class="detail-label">Order Total</div>
                            <div class="detail-value">₱{{ number_format($selectedTotal, 2) }}</div>
                        </div>
                        <div>
                            <div class="detail-label">Outstanding</div>
                            <div class="detail-value">₱{{ number_format($selectedBalance, 2) }}</div>
                        </div>
                    </div>

                    <div class="payment-title">Payment History</div>

                    <div class="table-wrapper" style="border:1px solid var(--line);border-radius:10px">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt No.</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Processed By</th>
                                </tr>
                            </thead>

                            <tbody>
                            @forelse($selectedOrder->payments->where('status', '!=', 'voided') as $payment)
                                <tr>
                                    <td>{{ optional($payment->payment_date)->format('M d, Y') }}</td>
                                    <td>{{ $payment->receipt_number }}</td>
                                    <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                    <td>₱{{ number_format((float) $payment->amount, 2) }}</td>
                                    <td>
                                        @if($payment->status === 'paid' || $payment->status === 'cleared')
                                            <span class="status s-green">{{ ucfirst($payment->status) }}</span>
                                        @elseif($payment->status === 'pending')
                                            <span class="status s-amber">Pending</span>
                                        @else
                                            <span class="status s-red">{{ ucfirst($payment->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->processedBy->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6">No payment history found.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

            </section>

        @else

            <div class="summary">
                <div class="summary-card" style="--card-color:#dc2626">
                    <div class="summary-label">Outstanding Receivable</div>
                    <div class="summary-value">₱{{ number_format($totalReceivable, 2) }}</div>
                </div>

                <div class="summary-card" style="--card-color:#059669">
                    <div class="summary-label">Credit Payments Recorded</div>
                    <div class="summary-value">₱{{ number_format($totalCollected, 2) }}</div>
                </div>

                <div class="summary-card" style="--card-color:#f59e0b">
                    <div class="summary-label">Pending / Unpaid Payments</div>
                    <div class="summary-value">₱{{ number_format($pendingPayments, 2) }}</div>
                </div>

                <div class="summary-card" style="--card-color:#2563eb">
                    <div class="summary-label">Customers With Credit</div>
                    <div class="summary-value">{{ $customerCount }}</div>
                </div>
            </div>


            <div class="toolbar">
                <form method="GET" action="{{ route('cashier.ledger') }}" class="filter-form">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search customer or order number">
                    <button type="submit" class="button button-primary">Search</button>
                    <a href="{{ route('cashier.ledger') }}" class="button button-secondary">Clear</a>
                </form>
            </div>


            <section class="table-panel">

                <div class="table-header">
                    <h2>Accounts Receivable Records</h2>
                </div>

                <div class="table-wrapper">

                    @if($ledger->count())

                        <table>
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Order No.</th>
                                    <th>Order Date</th>
                                    <th>Order Total</th>
                                    <th>Paid</th>
                                    <th>Outstanding Balance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($ledger as $row)
                                <tr>
                                    <td><div class="customer">{{ $row['order']->customer_name }}</div></td>
                                    <td><span class="order-number">{{ $row['order']->order_number }}</span></td>
                                    <td>{{ optional($row['order']->order_date)->format('M d, Y') }}</td>
                                    <td><span class="money">₱{{ number_format($row['order_total'], 2) }}</span></td>
                                    <td><span class="money">₱{{ number_format($row['total_paid'], 2) }}</span></td>
                                    <td><span class="balance">₱{{ number_format($row['balance'], 2) }}</span></td>
                                    <td>
                                        @if($row['status'] === 'settled')
                                            <span class="status s-green">Settled</span>
                                        @elseif($row['status'] === 'partially_paid')
                                            <span class="status s-amber">Partially Paid</span>
                                        @else
                                            <span class="status s-red">Outstanding</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('cashier.ledger.show', $row['order']) }}" class="button button-secondary button-small">View Ledger</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    @else

                        <div class="empty">No accounts receivable records found.</div>

                    @endif

                </div>

                @if($ledger instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="pagination" style="padding:18px;">
                        {{ $ledger->links() }}
                    </div>
                @endif

            </section>

        @endisset

    </main>

</div>

</body>
</html>