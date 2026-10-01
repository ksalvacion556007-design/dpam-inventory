<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Archive - Secretary - DPAM IMS</title>

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
        .cards { display:grid; grid-template-columns:repeat(2,1fr); gap:18px; margin-bottom:22px; }
        .card { display:block; background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; position:relative; overflow:hidden; }
        .card::before { content:""; position:absolute; left:0; top:0; bottom:0; width:4px; background:var(--card-color, var(--accent)); }
        .card-label { font-size:13px; color:var(--muted); margin-bottom:10px; }
        .card-value { font-size:30px; font-weight:700; }

        /* TABS */
        .tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:22px; }
        .tabs a { padding:9px 15px; border-radius:8px; background:#fff; border:1px solid var(--line); font-size:13px; font-weight:600; color:#475569; }
        .tabs a:hover { border-color:var(--accent); color:var(--accent); }
        .tabs a.active { background:var(--accent); border-color:var(--accent); color:#fff; }

        /* PANELS */
        .panel { background:#fff; border:1px solid var(--line); border-radius:14px; padding:22px; margin-bottom:22px; }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
        .panel h2 { margin:0; font-size:17px; }
        .count-text { font-size:13px; color:var(--muted); }
        .table-panel { padding:0; overflow:hidden; }
        .table-panel > .panel-head { padding:18px 22px; margin:0; border-bottom:1px solid var(--line); }

        /* FILTER */
        .filter-bar { display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; }
        .form-group { flex:1; min-width:220px; }
        .form-group label { display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:6px; }
        .form-control { width:100%; height:42px; border:1px solid #cbd5e1; border-radius:8px; padding:0 12px; background:#fff; font-size:13px; color:var(--ink); }
        .form-control:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(37,99,235,.12); }

        /* TABLE */
        .table-wrapper { overflow-x:auto; }
        .data-table { width:100%; border-collapse:collapse; min-width:800px; }
        .data-table th, .data-table td { padding:12px 16px; border-bottom:1px solid var(--line); text-align:left; font-size:13px; vertical-align:middle; }
        .data-table th { color:var(--muted); font-weight:600; background:#f8fafc; white-space:nowrap; }
        .data-table tbody tr:hover { background:#f8fafc; }
        .data-table tr:last-child td { border-bottom:none; }
        .text-right { text-align:right !important; }
        .strong { font-weight:600; }
        .pill { display:inline-block; padding:4px 10px; border-radius:999px; font-size:11px; font-weight:700; white-space:nowrap; }
        .pill-amber { background:#fef3c7; color:#92400e; }
        .pill-gray { background:#e5e7eb; color:#374151; }
        .empty-state { color:var(--muted); font-size:14px; padding:40px 20px; text-align:center; }

        /* BUTTONS */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; border:none; border-radius:8px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s; }
        .btn-primary { background:var(--accent); color:#fff; }
        .btn-primary:hover { background:var(--accent-dark); }
        .btn-ghost { background:#e2e8f0; color:#334155; }
        .btn-ghost:hover { background:#cbd5e1; }
        .btn-dark { background:#0f172a; color:#fff; }

        /* PRINT */
        .print-only { display:none; }

        @media print {
            @page { size:landscape; margin:12mm; }
            body { background:#fff; }
            .sidebar, .no-print { display:none !important; }
            .layout { display:block; }
            .main { padding:0; }
            .print-only { display:block; margin-bottom:14px; }
            .print-only h1 { margin:0; font-size:20px; }
            .print-only p { margin:4px 0 0; font-size:12px; color:#475569; }
            .panel { border:1px solid #cbd5e1; border-radius:0; margin-bottom:14px; }
            .data-table { min-width:0; }
            .data-table th, .data-table td { font-size:10px; padding:6px; }
            .table-wrapper { overflow:visible; }
        }

        /* RESPONSIVE */
        @media (max-width:800px) { .layout { flex-direction:column; } .sidebar { width:100%; height:auto; position:static; } .main { padding:20px; } .topbar { flex-direction:column; align-items:flex-start; } .cards { grid-template-columns:1fr; } }
    </style>
</head>

<body>

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
            <a href="{{ route('secretary.reports') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>
                Reports
            </a>
            <a href="{{ route('secretary.archive') }}" class="active">
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
                <h1 class="page-title">Archive</h1>
                <div class="page-description">Archived and inactive records</div>
            </div>
            <div class="top-actions">
                <button type="button" class="btn btn-dark" onclick="window.print()">Print Archive</button>
                <span class="user-chip">{{ auth()->user()->name }}</span>
                <span class="role-badge">SECRETARY</span>
            </div>
        </div>

        <div class="print-only">
            <h1>DPAM IMS - Archive</h1>
            <p>Printed {{ now()->format('M d, Y h:i A') }}</p>
        </div>


        {{-- SUMMARY --}}
        <div class="cards no-print">

            <div class="card" style="--card-color:#64748b">
                <div class="card-label">Archived Suppliers</div>
                <div class="card-value">{{ $archivedSupplierCount }}</div>
            </div>

            <div class="card" style="--card-color:#f59e0b">
                <div class="card-label">Inactive Products</div>
                <div class="card-value">{{ $inactiveProductCount }}</div>
            </div>

        </div>


        {{-- TABS --}}
        <div class="tabs no-print">

            <a href="{{ route('secretary.archive', ['type' => 'all', 'search' => $search]) }}" class="{{ $type === 'all' ? 'active' : '' }}">All</a>

            <a href="{{ route('secretary.archive', ['type' => 'suppliers', 'search' => $search]) }}" class="{{ $type === 'suppliers' ? 'active' : '' }}">Archived Suppliers</a>

            <a href="{{ route('secretary.archive', ['type' => 'products', 'search' => $search]) }}" class="{{ $type === 'products' ? 'active' : '' }}">Inactive Products</a>

        </div>


        {{-- SEARCH --}}
        <section class="panel no-print">

            <form method="GET" action="{{ route('secretary.archive') }}" class="filter-bar">

                <div class="form-group">
                    <label for="search">Search Archive</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{ $search }}" placeholder="Product or supplier name">
                </div>

                <input type="hidden" name="type" value="{{ $type }}">

                <button type="submit" class="btn btn-primary">Search</button>

                <a href="{{ route('secretary.archive', ['type' => $type]) }}" class="btn btn-ghost">Clear</a>

            </form>

        </section>


        {{-- ARCHIVED SUPPLIERS --}}
        @if($type === 'all' || $type === 'suppliers')

            <section class="panel table-panel">

                <div class="panel-head">
                    <h2>Archived Suppliers</h2>
                    <span class="count-text">{{ $archivedSuppliers->count() }} record(s)</span>
                </div>

                @if($archivedSuppliers->count())

                    <div class="table-wrapper">
                        <table class="data-table">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Supplier Name</th>
                                    <th>Contact Person</th>
                                    <th>Contact Number</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($archivedSuppliers as $index => $supplier)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="strong">{{ $supplier->supplier_name }}</td>
                                        <td>{{ $supplier->contact_person ?? '—' }}</td>
                                        <td>{{ $supplier->contact_number ?? '—' }}</td>
                                        <td>{{ $supplier->email ?? '—' }}</td>
                                        <td>{{ $supplier->address ?? '—' }}</td>
                                        <td><span class="pill pill-gray">Archived</span></td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="empty-state">No archived suppliers found.</div>

                @endif

            </section>

        @endif


        {{-- INACTIVE PRODUCTS --}}
        @if($type === 'all' || $type === 'products')

            <section class="panel table-panel">

                <div class="panel-head">
                    <h2>Inactive Products</h2>
                    <span class="count-text">{{ $inactiveProducts->count() }} record(s)</span>
                </div>

                @if($inactiveProducts->count())

                    <div class="table-wrapper">
                        <table class="data-table">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>API</th>
                                    <th>Base Oil</th>
                                    <th>Package Size</th>
                                    <th>Unit</th>
                                    <th class="text-right">Current Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($inactiveProducts as $index => $product)

                                    @php
                                        $currentStock = $product->inventory ? (int) $product->inventory->current_stock : 0;
                                    @endphp

                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="strong">{{ $product->product_name }}</td>
                                        <td>{{ $product->category->category_name ?? '—' }}</td>
                                        <td>{{ $product->api ?? '—' }}</td>
                                        <td>{{ $product->base_oil ?? '—' }}</td>
                                        <td>{{ $product->package_size ?? '—' }}</td>
                                        <td>{{ $product->unit }}</td>
                                        <td class="text-right">{{ number_format($currentStock) }}</td>
                                        <td><span class="pill pill-amber">Inactive</span></td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="empty-state">No inactive products found.</div>

                @endif

            </section>

        @endif

    </main>

</div>

</body>

</html>