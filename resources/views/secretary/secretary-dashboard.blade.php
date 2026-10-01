<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Secretary Dashboard - DPAM IMS</title>

    <style>
        :root { --bg:#f1f5f9; --ink:#0f172a; --muted:#64748b; --line:#e2e8f0; --sidebar:#0b1220; --accent:#2563eb; --accent-dark:#1d4ed8; --danger:#dc2626; --success:#059669; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:"Segoe UI", system-ui, -apple-system, Roboto, Arial, sans-serif; background:var(--bg); color:var(--ink); }
        a { color:inherit; text-decoration:none; }
        button, input, select, textarea { font-family:inherit; }
        .layout { display:flex; min-height:100vh; }

        /* SIDEBAR */
        .sidebar { width:256px; flex-shrink:0; background:var(--sidebar); color:#fff; padding:24px 14px; display:flex; flex-direction:column; position:sticky; top:0; height:100vh; overflow-y:auto; }
        .brand { font-size:20px; font-weight:700; padding:0 12px; }
        .subtitle { font-size:12px; color:#94a3b8; line-height:1.5; padding:0 12px; margin:4px 0 26px; }
        .menu-title { font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:.08em; margin:6px 12px 8px; }
        .menu a, .logout-button { display:flex; align-items:center; gap:11px; width:100%; padding:11px 12px; margin-bottom:4px; border-radius:8px; color:#cbd5e1; background:transparent; border:none; font-size:14px; cursor:pointer; text-align:left; transition:background .15s, color .15s; }
        .menu a svg, .logout-button svg { width:18px; height:18px; flex-shrink:0; }
        .menu a:hover, .logout-button:hover { background:#1e293b; color:#fff; }
        .menu a.active { background:var(--accent); color:#fff; font-weight:600; }
        .logout-form { margin-top:auto; padding-top:24px; }

        /* MAIN */
        .main { flex:1; min-width:0; padding:30px 34px; }
        .topbar { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:26px; }
        .page-title { margin:0; font-size:26px; font-weight:700; }
        .page-description { margin-top:5px; color:var(--muted); font-size:14px; }
        .top-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .user-chip { font-size:13px; font-weight:600; }
        .role-badge { background:#dbeafe; color:#1e40af; padding:7px 14px; border-radius:999px; font-size:12px; font-weight:700; letter-spacing:.05em; }

        /* CARDS */
        .cards { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:22px; }
        .cards.cols-2 { grid-template-columns:repeat(2,1fr); }
        .cards.cols-3 { grid-template-columns:repeat(3,1fr); }
        .card { display:block; background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; position:relative; overflow:hidden; transition:transform .15s, box-shadow .15s, border-color .15s; }
        .card::before { content:""; position:absolute; left:0; top:0; bottom:0; width:4px; background:var(--card-color, var(--accent)); }
        a.card { cursor:pointer; }
        a.card:hover { transform:translateY(-3px); box-shadow:0 10px 24px rgba(15,23,42,.09); border-color:#cbd5e1; }
        .card-label { font-size:13px; color:var(--muted); margin-bottom:10px; }
        .card-value { font-size:30px; font-weight:700; }
        .card-link { margin-top:10px; font-size:12px; font-weight:600; color:var(--card-color, var(--accent)); }

        /* PANELS */
        .panel { background:#fff; border:1px solid var(--line); border-radius:14px; padding:22px; }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
        .panel h2 { margin:0; font-size:17px; }
        .panel-sub { margin-top:4px; font-size:12px; color:var(--muted); }
        .panel-link { font-size:13px; font-weight:600; color:var(--accent); cursor:pointer; }
        .panel-link:hover { text-decoration:underline; }
        .table-panel { padding:0; overflow:hidden; }
        .table-panel > .panel-head { padding:18px 22px; margin:0; border-bottom:1px solid var(--line); }
        .chart-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; margin-bottom:22px; }
        .two-col { display:grid; grid-template-columns:1fr 1fr; gap:18px; }

        /* DONUT */
        .donut-wrap { display:flex; flex-direction:column; align-items:center; gap:16px; }
        .donut { width:170px; height:170px; }
        .donut a circle { transition:stroke-width .15s; cursor:pointer; }
        .donut a:hover circle { stroke-width:26; }
        .donut-total { font-size:26px; font-weight:700; fill:var(--ink); }
        .donut-sub { font-size:11px; fill:var(--muted); }
        .legend { width:100%; display:flex; flex-direction:column; gap:6px; }
        .legend a { display:flex; align-items:center; justify-content:space-between; padding:8px 10px; border-radius:8px; font-size:13px; cursor:pointer; transition:background .15s; }
        .legend a:hover { background:#f1f5f9; }
        .legend .dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:8px; }

        /* BARS */
        .bar-list { display:flex; flex-direction:column; gap:10px; }
        .bar-item { display:block; padding:10px 12px; border-radius:10px; cursor:pointer; transition:background .15s; }
        .bar-item:hover { background:#f1f5f9; }
        .bar-top { display:flex; justify-content:space-between; gap:10px; font-size:13px; margin-bottom:7px; }
        .bar-track { height:10px; background:#e2e8f0; border-radius:999px; overflow:hidden; }
        .bar-fill { height:100%; border-radius:999px; background:var(--bar-color, var(--accent)); min-width:3px; }

        /* TABLE */
        .table-wrapper { overflow-x:auto; }
        .data-table { width:100%; border-collapse:collapse; min-width:520px; }
        .data-table th, .data-table td { padding:12px 16px; border-bottom:1px solid var(--line); text-align:left; font-size:13px; vertical-align:middle; }
        .data-table th { color:var(--muted); font-weight:600; background:#f8fafc; white-space:nowrap; }
        .data-table tbody tr:hover { background:#f8fafc; }
        .data-table tr:last-child td { border-bottom:none; }
        .text-right { text-align:right !important; }
        .strong { font-weight:600; }
        .pill { display:inline-block; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:700; white-space:nowrap; }
        .pill-green { background:#dcfce7; color:#166534; }
        .pill-amber { background:#fef3c7; color:#92400e; }
        .pill-red { background:#fee2e2; color:#991b1b; }
        .pill-blue { background:#dbeafe; color:#1e40af; }
        .pill-gray { background:#e5e7eb; color:#374151; }
        .pill-indigo { background:#e0e7ff; color:#3730a3; }
        .empty-state { color:var(--muted); font-size:14px; padding:40px 20px; text-align:center; }

        /* BUTTONS */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; border:none; border-radius:8px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s; }
        .btn-primary { background:var(--accent); color:#fff; }
        .btn-primary:hover { background:var(--accent-dark); }
        .btn-success { background:var(--success); color:#fff; }
        .btn-danger { background:var(--danger); color:#fff; }
        .btn-ghost { background:#e2e8f0; color:#334155; }
        .btn-ghost:hover { background:#cbd5e1; }
        .btn-dark { background:#0f172a; color:#fff; }

        /* MODAL */
        .modal { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); align-items:center; justify-content:center; padding:20px; z-index:1000; }
        .modal.show { display:flex; }
        .modal-box { background:#fff; width:100%; max-width:1000px; max-height:90vh; display:flex; flex-direction:column; border-radius:16px; box-shadow:0 25px 60px rgba(0,0,0,.3); overflow:hidden; }
        .modal-header { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:18px 22px; border-bottom:1px solid var(--line); }
        .modal-header h2 { margin:0; font-size:18px; }
        .modal-actions { display:flex; align-items:center; gap:10px; }
        .full-link { font-size:13px; font-weight:600; color:var(--accent); padding:7px 12px; border-radius:8px; background:#eff6ff; }
        .full-link:hover { background:#dbeafe; }
        .close-button { border:none; background:transparent; font-size:26px; line-height:1; cursor:pointer; color:var(--muted); border-radius:6px; width:34px; height:34px; }
        .close-button:hover { background:#f1f5f9; color:var(--ink); }
        .modal-body { padding:0; overflow:auto; }
        .modal-loading { padding:50px; text-align:center; color:var(--muted); font-size:14px; }
        .modal-body .table-panel { border:none; border-radius:0; }

        /* RESPONSIVE */
        @media (max-width:1200px) { .chart-grid { grid-template-columns:1fr 1fr; } .chart-grid .panel:last-child { grid-column:1 / -1; } }
        @media (max-width:1000px) { .cards, .cards.cols-3 { grid-template-columns:repeat(2,1fr); } .two-col { grid-template-columns:1fr; } }
        @media (max-width:800px) { .layout { flex-direction:column; } .sidebar { width:100%; height:auto; position:static; } .main { padding:20px; } .chart-grid { grid-template-columns:1fr; } .topbar { flex-direction:column; align-items:flex-start; } }
        @media (max-width:560px) { .cards, .cards.cols-2, .cards.cols-3 { grid-template-columns:1fr; } }
    </style>
</head>

<body>

@php
    $total   = (int) $totalProducts;
    $low     = (int) $lowStockCount;
    $out     = (int) $outOfStockCount;
    $lowOnly = max($low - $out, 0);
    $healthy = max($total - $low, 0);

    $lowReport = route('secretary.reports', ['report' => 'low-stock']);

    $health = [
        ['label' => 'In Stock',     'count' => $healthy, 'color' => '#10b981', 'url' => route('secretary.products'), 'panel' => '#panel-products', 'filter' => 'Available'],
        ['label' => 'Low Stock',    'count' => $lowOnly, 'color' => '#f59e0b', 'url' => $lowReport, 'panel' => '#report-panel', 'filter' => 'Low Stock'],
        ['label' => 'Out of Stock', 'count' => $out,     'color' => '#dc2626', 'url' => $lowReport, 'panel' => '#report-panel', 'filter' => 'Out of Stock'],
    ];

    $healthTotal   = array_sum(array_column($health, 'count'));
    $radius        = 60;
    $circumference = 2 * M_PI * $radius;
    $offset        = 0;

    $moveCounts = $recentMovements->groupBy('movement_type')->map->count();

    $activity = [
        ['label' => 'Stock In',             'count' => (int) ($moveCounts['stock_in'] ?? 0),    'color' => '#059669', 'url' => route('secretary.inventory'), 'panel' => '#panel-movements'],
        ['label' => 'Stock Out',            'count' => (int) ($moveCounts['stock_out'] ?? 0),   'color' => '#dc2626', 'url' => route('secretary.inventory'), 'panel' => '#panel-movements'],
        ['label' => 'Adjustment',           'count' => (int) ($moveCounts['adjustment'] ?? 0),  'color' => '#f59e0b', 'url' => route('secretary.inventory'), 'panel' => '#panel-movements'],
        ['label' => 'Pending Order Checks', 'count' => (int) $pendingInventoryChecks,           'color' => '#2563eb', 'url' => route('secretary.customer-orders', ['status' => 'pending_inventory_check']), 'panel' => '#panel-orders'],
    ];
    $activityMax = max(max(array_column($activity, 'count')), 1);

    $lowList = $lowStockProducts->take(6);
    $lowMax  = max((int) ($lowList->max('current_stock') ?? 1), 1);
@endphp

<div class="layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="brand">DPAM IMS</div>
        <div class="subtitle">Industrial Supplies &amp; Services Inc.</div>

        <div class="menu-title">Secretary</div>

        <nav class="menu">
            <a href="{{ route('secretary.dashboard') }}" class="active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                Dashboard
            </a>
            <a href="{{ route('secretary.products') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
                Products
            </a>
            <a href="{{ route('secretary.inventory') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4z"/><path d="M3 12l9 4 9-4M3 17l9 4 9-4"/></svg>
                Inventory
            </a>
            <a href="{{ route('secretary.customer-orders') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4h6v3H9zM9 12h6M9 16h6"/></svg>
                Customer Orders
            </a>
            <a href="{{ route('secretary.stock-card') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l4 4v14H6z"/><path d="M9 12h7M9 16h7M9 8h3"/></svg>
                Stock Card
            </a>
            <a href="{{ route('secretary.reports') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>
                Reports
            </a>
            <a href="{{ route('secretary.archive') }}">
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


    {{-- MAIN --}}
    <main class="main">

        <div class="topbar">
            <div>
                <h1 class="page-title">Secretary Dashboard</h1>
                <div class="page-description">Welcome, {{ auth()->user()->name }}</div>
            </div>
            <div class="role-badge">SECRETARY</div>
        </div>


        {{-- KPI CARDS --}}
        <div class="cards">

            <a href="{{ route('secretary.products') }}" class="card js-modal" data-title="Active Products" data-panel="#panel-products" style="--card-color:#2563eb">
                <div class="card-label">Total Products</div>
                <div class="card-value">{{ $totalProducts }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="{{ route('secretary.inventory') }}" class="card js-modal" data-title="Current Inventory" data-panel="#panel-inventory" style="--card-color:#059669">
                <div class="card-label">Current Inventory</div>
                <div class="card-value">{{ number_format($totalInventoryQuantity) }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="{{ $lowReport }}" class="card js-modal" data-title="Low-Stock Items" data-panel="#report-panel" style="--card-color:#f59e0b">
                <div class="card-label">Low Stock Items</div>
                <div class="card-value">{{ $lowStockCount }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="{{ $lowReport }}" class="card js-modal" data-title="Out-of-Stock Items" data-panel="#report-panel" data-filter="Out of Stock" style="--card-color:#dc2626">
                <div class="card-label">Out of Stock</div>
                <div class="card-value">{{ $outOfStockCount }}</div>
                <div class="card-link">View details</div>
            </a>

        </div>


        {{-- GRAPHS --}}
        <div class="chart-grid">

            {{-- Stock health --}}
            <section class="panel">

                <div class="panel-head">
                    <h2>Stock Health</h2>
                    <a href="{{ route('secretary.products') }}" class="panel-link js-modal" data-title="Active Products" data-panel="#panel-products">Open</a>
                </div>

                <div class="donut-wrap">

                    <svg class="donut" viewBox="0 0 160 160">

                        <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="#e2e8f0" stroke-width="22"/>

                        @if($healthTotal > 0)
                            @foreach($health as $slice)
                                @if($slice['count'] > 0)
                                    @php $length = ($slice['count'] / $healthTotal) * $circumference; @endphp
                                    <a href="{{ $slice['url'] }}" class="js-modal" data-title="{{ $slice['label'] }}" data-panel="{{ $slice['panel'] }}" data-filter="{{ $slice['filter'] }}">
                                        <title>{{ $slice['label'] }}: {{ $slice['count'] }}</title>
                                        <circle
                                            cx="80" cy="80" r="{{ $radius }}"
                                            fill="none"
                                            stroke="{{ $slice['color'] }}"
                                            stroke-width="22"
                                            stroke-dasharray="{{ $length }} {{ $circumference - $length }}"
                                            stroke-dashoffset="{{ -$offset }}"
                                            transform="rotate(-90 80 80)"
                                        />
                                    </a>
                                    @php $offset += $length; @endphp
                                @endif
                            @endforeach
                        @endif

                        <text x="80" y="82" text-anchor="middle" class="donut-total">{{ $total }}</text>
                        <text x="80" y="98" text-anchor="middle" class="donut-sub">Products</text>

                    </svg>

                    <div class="legend">
                        @foreach($health as $slice)
                            <a href="{{ $slice['url'] }}" class="js-modal" data-title="{{ $slice['label'] }}" data-panel="{{ $slice['panel'] }}" data-filter="{{ $slice['filter'] }}">
                                <span><span class="dot" style="background:{{ $slice['color'] }}"></span>{{ $slice['label'] }}</span>
                                <strong>{{ $slice['count'] }}</strong>
                            </a>
                        @endforeach
                    </div>

                </div>

            </section>


            {{-- Lowest stock --}}
            <section class="panel">

                <div class="panel-head">
                    <h2>Lowest Stock Levels</h2>
                    <a href="{{ $lowReport }}" class="panel-link js-modal" data-title="Low-Stock Items" data-panel="#report-panel">Open</a>
                </div>

                @if($lowList->count() > 0)

                    <div class="bar-list">
                        @foreach($lowList as $item)
                            <a href="{{ $lowReport }}" class="bar-item js-modal" data-title="Low-Stock Items" data-panel="#report-panel" style="--bar-color:{{ $item->current_stock <= 0 ? '#dc2626' : '#f59e0b' }}">
                                <div class="bar-top"><span>{{ $item->product_name }}</span><strong>{{ number_format($item->current_stock) }}</strong></div>
                                <div class="bar-track">
                                    <div class="bar-fill" style="width: {{ max(0, ($item->current_stock / $lowMax) * 100) }}%"></div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                @else

                    <div class="empty-state">No low-stock products.</div>

                @endif

            </section>


            {{-- Activity --}}
            <section class="panel">

                <div class="panel-head">
                    <h2>Activity Overview</h2>
                    <a href="{{ route('secretary.inventory') }}" class="panel-link js-modal" data-title="Inventory Movement History" data-panel="#panel-movements">Open</a>
                </div>

                <div class="bar-list">
                    @foreach($activity as $row)
                        <a href="{{ $row['url'] }}" class="bar-item js-modal" data-title="{{ $row['label'] }}" data-panel="{{ $row['panel'] }}" style="--bar-color:{{ $row['color'] }}">
                            <div class="bar-top"><span>{{ $row['label'] }}</span><strong>{{ $row['count'] }}</strong></div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: {{ ($row['count'] / $activityMax) * 100 }}%"></div>
                            </div>
                        </a>
                    @endforeach
                </div>

            </section>

        </div>


        {{-- TABLES --}}
        <div class="two-col">

            <section class="panel table-panel">

                <div class="panel-head">
                    <h2>Low-Stock Items</h2>
                    <a href="{{ $lowReport }}" class="panel-link js-modal" data-title="Low-Stock Items" data-panel="#report-panel">View all</a>
                </div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-right">Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $item)
                                <tr>
                                    <td class="strong">{{ $item->product_name }}</td>
                                    <td class="text-right">{{ number_format($item->current_stock) }}</td>
                                    <td>
                                        @if($item->current_stock <= 0)
                                            <span class="pill pill-red">Out of Stock</span>
                                        @else
                                            <span class="pill pill-amber">Low Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="empty-state">No low-stock products.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </section>


            <section class="panel table-panel">

                <div class="panel-head">
                    <h2>Recent Stock Movements</h2>
                    <a href="{{ route('secretary.inventory') }}" class="panel-link js-modal" data-title="Inventory Movement History" data-panel="#panel-movements">View all</a>
                </div>

                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Type</th>
                                <th class="text-right">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMovements as $movement)
                                <tr>
                                    <td class="strong">{{ $movement->product?->product_name ?? '—' }}</td>
                                    <td>
                                        @if($movement->movement_type === 'stock_in')
                                            <span class="pill pill-green">Stock In</span>
                                        @elseif($movement->movement_type === 'stock_out')
                                            <span class="pill pill-red">Stock Out</span>
                                        @else
                                            <span class="pill pill-amber">Adjustment</span>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ number_format(abs($movement->quantity)) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="empty-state">No inventory movements recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </section>

        </div>

    </main>

</div>


{{-- DATA POPUP MODAL --}}
<div id="dataModal" class="modal">

    <div class="modal-box">

        <div class="modal-header">

            <h2 id="dataModalTitle">Details</h2>

            <div class="modal-actions">
                <a href="#" id="dataModalFull" class="full-link">Open full page</a>
                <button type="button" class="close-button" onclick="closeDataModal()" aria-label="Close">&times;</button>
            </div>

        </div>

        <div id="dataModalBody" class="modal-body"></div>

    </div>

</div>


<script>

    const dataModal = document.getElementById('dataModal');
    const dataModalTitle = document.getElementById('dataModalTitle');
    const dataModalBody = document.getElementById('dataModalBody');
    const dataModalFull = document.getElementById('dataModalFull');


    /* Make the popup view-only: remove forms, buttons and the Action column */
    function cleanModalContent(panel)
    {
        panel.querySelectorAll('script, form, button').forEach(function(el) { el.remove(); });

        panel.querySelectorAll('table').forEach(function(table)
        {
            const headers = Array.from(table.querySelectorAll('thead th'));

            const actionIndex = headers.findIndex(function(th)
            {
                const text = th.textContent.trim().toLowerCase();
                return text === 'actions' || text === 'action';
            });

            if (actionIndex === -1) return;

            table.querySelectorAll('tr').forEach(function(row)
            {
                const cell = row.children[actionIndex];
                if (cell) cell.remove();
            });
        });
    }


    /* Keep only rows whose status pill matches the clicked card or graph */
    function filterModalRows(panel, filter)
    {
        panel.querySelectorAll('tbody tr').forEach(function(row)
        {
            const pill = row.querySelector('.pill');

            if (!pill || pill.textContent.trim() !== filter) row.remove();
        });

        if (!panel.querySelector('tbody tr'))
        {
            panel.querySelectorAll('.table-wrapper').forEach(function(el) { el.remove(); });
            panel.insertAdjacentHTML('beforeend', '<div class="empty-state">No matching records.</div>');
        }
    }


    async function loadModalData(url, title, selector, filter)
    {
        dataModalTitle.textContent = title || 'Details';
        dataModalFull.href = url;
        dataModalBody.innerHTML = '<div class="modal-loading">Loading...</div>';
        dataModal.classList.add('show');

        try
        {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Request failed');

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const panel = doc.querySelector(selector || '.table-panel');

            if (!panel) throw new Error('No data');

            cleanModalContent(panel);

            if (filter) filterModalRows(panel, filter);

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


    document.addEventListener('click', function(event)
    {
        const trigger = event.target.closest('.js-modal');

        if (trigger && !dataModal.contains(trigger))
        {
            event.preventDefault();

            loadModalData(
                trigger.getAttribute('href'),
                trigger.getAttribute('data-title'),
                trigger.getAttribute('data-panel'),
                trigger.getAttribute('data-filter')
            );

            return;
        }

        if (event.target === dataModal) closeDataModal();
    });


    document.addEventListener('keydown', function(event)
    {
        if (event.key === 'Escape' && dataModal.classList.contains('show')) closeDataModal();
    });

</script>

</body>

</html>