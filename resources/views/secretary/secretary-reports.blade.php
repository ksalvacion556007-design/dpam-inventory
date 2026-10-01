<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Secretary Reports - DPAM IMS</title>

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

        /* TABS */
        .tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:22px; }
        .tabs a { padding:9px 15px; border-radius:8px; background:#fff; border:1px solid var(--line); font-size:13px; font-weight:600; color:#475569; }
        .tabs a:hover { border-color:var(--accent); color:var(--accent); }
        .tabs a.active { background:var(--accent); border-color:var(--accent); color:#fff; }

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
        .panel { background:#fff; border:1px solid var(--line); border-radius:14px; padding:22px; margin-bottom:22px; }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
        .panel h2 { margin:0; font-size:17px; }
        .panel-sub { margin-top:4px; font-size:12px; color:var(--muted); }
        .table-panel { padding:0; overflow:hidden; }
        .table-panel > .panel-head { padding:18px 22px; margin:0; border-bottom:1px solid var(--line); }

        /* FILTER */
        .filter-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:14px; align-items:end; }
        .form-group label { display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:6px; }
        .form-control { width:100%; height:42px; border:1px solid #cbd5e1; border-radius:8px; padding:0 12px; background:#fff; font-size:13px; color:var(--ink); }
        .form-control:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(37,99,235,.12); }
        .filter-actions { display:flex; gap:8px; }

        /* TABLE */
        .table-wrapper { overflow-x:auto; }
        .data-table { width:100%; border-collapse:collapse; min-width:850px; }
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
        .empty-state { color:var(--muted); font-size:14px; padding:40px 20px; text-align:center; }

        /* BUTTONS */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; border:none; border-radius:8px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s; }
        .btn-primary { background:var(--accent); color:#fff; }
        .btn-primary:hover { background:var(--accent-dark); }
        .btn-ghost { background:#e2e8f0; color:#334155; }
        .btn-ghost:hover { background:#cbd5e1; }
        .btn-dark { background:#0f172a; color:#fff; }

        /* MODAL */
        .modal { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); align-items:center; justify-content:center; padding:20px; z-index:1000; }
        .modal.show { display:flex; }
        .modal-box { background:#fff; width:100%; max-width:1000px; max-height:90vh; display:flex; flex-direction:column; border-radius:16px; box-shadow:0 25px 60px rgba(0,0,0,.3); overflow:hidden; }
        .modal-header { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:18px 22px; border-bottom:1px solid var(--line); }
        .modal-header h2 { margin:0; font-size:18px; }
        .close-button { border:none; background:transparent; font-size:26px; line-height:1; cursor:pointer; color:var(--muted); border-radius:6px; width:34px; height:34px; }
        .close-button:hover { background:#f1f5f9; color:var(--ink); }
        .modal-body { padding:0; overflow:auto; }
        .modal-body .table-panel { border:none; border-radius:0; margin:0; }

        /* PRINT */
        .print-only { display:none; }

        @media print {
            @page { size:landscape; margin:12mm; }
            body { background:#fff; }
            .sidebar, .no-print, .modal { display:none !important; }
            .layout { display:block; }
            .main { padding:0; }
            .print-only { display:block; margin-bottom:14px; }
            .print-only h1 { margin:0; font-size:20px; }
            .print-only p { margin:4px 0 0; font-size:12px; color:#475569; }
            .cards { gap:10px; margin-bottom:12px; }
            .card { border:1px solid #cbd5e1; border-radius:0; padding:10px; }
            .card-value { font-size:20px; }
            .card-link { display:none; }
            .panel { border:1px solid #cbd5e1; border-radius:0; }
            .data-table { min-width:0; }
            .data-table th, .data-table td { font-size:10px; padding:6px; }
            .table-wrapper { overflow:visible; }
            .pill, .card::before, .data-table th { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        }

        /* RESPONSIVE */
        @media (max-width:1000px) { .cards, .cards.cols-3 { grid-template-columns:repeat(2,1fr); } }
        @media (max-width:800px) { .layout { flex-direction:column; } .sidebar { width:100%; height:auto; position:static; } .main { padding:20px; } .topbar { flex-direction:column; align-items:flex-start; } }
        @media (max-width:560px) { .cards, .cards.cols-2, .cards.cols-3 { grid-template-columns:1fr; } }
    </style>
</head>

<body>

@php
    $reportTitles = [
        'inventory-summary' => ['Inventory Summary Report', 'Current inventory status of active products'],
        'stock-movement'    => ['Stock Movement Report', 'Inventory movements within the selected period'],
        'stock-in'          => ['Stock In Report', 'Products received from suppliers'],
        'stock-out'         => ['Stock Out Report', 'Products released or delivered to customers'],
        'low-stock'         => ['Low Stock / Reorder Report', 'Products at or below their reorder level'],
        'customer-orders'   => ['Customer Order Inventory Report', 'Customer orders and their inventory-check results'],
        'adjustments'       => ['Inventory Adjustment Report', 'Physical inventory adjustments and discrepancies'],
    ];

    $reportTitle = $reportTitles[$report][0] ?? 'Report';
    $reportSub   = $reportTitles[$report][1] ?? '';
@endphp

<div class="layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="brand">DPAM IMS</div>
        <div class="subtitle">Industrial Supplies &amp; Services Inc.</div>

        <div class="menu-title">Secretary</div>

        <nav class="menu">
            <a href="{{ route('secretary.dashboard') }}">
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
            <a href="{{ route('secretary.reports') }}" class="active">
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

        <div class="topbar no-print">
            <div>
                <h1 class="page-title">Reports</h1>
                <div class="page-description">Inventory monitoring and operational reports</div>
            </div>
            <div class="top-actions">
                <button type="button" class="btn btn-dark" onclick="window.print()">Print PDF</button>
                <span class="user-chip">{{ auth()->user()->name }}</span>
                <span class="role-badge">SECRETARY</span>
            </div>
        </div>

        <div class="print-only">
            <h1>DPAM IMS - {{ $reportTitle }}</h1>
            <p>
                @if(!empty($dateFrom) || !empty($dateTo))
                    Period: {{ $dateFrom ?: 'Start' }} to {{ $dateTo ?: 'Present' }} |
                @endif
                Printed {{ now()->format('M d, Y h:i A') }} by {{ auth()->user()->name }}
            </p>
        </div>


        {{-- REPORT TABS --}}
        <div class="tabs no-print">
            <a href="{{ route('secretary.reports', ['report' => 'inventory-summary']) }}" class="{{ $report === 'inventory-summary' ? 'active' : '' }}">Inventory Summary</a>
            <a href="{{ route('secretary.reports', ['report' => 'stock-movement']) }}" class="{{ $report === 'stock-movement' ? 'active' : '' }}">Stock Movement</a>
            <a href="{{ route('secretary.reports', ['report' => 'stock-in']) }}" class="{{ $report === 'stock-in' ? 'active' : '' }}">Stock In</a>
            <a href="{{ route('secretary.reports', ['report' => 'stock-out']) }}" class="{{ $report === 'stock-out' ? 'active' : '' }}">Stock Out</a>
            <a href="{{ route('secretary.reports', ['report' => 'low-stock']) }}" class="{{ $report === 'low-stock' ? 'active' : '' }}">Low Stock / Reorder</a>
            <a href="{{ route('secretary.reports', ['report' => 'customer-orders']) }}" class="{{ $report === 'customer-orders' ? 'active' : '' }}">Customer Orders</a>
            <a href="{{ route('secretary.reports', ['report' => 'adjustments']) }}" class="{{ $report === 'adjustments' ? 'active' : '' }}">Adjustments</a>
        </div>


        {{-- FILTERS --}}
        @if($report !== 'inventory-summary' && $report !== 'low-stock')

            <section class="panel no-print">

                <form method="GET" action="{{ route('secretary.reports') }}">

                    <input type="hidden" name="report" value="{{ $report }}">

                    <div class="filter-grid">

                        <div class="form-group">
                            <label for="date_from">{{ $report === 'customer-orders' ? 'Order Date From' : 'From Date' }}</label>
                            <input type="date" id="date_from" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>

                        <div class="form-group">
                            <label for="date_to">{{ $report === 'customer-orders' ? 'Order Date To' : 'To Date' }}</label>
                            <input type="date" id="date_to" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>

                        @if($report !== 'customer-orders')

                            <div class="form-group">
                                <label for="product_id">Product</label>
                                <select name="product_id" id="product_id" class="form-control">
                                    <option value="">All Products</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ (string) $productId === (string) $product->id ? 'selected' : '' }}>
                                            {{ $product->product_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        @endif

                        @if($report === 'stock-movement')

                            <div class="form-group">
                                <label for="movement_type">Movement Type</label>
                                <select name="movement_type" id="movement_type" class="form-control">
                                    <option value="">All Movements</option>
                                    <option value="stock_in" {{ $movementType === 'stock_in' ? 'selected' : '' }}>Stock In</option>
                                    <option value="stock_out" {{ $movementType === 'stock_out' ? 'selected' : '' }}>Stock Out</option>
                                    <option value="adjustment" {{ $movementType === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                </select>
                            </div>

                        @elseif($report === 'customer-orders')

                            <div class="form-group">
                                <label for="inventory_check_status">Inventory Check</label>
                                <select name="inventory_check_status" id="inventory_check_status" class="form-control">
                                    <option value="">All Results</option>
                                    <option value="pending" {{ $inventoryCheckStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="available" {{ $inventoryCheckStatus === 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="insufficient" {{ $inventoryCheckStatus === 'insufficient' ? 'selected' : '' }}>Insufficient</option>
                                </select>
                            </div>

                        @endif

                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">Generate Report</button>
                            <a href="{{ route('secretary.reports', ['report' => $report]) }}" class="btn btn-ghost">Clear</a>
                        </div>

                    </div>

                </form>

            </section>

        @endif


        {{-- ===================== INVENTORY SUMMARY ===================== --}}
        @if($report === 'inventory-summary')

            @php
                $totalProducts = $inventorySummary->count();

                $totalStock = $inventorySummary->sum(function ($product) {
                    return $product->inventory ? (int) $product->inventory->current_stock : 0;
                });

                $lowStockCount = $inventorySummary->filter(function ($product) {
                    $stock = $product->inventory ? (int) $product->inventory->current_stock : 0;
                    return $stock > 0 && $stock <= (int) $product->reorder_level;
                })->count();

                $outOfStockCount = $inventorySummary->filter(function ($product) {
                    $stock = $product->inventory ? (int) $product->inventory->current_stock : 0;
                    return $stock <= 0;
                })->count();
            @endphp

            <div class="cards">

                <a href="#" class="card js-report-card" data-title="Active Products" data-filter="" style="--card-color:#2563eb">
                    <div class="card-label">Active Products</div>
                    <div class="card-value">{{ $totalProducts }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Stock On Hand by Product" data-filter="" style="--card-color:#059669">
                    <div class="card-label">Total Stock</div>
                    <div class="card-value">{{ number_format($totalStock) }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Low-Stock Products" data-filter="Low Stock" style="--card-color:#f59e0b">
                    <div class="card-label">Low Stock</div>
                    <div class="card-value">{{ $lowStockCount }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Out-of-Stock Products" data-filter="Out of Stock" style="--card-color:#dc2626">
                    <div class="card-label">Out of Stock</div>
                    <div class="card-value">{{ $outOfStockCount }}</div>
                    <div class="card-link">View details</div>
                </a>

            </div>

            <section class="panel table-panel" id="report-panel">

                <div class="panel-head">
                    <div>
                        <h2>{{ $reportTitle }}</h2>
                        <div class="panel-sub">{{ $reportSub }}</div>
                    </div>
                </div>

                @if($inventorySummary->count())

                    <div class="table-wrapper">
                        <table class="data-table">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th class="text-right">Current Stock</th>
                                    <th class="text-right">Reorder Level</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($inventorySummary as $index => $product)

                                    @php
                                        $stock = $product->inventory ? (int) $product->inventory->current_stock : 0;
                                        $reorderLevel = (int) $product->reorder_level;

                                        if ($stock <= 0) {
                                            $stockStatus = 'Out of Stock';
                                            $statusClass = 'pill-red';
                                        } elseif ($stock <= $reorderLevel) {
                                            $stockStatus = 'Low Stock';
                                            $statusClass = 'pill-amber';
                                        } else {
                                            $stockStatus = 'In Stock';
                                            $statusClass = 'pill-green';
                                        }
                                    @endphp

                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="strong">{{ $product->product_name }}</td>
                                        <td>{{ $product->category->category_name ?? '—' }}</td>
                                        <td>{{ $product->unit }}</td>
                                        <td class="text-right">{{ number_format($stock) }}</td>
                                        <td class="text-right">{{ number_format($reorderLevel) }}</td>
                                        <td><span class="pill {{ $statusClass }}">{{ $stockStatus }}</span></td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="empty-state">No inventory records found.</div>

                @endif

            </section>

        @endif


        {{-- ===================== STOCK MOVEMENT ===================== --}}
        @if($report === 'stock-movement')

            <div class="cards">

                <a href="#" class="card js-report-card" data-title="All Movements" data-filter="" style="--card-color:#2563eb">
                    <div class="card-label">Transactions</div>
                    <div class="card-value">{{ $stockMovements->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Stock In Movements" data-filter="Stock In" style="--card-color:#059669">
                    <div class="card-label">Stock In</div>
                    <div class="card-value">{{ $stockMovements->where('movement_type', 'stock_in')->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Stock Out Movements" data-filter="Stock Out" style="--card-color:#dc2626">
                    <div class="card-label">Stock Out</div>
                    <div class="card-value">{{ $stockMovements->where('movement_type', 'stock_out')->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Adjustments" data-filter="Adjustment" style="--card-color:#f59e0b">
                    <div class="card-label">Adjustments</div>
                    <div class="card-value">{{ $stockMovements->where('movement_type', 'adjustment')->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

            </div>

            <section class="panel table-panel" id="report-panel">

                <div class="panel-head">
                    <div>
                        <h2>{{ $reportTitle }}</h2>
                        <div class="panel-sub">{{ $reportSub }}</div>
                    </div>
                </div>

                @if($stockMovements->count())

                    <div class="table-wrapper">
                        <table class="data-table">

                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Movement</th>
                                    <th class="text-right">Quantity</th>
                                    <th class="text-right">Stock Before</th>
                                    <th class="text-right">Stock After</th>
                                    <th>Supplier / Customer</th>
                                    <th>Reference</th>
                                    <th>Performed By</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($stockMovements as $movement)

                                    <tr>
                                        <td>{{ $movement->transaction_date?->format('M d, Y') }}</td>
                                        <td class="strong">{{ $movement->product->product_name ?? '—' }}</td>
                                        <td>
                                            @if($movement->movement_type === 'stock_in')
                                                <span class="pill pill-green">Stock In</span>
                                            @elseif($movement->movement_type === 'stock_out')
                                                <span class="pill pill-blue">Stock Out</span>
                                            @else
                                                <span class="pill pill-gray">Adjustment</span>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}</td>
                                        <td class="text-right">{{ number_format($movement->stock_before) }}</td>
                                        <td class="text-right">{{ number_format($movement->stock_after) }}</td>
                                        <td>{{ $movement->supplier_customer ?? '—' }}</td>
                                        <td>{{ $movement->reference ?? $movement->receipt_number ?? $movement->customer_order_reference ?? '—' }}</td>
                                        <td>{{ $movement->user->name ?? '—' }}</td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="empty-state">No stock movement records found for the selected filters.</div>

                @endif

            </section>

        @endif


        {{-- ===================== STOCK IN ===================== --}}
        @if($report === 'stock-in')

            @php
                $totalReceived = $stockIns->sum(function ($movement) {
                    return abs((int) $movement->quantity);
                });
            @endphp

            <div class="cards cols-2">

                <a href="#" class="card js-report-card" data-title="Stock In Transactions" data-filter="" style="--card-color:#2563eb">
                    <div class="card-label">Stock In Transactions</div>
                    <div class="card-value">{{ $stockIns->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Quantity Received" data-filter="" style="--card-color:#059669">
                    <div class="card-label">Total Quantity Received</div>
                    <div class="card-value">{{ number_format($totalReceived) }}</div>
                    <div class="card-link">View details</div>
                </a>

            </div>

            <section class="panel table-panel" id="report-panel">

                <div class="panel-head">
                    <div>
                        <h2>{{ $reportTitle }}</h2>
                        <div class="panel-sub">{{ $reportSub }}</div>
                    </div>
                </div>

                @if($stockIns->count())

                    <div class="table-wrapper">
                        <table class="data-table">

                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th class="text-right">Quantity Received</th>
                                    <th class="text-right">Unit Cost</th>
                                    <th>Supplier</th>
                                    <th>Purchase Order Ref.</th>
                                    <th>Reason</th>
                                    <th>Performed By</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($stockIns as $movement)

                                    <tr>
                                        <td>{{ $movement->transaction_date?->format('M d, Y') }}</td>
                                        <td class="strong">{{ $movement->product->product_name ?? '—' }}</td>
                                        <td class="text-right">{{ number_format(abs((int) $movement->quantity)) }}</td>
                                        <td class="text-right">
                                            @if($movement->unit_cost !== null)
                                                ₱{{ number_format((float) $movement->unit_cost, 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $movement->supplier_customer ?? '—' }}</td>
                                        <td>{{ $movement->reference ?? '—' }}</td>
                                        <td>{{ $movement->reason ?? '—' }}</td>
                                        <td>{{ $movement->user->name ?? '—' }}</td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="empty-state">No stock-in records found for the selected filters.</div>

                @endif

            </section>

        @endif


        {{-- ===================== STOCK OUT ===================== --}}
        @if($report === 'stock-out')

            @php
                $totalReleased = $stockOuts->sum(function ($movement) {
                    return abs((int) $movement->quantity);
                });
            @endphp

            <div class="cards cols-2">

                <a href="#" class="card js-report-card" data-title="Stock Out Transactions" data-filter="" style="--card-color:#2563eb">
                    <div class="card-label">Stock Out Transactions</div>
                    <div class="card-value">{{ $stockOuts->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Quantity Released" data-filter="" style="--card-color:#dc2626">
                    <div class="card-label">Total Quantity Released</div>
                    <div class="card-value">{{ number_format($totalReleased) }}</div>
                    <div class="card-link">View details</div>
                </a>

            </div>

            <section class="panel table-panel" id="report-panel">

                <div class="panel-head">
                    <div>
                        <h2>{{ $reportTitle }}</h2>
                        <div class="panel-sub">{{ $reportSub }}</div>
                    </div>
                </div>

                @if($stockOuts->count())

                    <div class="table-wrapper">
                        <table class="data-table" style="min-width:1050px">

                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Customer Order Ref.</th>
                                    <th>Receipt Number</th>
                                    <th class="text-right">Quantity</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-right">Amount</th>
                                    <th>Received By</th>
                                    <th>Performed By</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($stockOuts as $movement)

                                    <tr>
                                        <td>{{ $movement->transaction_date?->format('M d, Y') }}</td>
                                        <td class="strong">{{ $movement->product->product_name ?? '—' }}</td>
                                        <td>{{ $movement->supplier_customer ?? '—' }}</td>
                                        <td>{{ $movement->customer_order_reference ?? '—' }}</td>
                                        <td>{{ $movement->receipt_number ?? '—' }}</td>
                                        <td class="text-right">{{ number_format(abs((int) $movement->quantity)) }}</td>
                                        <td class="text-right">
                                            @if($movement->unit_price !== null)
                                                ₱{{ number_format((float) $movement->unit_price, 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if($movement->amount !== null)
                                                ₱{{ number_format((float) $movement->amount, 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $movement->received_by ?? '—' }}</td>
                                        <td>{{ $movement->user->name ?? '—' }}</td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="empty-state">No stock-out records found for the selected filters.</div>

                @endif

            </section>

        @endif


        {{-- ===================== LOW STOCK ===================== --}}
        @if($report === 'low-stock')

            @php
                $outOfStock = $lowStockProducts->filter(function ($product) {
                    $stock = $product->inventory ? (int) $product->inventory->current_stock : 0;
                    return $stock <= 0;
                })->count();

                $lowOnly = $lowStockProducts->count() - $outOfStock;
            @endphp

            <div class="cards cols-3">

                <a href="#" class="card js-report-card" data-title="Products Needing Attention" data-filter="" style="--card-color:#2563eb">
                    <div class="card-label">Products Needing Attention</div>
                    <div class="card-value">{{ $lowStockProducts->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Low-Stock Products" data-filter="Low Stock" style="--card-color:#f59e0b">
                    <div class="card-label">Low Stock</div>
                    <div class="card-value">{{ $lowOnly }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Out-of-Stock Products" data-filter="Out of Stock" style="--card-color:#dc2626">
                    <div class="card-label">Out of Stock</div>
                    <div class="card-value">{{ $outOfStock }}</div>
                    <div class="card-link">View details</div>
                </a>

            </div>

            <section class="panel table-panel" id="report-panel">

                <div class="panel-head">
                    <div>
                        <h2>{{ $reportTitle }}</h2>
                        <div class="panel-sub">{{ $reportSub }}</div>
                    </div>
                </div>

                @if($lowStockProducts->count())

                    <div class="table-wrapper">
                        <table class="data-table">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Unit</th>
                                    <th class="text-right">Current Stock</th>
                                    <th class="text-right">Reorder Level</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($lowStockProducts as $index => $product)

                                    @php
                                        $stock = $product->inventory ? (int) $product->inventory->current_stock : 0;
                                        $reorderLevel = (int) $product->reorder_level;
                                    @endphp

                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="strong">{{ $product->product_name }}</td>
                                        <td>{{ $product->category->category_name ?? '—' }}</td>
                                        <td>{{ $product->unit }}</td>
                                        <td class="text-right">{{ number_format($stock) }}</td>
                                        <td class="text-right">{{ number_format($reorderLevel) }}</td>
                                        <td>
                                            @if($stock <= 0)
                                                <span class="pill pill-red">Out of Stock</span>
                                            @else
                                                <span class="pill pill-amber">Low Stock</span>
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="empty-state">No products currently require reordering.</div>

                @endif

            </section>

        @endif


        {{-- ===================== CUSTOMER ORDERS ===================== --}}
        @if($report === 'customer-orders')

            <div class="cards">

                <a href="#" class="card js-report-card" data-title="All Customer Orders" data-filter="" style="--card-color:#2563eb">
                    <div class="card-label">Total Orders</div>
                    <div class="card-value">{{ $customerOrders->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Pending Inventory Check" data-filter="Pending" style="--card-color:#f59e0b">
                    <div class="card-label">Pending</div>
                    <div class="card-value">{{ $customerOrders->where('inventory_check_status', 'pending')->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Available Orders" data-filter="Available" style="--card-color:#059669">
                    <div class="card-label">Available</div>
                    <div class="card-value">{{ $customerOrders->where('inventory_check_status', 'available')->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Insufficient Orders" data-filter="Insufficient" style="--card-color:#dc2626">
                    <div class="card-label">Insufficient</div>
                    <div class="card-value">{{ $customerOrders->where('inventory_check_status', 'insufficient')->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

            </div>

            <section class="panel table-panel" id="report-panel">

                <div class="panel-head">
                    <div>
                        <h2>{{ $reportTitle }}</h2>
                        <div class="panel-sub">{{ $reportSub }}</div>
                    </div>
                </div>

                @if($customerOrders->count())

                    <div class="table-wrapper">
                        <table class="data-table">

                            <thead>
                                <tr>
                                    <th>Order Date</th>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Products / Quantity</th>
                                    <th>Inventory Check</th>
                                    <th>Checked By</th>
                                    <th>Checked Date</th>
                                    <th>Order Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($customerOrders as $order)

                                    <tr>
                                        <td>{{ $order->order_date?->format('M d, Y') }}</td>
                                        <td class="strong">{{ $order->order_number }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>
                                            @foreach($order->items as $item)
                                                <div style="margin-bottom:4px">
                                                    {{ $item->product->product_name ?? 'Unknown Product' }} × {{ number_format((int) $item->quantity) }}
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($order->inventory_check_status === 'available')
                                                <span class="pill pill-green">Available</span>
                                            @elseif($order->inventory_check_status === 'insufficient')
                                                <span class="pill pill-red">Insufficient</span>
                                            @elseif($order->inventory_check_status === 'pending')
                                                <span class="pill pill-amber">Pending</span>
                                            @else
                                                <span class="pill pill-gray">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->inventoryCheckedBy->name ?? '—' }}</td>
                                        <td>{{ $order->inventory_checked_at ? $order->inventory_checked_at->format('M d, Y h:i A') : '—' }}</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $order->status)) }}</td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="empty-state">No customer order records found for the selected filters.</div>

                @endif

            </section>

        @endif


        {{-- ===================== ADJUSTMENTS ===================== --}}
        @if($report === 'adjustments')

            @php
                $totalAdjustment = $adjustments->sum(function ($movement) {
                    return (int) $movement->quantity;
                });
            @endphp

            <div class="cards cols-2">

                <a href="#" class="card js-report-card" data-title="Adjustment Transactions" data-filter="" style="--card-color:#2563eb">
                    <div class="card-label">Adjustment Transactions</div>
                    <div class="card-value">{{ $adjustments->count() }}</div>
                    <div class="card-link">View details</div>
                </a>

                <a href="#" class="card js-report-card" data-title="Net Adjustment" data-filter="" style="--card-color:#f59e0b">
                    <div class="card-label">Net Adjustment</div>
                    <div class="card-value">{{ $totalAdjustment > 0 ? '+' : '' }}{{ number_format($totalAdjustment) }}</div>
                    <div class="card-link">View details</div>
                </a>

            </div>

            <section class="panel table-panel" id="report-panel">

                <div class="panel-head">
                    <div>
                        <h2>{{ $reportTitle }}</h2>
                        <div class="panel-sub">{{ $reportSub }}</div>
                    </div>
                </div>

                @if($adjustments->count())

                    <div class="table-wrapper">
                        <table class="data-table">

                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th class="text-right">Stock Before</th>
                                    <th class="text-right">Adjustment</th>
                                    <th class="text-right">Stock After</th>
                                    <th>Reason</th>
                                    <th>Reference</th>
                                    <th>Performed By</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($adjustments as $movement)

                                    <tr>
                                        <td>{{ $movement->transaction_date?->format('M d, Y') }}</td>
                                        <td class="strong">{{ $movement->product->product_name ?? '—' }}</td>
                                        <td class="text-right">{{ number_format($movement->stock_before) }}</td>
                                        <td class="text-right">
                                            @if($movement->quantity > 0)
                                                +{{ number_format($movement->quantity) }}
                                            @else
                                                {{ number_format($movement->quantity) }}
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($movement->stock_after) }}</td>
                                        <td>{{ $movement->reason ?? '—' }}</td>
                                        <td>{{ $movement->reference ?? '—' }}</td>
                                        <td>{{ $movement->user->name ?? '—' }}</td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="empty-state">No inventory adjustment records found for the selected filters.</div>

                @endif

            </section>

        @endif

    </main>

</div>


{{-- CARD POPUP MODAL --}}
<div id="reportModal" class="modal">

    <div class="modal-box">

        <div class="modal-header">
            <h2 id="reportModalTitle">Details</h2>
            <button type="button" class="close-button" onclick="closeReportModal()" aria-label="Close">&times;</button>
        </div>

        <div id="reportModalBody" class="modal-body"></div>

    </div>

</div>


<script>

    const reportModal = document.getElementById('reportModal');
    const reportModalTitle = document.getElementById('reportModalTitle');
    const reportModalBody = document.getElementById('reportModalBody');


    function openReportModal(title, filter)
    {
        const source = document.getElementById('report-panel');

        if (!source) return;

        const panel = source.cloneNode(true);

        panel.removeAttribute('id');

        /* Keep only rows whose status pill matches the clicked card */
        if (filter)
        {
            panel.querySelectorAll('tbody tr').forEach(function (row)
            {
                const pill = row.querySelector('.pill');

                if (!pill || pill.textContent.trim() !== filter) row.remove();
            });

            if (!panel.querySelector('tbody tr'))
            {
                panel.querySelectorAll('.table-wrapper').forEach(function (el) { el.remove(); });
                panel.insertAdjacentHTML('beforeend', '<div class="empty-state">No matching records.</div>');
            }
        }

        reportModalTitle.textContent = title || 'Details';
        reportModalBody.innerHTML = '';
        reportModalBody.appendChild(panel);
        reportModal.classList.add('show');
    }


    function closeReportModal()
    {
        reportModal.classList.remove('show');
        reportModalBody.innerHTML = '';
    }


    document.addEventListener('click', function (event)
    {
        const card = event.target.closest('.js-report-card');

        if (card)
        {
            event.preventDefault();

            openReportModal(card.getAttribute('data-title'), card.getAttribute('data-filter'));

            return;
        }

        if (event.target === reportModal) closeReportModal();
    });


    document.addEventListener('keydown', function (event)
    {
        if (event.key === 'Escape' && reportModal.classList.contains('show')) closeReportModal();
    });

</script>

</body>

</html>