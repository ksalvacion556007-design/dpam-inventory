<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Orders - Secretary | DPAM IMS</title>

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
        .alert-warning { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
        .alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
        .alert ul { margin:8px 0 0; padding-left:20px; }

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
        .count-text { font-size:13px; color:var(--muted); }
        .table-panel { padding:0; overflow:hidden; }
        .table-panel > .panel-head { padding:18px 22px; margin:0; border-bottom:1px solid var(--line); }

        /* FILTER */
        .filter-bar { display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap; }
        .form-group { flex:1; min-width:220px; }
        .form-group.small { flex:0 0 240px; }
        .form-group label { display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:6px; }
        .form-control { width:100%; height:42px; border:1px solid #cbd5e1; border-radius:8px; padding:0 12px; background:#fff; font-size:13px; color:var(--ink); }
        textarea.form-control { height:auto; min-height:100px; padding:10px 12px; resize:vertical; }
        .form-control:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(37,99,235,.12); }

        /* TABLE */
        .table-wrapper { overflow-x:auto; }
        .data-table { width:100%; border-collapse:collapse; min-width:900px; }
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
        .stock-ok { color:#166534; font-weight:700; }
        .stock-short { color:#991b1b; font-weight:700; }
        .empty-state { color:var(--muted); font-size:14px; padding:40px 20px; text-align:center; }
        .empty-state h3 { margin:0 0 6px; color:#334155; font-size:16px; }
        .empty-state p { margin:0; }
        .action-buttons { display:flex; gap:8px; flex-wrap:wrap; }

        /* BUTTONS */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; border:none; border-radius:8px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s; }
        .btn-primary { background:var(--accent); color:#fff; }
        .btn-primary:hover { background:var(--accent-dark); }
        .btn-success { background:var(--success); color:#fff; }
        .btn-success:hover { background:#047857; }
        .btn-ghost { background:#e2e8f0; color:#334155; }
        .btn-ghost:hover { background:#cbd5e1; }
        .btn-sm { padding:7px 12px; font-size:12px; }

        /* MODAL */
        .modal { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); align-items:center; justify-content:center; padding:20px; z-index:1000; }
        .modal.show { display:flex; }
        .modal-box { background:#fff; width:100%; max-width:960px; max-height:90vh; display:flex; flex-direction:column; border-radius:16px; box-shadow:0 25px 60px rgba(0,0,0,.3); overflow:hidden; }
        .modal-header { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:18px 22px; border-bottom:1px solid var(--line); }
        .modal-header h2 { margin:0; font-size:18px; }
        .close-button { border:none; background:transparent; font-size:26px; line-height:1; cursor:pointer; color:var(--muted); border-radius:6px; width:34px; height:34px; }
        .close-button:hover { background:#f1f5f9; color:var(--ink); }
        .modal-body { padding:22px; overflow:auto; }
        .modal-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
        .detail-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:22px; }
        .detail-item { background:#f8fafc; border:1px solid var(--line); border-radius:10px; padding:14px; }
        .detail-label { display:block; font-size:11px; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); font-weight:700; margin-bottom:6px; }
        .detail-value { font-size:14px; font-weight:600; line-height:1.5; }
        .detail-value.plain { font-weight:400; }
        .section-title { margin:0 0 12px; font-size:16px; }
        .items-wrapper { overflow-x:auto; border:1px solid var(--line); border-radius:10px; margin-bottom:20px; }
        .items-wrapper .data-table { min-width:700px; }
        .check-result { border-radius:10px; padding:14px 16px; margin-bottom:20px; font-size:13px; }
        .check-result strong { display:block; margin-bottom:4px; }
        .result-available { background:#dcfce7; border:1px solid #86efac; color:#166534; }
        .result-insufficient { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; }

        /* RESPONSIVE */
        @media (max-width:1000px) { .cards { grid-template-columns:repeat(2,1fr); } .detail-grid { grid-template-columns:repeat(2,1fr); } }
        @media (max-width:800px) { .layout { flex-direction:column; } .sidebar { width:100%; height:auto; position:static; } .main { padding:20px; } .topbar { flex-direction:column; align-items:flex-start; } }
        @media (max-width:560px) { .cards, .detail-grid { grid-template-columns:1fr; } }
    </style>
</head>

<body>

@php
    $orderStatusMap = [
        'pending_inventory_check' => ['Pending Inventory Check', 'pill-gray'],
        'inventory_checked'       => ['Inventory Checked', 'pill-blue'],
        'confirmed'               => ['Confirmed', 'pill-green'],
        'for_purchasing'          => ['For Purchasing', 'pill-amber'],
        'ready_for_delivery'      => ['Ready for Delivery', 'pill-indigo'],
        'delivered'               => ['Delivered', 'pill-green'],
        'fulfilled'               => ['Fulfilled', 'pill-green'],
        'cancelled'               => ['Cancelled', 'pill-red'],
    ];

    $checkStatusMap = [
        'available'    => ['Available', 'pill-green'],
        'insufficient' => ['Insufficient', 'pill-red'],
    ];
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
            <a href="{{ route('secretary.customer-orders') }}" class="active">
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
                <h1 class="page-title">Customer Orders</h1>
                <div class="page-description">Check order availability against current inventory</div>
            </div>
            <div class="top-actions">
                <span class="user-chip">{{ auth()->user()->name }}</span>
                <span class="role-badge">SECRETARY</span>
            </div>
        </div>


        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Please check the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- SUMMARY --}}
        <div class="cards">

            <div class="card" style="--card-color:#2563eb">
                <div class="card-label">Total Orders</div>
                <div class="card-value">{{ number_format($totalOrders) }}</div>
            </div>

            <div class="card" style="--card-color:#64748b">
                <div class="card-label">Pending Inventory Check</div>
                <div class="card-value">{{ number_format($pendingCount) }}</div>
            </div>

            <div class="card" style="--card-color:#059669">
                <div class="card-label">Available</div>
                <div class="card-value">{{ number_format($availableCount) }}</div>
            </div>

            <div class="card" style="--card-color:#dc2626">
                <div class="card-label">Insufficient</div>
                <div class="card-value">{{ number_format($insufficientCount) }}</div>
            </div>

        </div>


        {{-- FILTER --}}
        <section class="panel">

            <form action="{{ route('secretary.customer-orders') }}" method="GET" class="filter-bar">

                <div class="form-group">
                    <label for="search">Search Customer Order</label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        class="form-control"
                        value="{{ $search ?? '' }}"
                        placeholder="Order number, customer name, or contact"
                    >
                </div>

                <div class="form-group small">
                    <label for="status">Order Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="">All Statuses</option>
                        @foreach($orderStatusMap as $statusValue => $statusInfo)
                            <option value="{{ $statusValue }}" {{ ($status ?? '') === $statusValue ? 'selected' : '' }}>
                                {{ $statusInfo[0] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Search</button>

                @if(!empty($search) || !empty($status))
                    <a href="{{ route('secretary.customer-orders') }}" class="btn btn-ghost">Clear</a>
                @endif

            </form>

        </section>


        {{-- ORDERS TABLE --}}
        <section class="panel table-panel" id="panel-orders">

            <div class="panel-head">
                <h2>Customer Orders</h2>
                <span class="count-text">{{ $orders->count() }} order(s)</span>
            </div>

            @if($orders->count() > 0)

                <div class="table-wrapper">
                    <table class="data-table">

                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Order Date</th>
                                <th>Items</th>
                                <th>Inventory Check</th>
                                <th>Order Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($orders as $order)

                                @php
                                    $checkInfo  = $checkStatusMap[$order->inventory_check_status] ?? ['Pending', 'pill-gray'];
                                    $statusInfo = $orderStatusMap[$order->status] ?? [ucfirst(str_replace('_', ' ', $order->status)), 'pill-gray'];
                                @endphp

                                <tr>
                                    <td class="strong">{{ $order->order_number }}</td>
                                    <td class="strong">{{ $order->customer_name }}</td>
                                    <td>{{ $order->customer_contact ?? '—' }}</td>
                                    <td>{{ $order->order_date?->format('M d, Y') ?? '—' }}</td>
                                    <td>{{ $order->items->count() }} item(s)</td>
                                    <td><span class="pill {{ $checkInfo[1] }}">{{ $checkInfo[0] }}</span></td>
                                    <td><span class="pill {{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span></td>
                                    <td>
                                        <div class="action-buttons">

                                            <button type="button" class="btn btn-primary btn-sm" onclick="openOrderModal({{ $order->id }})">View</button>

                                            @if($order->status === 'pending_inventory_check' || $order->inventory_check_status === 'pending')
                                                <button type="button" class="btn btn-success btn-sm" onclick="openCheckModal({{ $order->id }})">Check Inventory</button>
                                            @endif

                                        </div>
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>

                    </table>
                </div>

            @else

                <div class="empty-state">
                    <h3>No Customer Orders Found</h3>
                    <p>
                        @if(!empty($search) || !empty($status))
                            No customer orders match your current filters.
                        @else
                            There are currently no customer orders.
                        @endif
                    </p>
                </div>

            @endif

        </section>

    </main>

</div>


{{-- VIEW ORDER MODALS --}}
@foreach($orders as $order)

    @php
        $viewStatusInfo = $orderStatusMap[$order->status] ?? [ucfirst(str_replace('_', ' ', $order->status)), 'pill-gray'];
    @endphp

    <div class="modal" id="orderModal{{ $order->id }}" onclick="closeOrderModalOnBackground(event, {{ $order->id }})">

        <div class="modal-box">

            <div class="modal-header">
                <h2>Order {{ $order->order_number }}</h2>
                <button type="button" class="close-button" onclick="closeOrderModal({{ $order->id }})" aria-label="Close">&times;</button>
            </div>

            <div class="modal-body">

                <div class="detail-grid">

                    <div class="detail-item">
                        <span class="detail-label">Order Number</span>
                        <span class="detail-value">{{ $order->order_number }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Customer</span>
                        <span class="detail-value">{{ $order->customer_name }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Contact</span>
                        <span class="detail-value">{{ $order->customer_contact ?? '—' }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Order Date</span>
                        <span class="detail-value">{{ $order->order_date?->format('M d, Y') ?? '—' }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Order Status</span>
                        <span class="detail-value"><span class="pill {{ $viewStatusInfo[1] }}">{{ $viewStatusInfo[0] }}</span></span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Inventory Check</span>
                        <span class="detail-value">{{ ucfirst($order->inventory_check_status) }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Checked By</span>
                        <span class="detail-value">{{ $order->inventoryCheckedBy?->name ?? 'Not checked yet' }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Checked At</span>
                        <span class="detail-value">{{ $order->inventory_checked_at?->format('M d, Y h:i A') ?? '—' }}</span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Created By</span>
                        <span class="detail-value">{{ $order->user?->name ?? '—' }}</span>
                    </div>

                </div>


                @if($order->inventory_check_status === 'available')

                    <div class="check-result result-available">
                        <strong>Inventory Available</strong>
                        Sufficient stock was found for all requested products.
                    </div>

                @elseif($order->inventory_check_status === 'insufficient')

                    <div class="check-result result-insufficient">
                        <strong>Inventory Insufficient</strong>
                        One or more requested products do not have enough stock.
                    </div>

                @endif


                <h3 class="section-title">Requested Products</h3>

                <div class="items-wrapper">
                    <table class="data-table">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th class="text-right">Requested Qty</th>
                                <th class="text-right">Current Stock</th>
                                <th class="text-right">Difference</th>
                                <th>Availability</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($order->items as $item)

                                @php
                                    $currentStock      = $item->product?->inventory?->current_stock ?? 0;
                                    $requestedQuantity = (int) $item->quantity;
                                    $difference        = $currentStock - $requestedQuantity;
                                    $itemAvailable     = $currentStock >= $requestedQuantity;
                                @endphp

                                <tr>
                                    <td class="strong">{{ $item->product?->product_name ?? 'Product unavailable' }}</td>
                                    <td>{{ $item->product?->category?->category_name ?? '—' }}</td>
                                    <td class="text-right">{{ number_format($requestedQuantity) }}</td>
                                    <td class="text-right">{{ number_format($currentStock) }}</td>
                                    <td class="text-right">
                                        @if($difference >= 0)
                                            +{{ number_format($difference) }}
                                        @else
                                            {{ number_format($difference) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($itemAvailable)
                                            <span class="stock-ok">Available</span>
                                        @else
                                            <span class="stock-short">Insufficient</span>
                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>

                    </table>
                </div>


                @if($order->inventory_check_notes)
                    <div class="detail-item" style="margin-bottom:16px">
                        <span class="detail-label">Inventory Check Notes</span>
                        <span class="detail-value plain">{{ $order->inventory_check_notes }}</span>
                    </div>
                @endif

                @if($order->notes)
                    <div class="detail-item">
                        <span class="detail-label">Order Notes</span>
                        <span class="detail-value plain">{{ $order->notes }}</span>
                    </div>
                @endif


                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeOrderModal({{ $order->id }})">Back</button>
                </div>

            </div>

        </div>

    </div>

@endforeach


{{-- CHECK INVENTORY MODALS --}}
@foreach($orders as $order)

    @if($order->status === 'pending_inventory_check' || $order->inventory_check_status === 'pending')

        <div class="modal" id="checkModal{{ $order->id }}" onclick="closeCheckModalOnBackground(event, {{ $order->id }})">

            <div class="modal-box">

                <div class="modal-header">
                    <h2>Check Inventory - {{ $order->order_number }}</h2>
                    <button type="button" class="close-button" onclick="closeCheckModal({{ $order->id }})" aria-label="Close">&times;</button>
                </div>

                <div class="modal-body">

                    <h3 class="section-title">Requested Products</h3>

                    <div class="items-wrapper">
                        <table class="data-table">

                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-right">Requested</th>
                                    <th class="text-right">Current Stock</th>
                                    <th class="text-right">Difference</th>
                                    <th>Result</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($order->items as $item)

                                    @php
                                        $currentStock      = $item->product?->inventory?->current_stock ?? 0;
                                        $requestedQuantity = (int) $item->quantity;
                                        $difference        = $currentStock - $requestedQuantity;
                                        $itemAvailable     = $currentStock >= $requestedQuantity;
                                    @endphp

                                    <tr>
                                        <td class="strong">{{ $item->product?->product_name ?? 'Product unavailable' }}</td>
                                        <td class="text-right">{{ number_format($requestedQuantity) }}</td>
                                        <td class="text-right">{{ number_format($currentStock) }}</td>
                                        <td class="text-right">
                                            @if($difference >= 0)
                                                +{{ number_format($difference) }}
                                            @else
                                                {{ number_format($difference) }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($itemAvailable)
                                                <span class="stock-ok">Available</span>
                                            @else
                                                <span class="stock-short">Insufficient</span>
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>


                    <form action="{{ route('secretary.customer-orders.check-inventory', $order) }}" method="POST">

                        @csrf

                        @method('PATCH')

                        <div class="form-group">
                            <label for="inventory_check_notes_{{ $order->id }}">Inventory Check Notes</label>
                            <textarea
                                id="inventory_check_notes_{{ $order->id }}"
                                name="inventory_check_notes"
                                class="form-control"
                                placeholder="Notes about this inventory check"
                            ></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-ghost" onclick="closeCheckModal({{ $order->id }})">Cancel</button>
                            <button type="submit" class="btn btn-success">Complete Inventory Check</button>
                        </div>

                    </form>

                </div>

            </div>

        </div>

    @endif

@endforeach


<script>

    function openOrderModal(orderId)
    {
        const modal = document.getElementById('orderModal' + orderId);

        if (!modal) return;

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }


    function closeOrderModal(orderId)
    {
        const modal = document.getElementById('orderModal' + orderId);

        if (!modal) return;

        modal.classList.remove('show');
        document.body.style.overflow = '';
    }


    function closeOrderModalOnBackground(event, orderId)
    {
        if (event.target.classList.contains('modal')) closeOrderModal(orderId);
    }


    function openCheckModal(orderId)
    {
        const modal = document.getElementById('checkModal' + orderId);

        if (!modal) return;

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }


    function closeCheckModal(orderId)
    {
        const modal = document.getElementById('checkModal' + orderId);

        if (!modal) return;

        modal.classList.remove('show');
        document.body.style.overflow = '';
    }


    function closeCheckModalOnBackground(event, orderId)
    {
        if (event.target.classList.contains('modal')) closeCheckModal(orderId);
    }


    document.addEventListener('keydown', function (event)
    {
        if (event.key !== 'Escape') return;

        document.querySelectorAll('.modal.show').forEach(function (modal) { modal.classList.remove('show'); });

        document.body.style.overflow = '';
    });

</script>

</body>

</html>