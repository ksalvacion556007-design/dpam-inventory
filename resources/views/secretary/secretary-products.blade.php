<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products - Secretary | DPAM IMS</title>

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

        /* PANELS */
        .panel { background:#fff; border:1px solid var(--line); border-radius:14px; padding:22px; margin-bottom:22px; }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
        .panel h2 { margin:0; font-size:17px; }
        .panel-sub { margin-top:4px; font-size:12px; color:var(--muted); }
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
        .data-table { width:100%; border-collapse:collapse; min-width:1000px; }
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
        .empty-state h3 { margin:0 0 6px; color:#334155; font-size:16px; }
        .empty-state p { margin:0; }

        /* BUTTONS */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; border:none; border-radius:8px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s; }
        .btn-primary { background:var(--accent); color:#fff; }
        .btn-primary:hover { background:var(--accent-dark); }
        .btn-ghost { background:#e2e8f0; color:#334155; }
        .btn-ghost:hover { background:#cbd5e1; }
        .btn-sm { padding:7px 12px; font-size:12px; }

        /* DETAILS */
        .detail-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
        .detail-item { background:#f8fafc; border:1px solid var(--line); border-radius:10px; padding:14px; }
        .detail-label { font-size:11px; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); font-weight:700; margin-bottom:6px; }
        .detail-value { font-size:14px; font-weight:600; }
        .detail-stock { font-size:22px; font-weight:700; }
        .section-title { margin:26px 0 12px; font-size:16px; }
        .movement-panel { border:1px solid var(--line); border-radius:12px; overflow:hidden; }
        .movement-panel .data-table { min-width:900px; }

        /* RESPONSIVE */
        @media (max-width:1100px) { .detail-grid { grid-template-columns:repeat(2,1fr); } }
        @media (max-width:800px) { .layout { flex-direction:column; } .sidebar { width:100%; height:auto; position:static; } .main { padding:20px; } .topbar { flex-direction:column; align-items:flex-start; } .detail-grid { grid-template-columns:1fr; } }
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
            <a href="{{ route('secretary.products') }}" class="active">
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
                <h1 class="page-title">Products</h1>
                <div class="page-description">Product information and current stock status</div>
            </div>
            <div class="top-actions">
                <span class="user-chip">{{ auth()->user()->name }}</span>
                <span class="role-badge">SECRETARY</span>
            </div>
        </div>


        {{-- SEARCH --}}
        <section class="panel">

            <form action="{{ route('secretary.products') }}" method="GET" class="filter-bar">

                <div class="form-group">
                    <label for="search">Search Product</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        value="{{ $search ?? '' }}"
                        placeholder="Product name, API, base oil, or package size"
                    >
                </div>

                <button type="submit" class="btn btn-primary">Search</button>

                @if(!empty($search))
                    <a href="{{ route('secretary.products') }}" class="btn btn-ghost">Clear</a>
                @endif

            </form>

        </section>


        {{-- SELECTED PRODUCT --}}
        @if($selectedProduct)

            @php
                $selectedCurrentStock = $selectedProduct->inventory?->current_stock ?? 0;
                $selectedReorderLevel = $selectedProduct->reorder_level ?? 0;

                if ($selectedCurrentStock <= 0) {
                    $selectedStockStatus = 'Out of Stock';
                    $selectedStatusClass = 'pill-red';
                } elseif ($selectedCurrentStock <= $selectedReorderLevel) {
                    $selectedStockStatus = 'Low Stock';
                    $selectedStatusClass = 'pill-amber';
                } else {
                    $selectedStockStatus = 'Available';
                    $selectedStatusClass = 'pill-green';
                }
            @endphp

            <section class="panel">

                <div class="panel-head">
                    <div>
                        <h2>{{ $selectedProduct->product_name }}</h2>
                        <div class="panel-sub">Product Details</div>
                    </div>
                    <a href="{{ route('secretary.products', ['search' => $search]) }}" class="btn btn-ghost">Back to Products</a>
                </div>

                <div class="detail-grid">

                    <div class="detail-item">
                        <div class="detail-label">Product Name</div>
                        <div class="detail-value">{{ $selectedProduct->product_name }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Category</div>
                        <div class="detail-value">{{ $selectedProduct->category?->category_name ?? '—' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">API</div>
                        <div class="detail-value">{{ $selectedProduct->api ?? '—' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Base Oil</div>
                        <div class="detail-value">{{ $selectedProduct->base_oil ?? '—' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Package Size</div>
                        <div class="detail-value">{{ $selectedProduct->package_size ?? '—' }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Unit</div>
                        <div class="detail-value">{{ $selectedProduct->unit }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Unit Price</div>
                        <div class="detail-value">₱{{ number_format((float) $selectedProduct->unit_price, 2) }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Reorder Level</div>
                        <div class="detail-value">{{ number_format($selectedReorderLevel) }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Current Stock</div>
                        <div class="detail-value detail-stock">{{ number_format($selectedCurrentStock) }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Stock Status</div>
                        <div class="detail-value">
                            <span class="pill {{ $selectedStatusClass }}">{{ $selectedStockStatus }}</span>
                        </div>
                    </div>

                </div>


                <h3 class="section-title">Recent Inventory Movements</h3>

                @if($selectedProduct->inventoryMovements->count() > 0)

                    <div class="movement-panel">
                        <div class="table-wrapper">
                            <table class="data-table">

                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Movement</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Stock Before</th>
                                        <th class="text-right">Stock After</th>
                                        <th>Reference</th>
                                        <th>Performed By</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($selectedProduct->inventoryMovements as $movement)

                                        @php
                                            if ($movement->movement_type === 'stock_in') {
                                                $movementLabel = 'Stock In';
                                                $movementClass = 'pill-green';
                                            } elseif ($movement->movement_type === 'stock_out') {
                                                $movementLabel = 'Stock Out';
                                                $movementClass = 'pill-red';
                                            } else {
                                                $movementLabel = 'Adjustment';
                                                $movementClass = 'pill-gray';
                                            }
                                        @endphp

                                        <tr>
                                            <td>{{ $movement->transaction_date?->format('M d, Y') ?? '—' }}</td>
                                            <td><span class="pill {{ $movementClass }}">{{ $movementLabel }}</span></td>
                                            <td class="text-right">{{ number_format($movement->quantity) }}</td>
                                            <td class="text-right">{{ number_format($movement->stock_before) }}</td>
                                            <td class="text-right">{{ number_format($movement->stock_after) }}</td>
                                            <td>
                                                @if($movement->receipt_number)
                                                    Receipt: {{ $movement->receipt_number }}
                                                @elseif($movement->reference)
                                                    {{ $movement->reference }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $movement->user?->name ?? '—' }}</td>
                                        </tr>

                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>

                @else

                    <div class="empty-state">
                        <h3>No Inventory Movements</h3>
                        <p>No movement has been recorded for this product yet.</p>
                    </div>

                @endif

            </section>

        @endif


        {{-- PRODUCT TABLE --}}
        <section class="panel table-panel" id="panel-products">

            <div class="panel-head">
                <h2>Active Products</h2>
                <span class="count-text">{{ $products->count() }} product(s)</span>
            </div>

            @if($products->count() > 0)

                <div class="table-wrapper">
                    <table class="data-table">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>API</th>
                                <th>Base Oil</th>
                                <th>Package Size</th>
                                <th>Unit</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Reorder Level</th>
                                <th class="text-right">Current Stock</th>
                                <th>Stock Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($products as $product)

                                @php
                                    $currentStock = $product->inventory?->current_stock ?? 0;
                                    $reorderLevel = $product->reorder_level ?? 0;

                                    if ($currentStock <= 0) {
                                        $stockStatus = 'Out of Stock';
                                        $statusClass = 'pill-red';
                                    } elseif ($currentStock <= $reorderLevel) {
                                        $stockStatus = 'Low Stock';
                                        $statusClass = 'pill-amber';
                                    } else {
                                        $stockStatus = 'Available';
                                        $statusClass = 'pill-green';
                                    }
                                @endphp

                                <tr>
                                    <td class="strong">{{ $product->product_name }}</td>
                                    <td>{{ $product->category?->category_name ?? '—' }}</td>
                                    <td>{{ $product->api ?? '—' }}</td>
                                    <td>{{ $product->base_oil ?? '—' }}</td>
                                    <td>{{ $product->package_size ?? '—' }}</td>
                                    <td>{{ $product->unit }}</td>
                                    <td class="text-right">₱{{ number_format((float) $product->unit_price, 2) }}</td>
                                    <td class="text-right">{{ number_format($reorderLevel) }}</td>
                                    <td class="text-right strong">{{ number_format($currentStock) }}</td>
                                    <td><span class="pill {{ $statusClass }}">{{ $stockStatus }}</span></td>
                                    <td>
                                        <a href="{{ route('secretary.products', ['product' => $product->id, 'search' => $search]) }}" class="btn btn-primary btn-sm">View</a>
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>

                    </table>
                </div>

            @else

                <div class="empty-state">
                    <h3>No Products Found</h3>
                    <p>
                        @if(!empty($search))
                            No active products match your search.
                        @else
                            There are currently no active products.
                        @endif
                    </p>
                </div>

            @endif

        </section>

    </main>

</div>

</body>

</html>