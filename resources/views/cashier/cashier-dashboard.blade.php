<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard | DPAM IMS</title>

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

        .cards{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:22px}
        .card{display:block;background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;position:relative;overflow:hidden;cursor:pointer;transition:transform .15s,box-shadow .15s,border-color .15s}
        .card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--card-color,var(--accent))}
        .card:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(15,23,42,.09);border-color:#cbd5e1}
        .card-label{font-size:13px;color:var(--muted);margin-bottom:10px}
        .card-value{font-size:28px;font-weight:700}
        .card-link{margin-top:10px;font-size:12px;font-weight:600;color:var(--card-color,var(--accent))}

        .panel{background:#fff;border:1px solid var(--line);border-radius:14px;padding:22px;margin-bottom:22px}
        .panel-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
        .panel h2{margin:0;font-size:17px}
        .panel-link{font-size:13px;font-weight:600;color:var(--accent);cursor:pointer}
        .panel-link:hover{text-decoration:underline}
        .chart-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:22px}
        .chart-grid .panel{margin-bottom:0}
        .section-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:22px}
        .section-grid .panel{margin-bottom:0}

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

        .bar-list{display:flex;flex-direction:column;gap:10px}
        .bar-item{display:block;padding:10px 12px;border-radius:10px;cursor:pointer;transition:background .15s}
        .bar-item:hover{background:#f1f5f9}
        .bar-top{display:flex;justify-content:space-between;gap:10px;font-size:13px;margin-bottom:7px}
        .bar-track{height:10px;background:#e2e8f0;border-radius:999px;overflow:hidden}
        .bar-fill{height:100%;border-radius:999px;background:var(--bar-color,var(--accent));min-width:3px}

        .table-wrapper{overflow-x:auto}
        table{width:100%;border-collapse:collapse;min-width:480px}
        th,td{padding:12px 14px;border-bottom:1px solid var(--line);text-align:left;font-size:13px;white-space:nowrap}
        th{color:var(--muted);font-weight:600;background:#f8fafc}
        tbody tr:hover td{background:#f8fafc}
        tr:last-child td{border-bottom:none}
        .amount{font-weight:600}
        .empty{color:var(--muted);font-size:14px;padding:24px 0;text-align:center}

        .status{display:inline-block;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700}
        .s-green{background:#dcfce7;color:#166534}.s-blue{background:#dbeafe;color:#1e40af}
        .s-amber{background:#fef3c7;color:#92400e}.s-red{background:#fee2e2;color:#991b1b}.s-gray{background:#f1f5f9;color:#475569}

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
        .data-modal-body .panel,.data-modal-body .table-panel{border:none;border-radius:0;margin:0;padding:0}
        .data-modal-body .panel-head,.data-modal-body .table-header{padding:14px 22px;border-bottom:1px solid var(--line);margin:0}
        .data-modal-body .panel-head h2,.data-modal-body .table-header h2{font-size:15px}
        .data-modal-body .pagination,.data-modal-body .pagination-container{padding:16px 22px}

        @media(max-width:1200px){.chart-grid{grid-template-columns:1fr 1fr}.chart-grid .panel:last-child{grid-column:1/-1}}
        @media(max-width:1000px){.cards{grid-template-columns:repeat(2,1fr)}.section-grid{grid-template-columns:1fr}}
        @media(max-width:800px){.layout{flex-direction:column}.sidebar{width:100%}.main{padding:20px}.chart-grid{grid-template-columns:1fr}.topbar{flex-direction:column;align-items:flex-start}}
        @media(max-width:560px){.cards{grid-template-columns:1fr}}
    </style>
</head>

<body>

@php
    $statusLabel = fn ($s) => ucwords(str_replace('_', ' ', (string) $s));

    $statusCounts = $customerOrders->groupBy('status')->map->count()->sortDesc();
    $statusMax = max($statusCounts->max() ?? 1, 1);

    $releaseByProduct = $recentReleases
        ->groupBy(fn ($m) => $m->product->product_name ?? 'Unknown')
        ->map(fn ($g) => $g->sum(fn ($m) => (float) ($m->amount ?? 0)))
        ->sortDesc()
        ->take(6);
    $releaseMax = max($releaseByProduct->max() ?? 1, 1);

    $donutData = [
        ['label' => 'Pending',   'count' => (int) $pendingOrderCount,   'color' => '#f59e0b', 'key' => 'pending_inventory_check'],
        ['label' => 'Confirmed', 'count' => (int) $confirmedOrderCount, 'color' => '#059669', 'key' => 'confirmed'],
    ];
    $donutTotal = array_sum(array_column($donutData, 'count'));
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
            <a href="{{ route('cashier.dashboard') }}" class="active">
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
                <h1 class="page-title">Cashier Dashboard</h1>
                <div class="page-description">Welcome, {{ auth()->user()->name }}</div>
            </div>
            <div class="admin-badge">CASHIER</div>
        </div>


        {{-- KPI CARDS (popup) --}}
        <div class="cards">

            <a href="{{ route('cashier.customer-orders', ['status' => 'pending_inventory_check']) }}" class="card js-modal" data-title="Pending Customer Orders" style="--card-color:#f59e0b">
                <div class="card-label">Pending Customer Orders</div>
                <div class="card-value">{{ $pendingOrderCount }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="{{ route('cashier.customer-orders', ['status' => 'confirmed']) }}" class="card js-modal" data-title="Confirmed Orders" style="--card-color:#059669">
                <div class="card-label">Confirmed Orders</div>
                <div class="card-value">{{ $confirmedOrderCount }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="#releases" class="card js-modal" data-target="releases" data-title="Today's Releases" style="--card-color:#2563eb">
                <div class="card-label">Today's Releases</div>
                <div class="card-value">{{ $todayStockOuts }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="#releases" class="card js-modal" data-target="releases" data-title="Released Amount" style="--card-color:#7c3aed">
                <div class="card-label">Today's Released Amount</div>
                <div class="card-value">₱{{ number_format((float) $todayReleasedAmount, 2) }}</div>
                <div class="card-link">View details</div>
            </a>

        </div>


        {{-- GRAPHS (popup) --}}
        <div class="chart-grid">

            <section class="panel">
                <div class="panel-head">
                    <h2>Order Overview</h2>
                    <a href="{{ route('cashier.customer-orders') }}" class="panel-link js-modal" data-title="Customer Orders">Open</a>
                </div>

                <div class="donut-wrap">
                    <svg class="donut" viewBox="0 0 160 160">
                        <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="#e2e8f0" stroke-width="22"/>
                        @if($donutTotal > 0)
                            @foreach($donutData as $slice)
                                @if($slice['count'] > 0)
                                    @php $length = ($slice['count'] / $donutTotal) * $circumference; @endphp
                                    <a href="{{ route('cashier.customer-orders', ['status' => $slice['key']]) }}" class="js-modal" data-title="{{ $slice['label'] }} Orders">
                                        <title>{{ $slice['label'] }}: {{ $slice['count'] }}</title>
                                        <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="{{ $slice['color'] }}" stroke-width="22"
                                            stroke-dasharray="{{ $length }} {{ $circumference - $length }}"
                                            stroke-dashoffset="{{ -$offset }}" transform="rotate(-90 80 80)"/>
                                    </a>
                                    @php $offset += $length; @endphp
                                @endif
                            @endforeach
                        @endif
                        <text x="80" y="82" text-anchor="middle" class="donut-total">{{ $donutTotal }}</text>
                        <text x="80" y="98" text-anchor="middle" class="donut-sub">Orders</text>
                    </svg>

                    <div class="legend">
                        @foreach($donutData as $slice)
                            <a href="{{ route('cashier.customer-orders', ['status' => $slice['key']]) }}" class="js-modal" data-title="{{ $slice['label'] }} Orders">
                                <span><span class="dot" style="background:{{ $slice['color'] }}"></span>{{ $slice['label'] }}</span>
                                <strong>{{ $slice['count'] }}</strong>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>


            <section class="panel">
                <div class="panel-head">
                    <h2>Recent Orders by Status</h2>
                    <a href="{{ route('cashier.customer-orders') }}" class="panel-link js-modal" data-title="Customer Orders">Open</a>
                </div>

                @if($statusCounts->count() > 0)
                    <div class="bar-list">
                        @foreach($statusCounts as $statusKey => $count)
                            <a href="{{ route('cashier.customer-orders', ['status' => $statusKey]) }}" class="bar-item js-modal" data-title="{{ $statusLabel($statusKey) }} Orders">
                                <div class="bar-top"><span>{{ $statusLabel($statusKey) }}</span><strong>{{ $count }}</strong></div>
                                <div class="bar-track"><div class="bar-fill" style="width: {{ ($count / $statusMax) * 100 }}%"></div></div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty">No orders to display.</div>
                @endif
            </section>


            <section class="panel">
                <div class="panel-head">
                    <h2>Releases by Product</h2>
                    <a href="#releases" class="panel-link js-modal" data-target="releases" data-title="Recent Product Releases">Open</a>
                </div>

                @if($releaseByProduct->count() > 0)
                    <div class="bar-list">
                        @foreach($releaseByProduct as $productName => $total)
                            <a href="#releases" class="bar-item js-modal" data-target="releases" data-title="Recent Product Releases" style="--bar-color:#7c3aed">
                                <div class="bar-top"><span>{{ $productName }}</span><strong>₱{{ number_format($total, 2) }}</strong></div>
                                <div class="bar-track"><div class="bar-fill" style="width: {{ ($total / $releaseMax) * 100 }}%"></div></div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty">No releases recorded yet.</div>
                @endif
            </section>

        </div>


        <div class="section-grid">

            <section class="panel">
                <div class="panel-head">
                    <h2>Recent Customer Orders</h2>
                    <a href="{{ route('cashier.customer-orders') }}" class="panel-link js-modal" data-title="Customer Orders">View all</a>
                </div>

                <div class="table-wrapper">
                    @if($customerOrders->count())
                        <table>
                            <thead><tr><th>Order No.</th><th>Customer</th><th>Date</th><th>Status</th></tr></thead>
                            <tbody>
                            @foreach($customerOrders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ optional($order->order_date)->format('M d, Y') }}</td>
                                    <td>
                                        @if($order->status === 'confirmed')
                                            <span class="status s-green">Confirmed</span>
                                        @elseif($order->status === 'fulfilled')
                                            <span class="status s-green">Fulfilled</span>
                                        @elseif($order->status === 'pending_inventory_check')
                                            <span class="status s-amber">Pending Check</span>
                                        @else
                                            <span class="status s-gray">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty">No customer orders to display.</div>
                    @endif
                </div>
            </section>


            <section class="panel">
                <div class="panel-head">
                    <h2>Orders Requiring Attention</h2>
                    <a href="{{ route('cashier.customer-orders', ['status' => 'pending_inventory_check']) }}" class="panel-link js-modal" data-title="Pending Customer Orders">View orders</a>
                </div>

                <div class="table-wrapper">
                    @if($pendingOrders->count())
                        <table>
                            <thead><tr><th>Order No.</th><th>Customer</th><th>Status</th></tr></thead>
                            <tbody>
                            @foreach($pendingOrders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td><span class="status s-amber">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty">No pending customer orders.</div>
                    @endif
                </div>
            </section>

        </div>


        <section class="panel" id="releases">
            <div class="panel-head">
                <h2>Recent Product Releases</h2>
            </div>

            <div class="table-wrapper">
                @if($recentReleases->count())
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th><th>Product</th><th>Customer</th><th>Receipt No.</th>
                                <th>Quantity</th><th>Amount</th><th>Processed By</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($recentReleases as $movement)
                            <tr>
                                <td>{{ optional($movement->transaction_date)->format('M d, Y') }}</td>
                                <td>{{ $movement->product->product_name ?? '—' }}</td>
                                <td>{{ $movement->supplier_customer ?? '—' }}</td>
                                <td>{{ $movement->receipt_number ?? '—' }}</td>
                                <td>{{ abs((int) $movement->quantity) }}</td>
                                <td class="amount">₱{{ number_format((float) ($movement->amount ?? 0), 2) }}</td>
                                <td>{{ $movement->user->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">No product releases recorded yet.</div>
                @endif
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

    /* Keep the popup view-only: remove forms, buttons and the Action column */
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