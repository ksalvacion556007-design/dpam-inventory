<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Secretary - Stock Card | DPAM IMS</title>

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

        /* ALERTS */
        .alert { padding:14px 16px; border-radius:10px; margin-bottom:20px; font-size:13px; }
        .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
        .alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

        /* CARDS */
        .cards { display:grid; grid-template-columns:repeat(4,1fr); gap:18px; margin-bottom:22px; }
        .card { display:block; background:#fff; border:1px solid var(--line); border-radius:14px; padding:20px; position:relative; overflow:hidden; }
        .card::before { content:""; position:absolute; left:0; top:0; bottom:0; width:4px; background:var(--card-color, var(--accent)); }
        .card-label { font-size:13px; color:var(--muted); margin-bottom:10px; }
        .card-value { font-size:30px; font-weight:700; }

        /* PANELS */
        .panel { background:#fff; border:1px solid var(--line); border-radius:14px; padding:22px; margin-bottom:22px; }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
        .panel h2 { margin:0; font-size:17px; }
        .panel-sub { margin-top:4px; font-size:12px; color:var(--muted); }
        .table-panel { padding:0; overflow:hidden; }
        .table-panel > .panel-head { padding:18px 22px; margin:0; border-bottom:1px solid var(--line); }

        /* FILTER */
        .filter-bar { display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; }
        .form-group { flex:1; min-width:220px; }
        .form-group label { display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:6px; }
        .form-control { width:100%; height:42px; border:1px solid #cbd5e1; border-radius:8px; padding:0 12px; background:#fff; font-size:13px; color:var(--ink); }
        .form-control:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(37,99,235,.12); }

        /* PRODUCT HEADER */
        .product-head { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap; }
        .product-name { font-size:22px; font-weight:700; }
        .product-meta { margin-top:6px; font-size:13px; color:var(--muted); line-height:1.7; }
        .product-meta strong { color:var(--ink); }
        .stock-balance { text-align:right; }
        .stock-label { font-size:11px; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); font-weight:700; }
        .stock-value { font-size:30px; font-weight:700; }
        .stock-unit { font-size:12px; color:var(--muted); }

        /* STOCK CARD TABLE */
        .table-wrapper { overflow-x:auto; }
        .stock-table { width:100%; border-collapse:collapse; min-width:1250px; }
        .stock-table th, .stock-table td { border:1px solid var(--line); padding:10px 10px; font-size:12px; vertical-align:middle; }
        .stock-table th { background:#f8fafc; color:#475569; font-weight:700; text-align:center; white-space:nowrap; }
        .stock-table th.group { background:#e2e8f0; color:var(--ink); font-size:12px; letter-spacing:.04em; }
        .stock-table td.center { text-align:center; }
        .stock-table td.right { text-align:right; }
        .stock-table td.dash { color:#94a3b8; text-align:center; }
        .stock-table tbody tr:hover { background:#f8fafc; }
        .row-receive { background:#f0fdf4; }
        .row-sale { background:#fff7ed; }
        .row-adjustment { background:#f8fafc; }
        .note { display:block; margin-top:3px; font-size:11px; color:var(--muted); }
        .empty-state { color:var(--muted); font-size:14px; padding:40px 20px; text-align:center; }
        .empty-state strong { display:block; color:#334155; margin-bottom:4px; }

        /* BUTTONS */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; border:none; border-radius:8px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s; }
        .btn-primary { background:var(--accent); color:#fff; }
        .btn-primary:hover { background:var(--accent-dark); }
        .btn-dark { background:#0f172a; color:#fff; }

        /* PRINT */
        .print-only { display:none; }

        @media print {
            @page { size:landscape; margin:10mm; }
            body { background:#fff; }
            .sidebar, .no-print { display:none !important; }
            .layout { display:block; }
            .main { padding:0; }
            .print-only { display:block; margin-bottom:12px; }
            .print-only h1 { margin:0; font-size:18px; }
            .print-only p { margin:4px 0 0; font-size:11px; color:#475569; }
            .panel { border:none; padding:0; margin-bottom:10px; }
            .stock-table { min-width:0; }
            .stock-table th, .stock-table td { font-size:9px; padding:5px; }
            .table-wrapper { overflow:visible; }
            .row-receive, .row-sale, .row-adjustment, .stock-table th, .stock-table th.group { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
        }

        /* RESPONSIVE */
        @media (max-width:1000px) { .cards { grid-template-columns:repeat(2,1fr); } }
        @media (max-width:800px) { .layout { flex-direction:column; } .sidebar { width:100%; height:auto; position:static; } .main { padding:20px; } .topbar { flex-direction:column; align-items:flex-start; } .stock-balance { text-align:left; } }
        @media (max-width:560px) { .cards { grid-template-columns:1fr; } }
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
            <a href="{{ route('secretary.stock-card') }}" class="active">
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

        <div class="topbar no-print">
            <div>
                <h1 class="page-title">Stock Card</h1>
                <div class="page-description">Running inventory history of each product</div>
            </div>
            <div class="top-actions">
                @if($selectedProduct)
                    <button type="button" class="btn btn-dark" onclick="window.print()">Print Stock Card</button>
                @endif
                <span class="user-chip">{{ auth()->user()->name }}</span>
                <span class="role-badge">SECRETARY</span>
            </div>
        </div>


        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success no-print">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error no-print">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error no-print">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif


        {{-- PRODUCT SELECTOR --}}
        <section class="panel no-print">

            <form method="GET" action="{{ route('secretary.stock-card') }}" class="filter-bar">

                <div class="form-group">
                    <label for="search">Search Product</label>
                    <input type="text" id="search" name="search" class="form-control" value="{{ $search }}" placeholder="Product name, API, or base oil">
                </div>

                <div class="form-group">
                    <label for="product_id">Product</label>
                    <select id="product_id" name="product_id" class="form-control">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ (string) $selectedProductId === (string) $product->id ? 'selected' : '' }}>
                                {{ $product->product_name }}@if($product->unit) — {{ $product->unit }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">View Stock Card</button>

            </form>

        </section>


        @if($selectedProduct)

            <div class="print-only">
                <h1>DPAM IMS - Stock Card</h1>
                <p>{{ $selectedProduct->product_name }} | Printed {{ now()->format('M d, Y h:i A') }} by {{ auth()->user()->name }}</p>
            </div>


            {{-- PRODUCT INFORMATION --}}
            <section class="panel">

                <div class="product-head">

                    <div>
                        <div class="product-name">{{ $selectedProduct->product_name }}</div>

                        <div class="product-meta">
                            Category: <strong>{{ $selectedProduct->category?->category_name ?? '—' }}</strong>
                            &nbsp;|&nbsp; Unit: <strong>{{ $selectedProduct->unit ?: '—' }}</strong>
                            @if($selectedProduct->package_size)
                                &nbsp;|&nbsp; Package: <strong>{{ $selectedProduct->package_size }}</strong>
                            @endif
                            @if($selectedProduct->api)
                                &nbsp;|&nbsp; API: <strong>{{ $selectedProduct->api }}</strong>
                            @endif
                        </div>
                    </div>

                    <div class="stock-balance">
                        <div class="stock-label">Current Stock On Hand</div>
                        <div class="stock-value">{{ number_format($currentStock) }}</div>
                        <div class="stock-unit">{{ $selectedProduct->unit ?: 'units' }}</div>
                    </div>

                </div>

            </section>


            {{-- SUMMARY --}}
            <div class="cards no-print">

                <div class="card" style="--card-color:#059669">
                    <div class="card-label">Total Received</div>
                    <div class="card-value">{{ number_format($totalReceived) }}</div>
                </div>

                <div class="card" style="--card-color:#f59e0b">
                    <div class="card-label">Total Released</div>
                    <div class="card-value">{{ number_format($totalReleased) }}</div>
                </div>

                <div class="card" style="--card-color:#64748b">
                    <div class="card-label">Adjustments</div>
                    <div class="card-value">
                        @if($adjustmentQuantity > 0)
                            +{{ number_format($adjustmentQuantity) }}
                        @else
                            {{ number_format($adjustmentQuantity) }}
                        @endif
                    </div>
                </div>

                <div class="card" style="--card-color:#2563eb">
                    <div class="card-label">Stock On Hand</div>
                    <div class="card-value">{{ number_format($currentStock) }}</div>
                </div>

            </div>


            {{-- STOCK CARD TABLE --}}
            <section class="panel table-panel">

                <div class="panel-head">
                    <div>
                        <h2>{{ $selectedProduct->product_name }}</h2>
                        <div class="panel-sub">Inventory Movement / Stock Card History</div>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table class="stock-table">

                        <thead>

                            <tr>
                                <th colspan="4" class="group">RECEIVE</th>
                                <th colspan="6" class="group">SALE</th>
                                <th colspan="2" class="group">ADJUSTMENT</th>
                                <th rowspan="2">Stock On Hand</th>
                            </tr>

                            <tr>
                                <th>Date</th>
                                <th>Quantity</th>
                                <th>Cost</th>
                                <th>Supplier</th>

                                <th>Date</th>
                                <th>Receipt No.</th>
                                <th>Customer</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Amount</th>

                                <th>Date</th>
                                <th>Quantity</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($movements as $movement)

                                @if($movement->movement_type === 'stock_in')

                                    <tr class="row-receive">
                                        <td class="center">{{ $movement->transaction_date?->format('M d, Y') }}</td>
                                        <td class="right">{{ number_format(abs($movement->quantity)) }}</td>
                                        <td class="right">
                                            @if($movement->unit_cost !== null)
                                                ₱{{ number_format($movement->unit_cost, 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $movement->supplier_customer ?: '—' }}</td>

                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>

                                        <td class="dash">—</td>
                                        <td class="dash">—</td>

                                        <td class="right"><strong>{{ number_format($movement->stock_after) }}</strong></td>
                                    </tr>

                                @elseif($movement->movement_type === 'stock_out')

                                    <tr class="row-sale">
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>

                                        <td class="center">{{ $movement->transaction_date?->format('M d, Y') }}</td>
                                        <td class="center">{{ $movement->receipt_number ?: '—' }}</td>
                                        <td>{{ $movement->supplier_customer ?: '—' }}</td>
                                        <td class="right">{{ number_format(abs($movement->quantity)) }}</td>
                                        <td class="right">
                                            @if($movement->unit_price !== null)
                                                ₱{{ number_format($movement->unit_price, 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="right">
                                            @if($movement->amount !== null)
                                                ₱{{ number_format($movement->amount, 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td class="dash">—</td>
                                        <td class="dash">—</td>

                                        <td class="right"><strong>{{ number_format($movement->stock_after) }}</strong></td>
                                    </tr>

                                @else

                                    <tr class="row-adjustment">
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>

                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>
                                        <td class="dash">—</td>

                                        <td class="center">{{ $movement->transaction_date?->format('M d, Y') }}</td>
                                        <td class="right">
                                            {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}
                                            <span class="note">{{ $movement->reason ?: 'Physical Count Adjustment' }}</span>
                                        </td>

                                        <td class="right"><strong>{{ number_format($movement->stock_after) }}</strong></td>
                                    </tr>

                                @endif

                            @empty

                                <tr>
                                    <td colspan="13" class="empty-state">No inventory movement has been recorded for this product yet.</td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

            </section>

        @else

            <section class="panel">
                <div class="empty-state">
                    <strong>No product selected</strong>
                    Select a product above to view its stock card.
                </div>
            </section>

        @endif

    </main>

</div>

</body>

</html>