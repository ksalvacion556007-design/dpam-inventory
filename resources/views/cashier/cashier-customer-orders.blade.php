<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders | Cashier | DPAM IMS</title>

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

        .toolbar,.table-panel,.details-card{background:#fff;border:1px solid var(--line);border-radius:14px}
        .toolbar{padding:18px;margin-bottom:20px}
        .filter-form{display:grid;grid-template-columns:1fr 220px auto auto;gap:10px;align-items:end}
        .field{display:flex;flex-direction:column;gap:6px}
        .field label{font-size:12px;color:var(--muted);font-weight:600}
        input,select{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:14px;font-family:inherit;color:var(--ink);transition:border-color .15s,box-shadow .15s}
        input:focus,select:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(37,99,235,.15)}

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
        .order-number{font-weight:700}
        .customer-name{font-weight:600}
        .muted{color:#94a3b8;font-size:12px}
        .empty{padding:45px 20px;text-align:center;color:var(--muted);font-size:13px}
        .pagination-wrapper{padding:16px 20px;border-top:1px solid var(--line)}

        .status{display:inline-block;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700}
        .s-green{background:#dcfce7;color:#166534}.s-blue{background:#dbeafe;color:#1e40af}
        .s-amber{background:#fef3c7;color:#92400e}.s-red{background:#fee2e2;color:#991b1b}.s-gray{background:#f1f5f9;color:#475569}

        .details-body{padding:22px}
        .details-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-bottom:24px}
        .detail-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:5px}
        .detail-value{font-size:14px;font-weight:600}
        .items-title{font-size:14px;font-weight:700;margin-bottom:10px}
        .note-box{margin-top:18px;padding:14px;background:#f8fafc;border:1px solid var(--line);border-radius:10px;font-size:13px;line-height:1.5}

        @media(max-width:1000px){.filter-form{grid-template-columns:1fr 1fr}.details-grid{grid-template-columns:1fr 1fr}}
        @media(max-width:800px){.layout{flex-direction:column}.sidebar{width:100%}.main{padding:20px}.topbar{flex-direction:column;align-items:flex-start}.filter-form,.details-grid{grid-template-columns:1fr}}
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
            <a href="{{ route('cashier.customer-orders') }}" class="active">
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
                <h1 class="page-title">Customer Orders</h1>
                <div class="page-description">Orders relevant to payment and accounting.</div>
            </div>
            <div class="admin-badge">CASHIER</div>
        </div>


        @isset($selectedOrder)

            {{-- ORDER DETAILS (with back) --}}
            <section class="details-card">

                <div class="table-header">
                    <h2>Order Details: {{ $selectedOrder->order_number }}</h2>

                    <a href="{{ route('cashier.customer-orders') }}" class="button button-secondary button-small">
                        &larr; Back to Orders
                    </a>
                </div>

                <div class="details-body">

                    <div class="details-grid">
                        <div>
                            <div class="detail-label">Customer</div>
                            <div class="detail-value">{{ $selectedOrder->customer_name }}</div>
                        </div>
                        <div>
                            <div class="detail-label">Contact</div>
                            <div class="detail-value">{{ $selectedOrder->customer_contact ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="detail-label">Order Date</div>
                            <div class="detail-value">{{ optional($selectedOrder->order_date)->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <div class="detail-label">Inventory Check</div>
                            <div class="detail-value">
                                @if($selectedOrder->inventory_check_status)
                                    {{ ucwords(str_replace('_', ' ', $selectedOrder->inventory_check_status)) }}
                                @else
                                    Pending
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="detail-label">Checked By</div>
                            <div class="detail-value">{{ $selectedOrder->inventoryCheckedBy->name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="detail-label">Owner Decision</div>
                            <div class="detail-value">
                                {{ $selectedOrder->owner_decision
                                    ? ucwords(str_replace('_', ' ', $selectedOrder->owner_decision))
                                    : '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="items-title">Ordered Products</div>

                    <div class="table-wrapper" style="border:1px solid var(--line);border-radius:10px">
                        <table>
                            <thead>
                                <tr><th>Product</th><th>Quantity</th><th>Unit Price</th><th>Subtotal</th></tr>
                            </thead>
                            <tbody>
                            @forelse($selectedOrder->items as $item)
                                <tr>
                                    <td>{{ $item->product->product_name ?? '—' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₱{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td>₱{{ number_format((float) $item->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4">No products recorded.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($selectedOrder->inventory_check_notes)
                        <div class="note-box">
                            <strong>Inventory Check Notes:</strong>
                            {{ $selectedOrder->inventory_check_notes }}
                        </div>
                    @endif

                    @if($selectedOrder->notes)
                        <div class="note-box">
                            <strong>Notes:</strong>
                            {{ $selectedOrder->notes }}
                        </div>
                    @endif

                </div>

            </section>

        @else

            {{-- SEARCH / FILTER --}}
            <div class="toolbar">
                <form method="GET" action="{{ route('cashier.customer-orders') }}" class="filter-form">

                    <div class="field">
                        <label for="q">Search</label>
                        <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Order number or customer name">
                    </div>

                    <div class="field">
                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="">All Statuses</option>
                            <option value="pending_inventory_check" @selected(request('status') === 'pending_inventory_check')>Pending Inventory Check</option>
                            <option value="inventory_checked" @selected(request('status') === 'inventory_checked')>Inventory Checked</option>
                            <option value="confirmed" @selected(request('status') === 'confirmed')>Confirmed</option>
                            <option value="partially_fulfilled" @selected(request('status') === 'partially_fulfilled')>Partially Fulfilled</option>
                            <option value="fulfilled" @selected(request('status') === 'fulfilled')>Fulfilled</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="button button-primary">Search</button>

                    <a href="{{ route('cashier.customer-orders') }}" class="button button-secondary">Clear</a>

                </form>
            </div>


            {{-- ORDER TABLE --}}
            <section class="table-panel">

                <div class="table-header">
                    <h2>Customer Order Records</h2>
                </div>

                <div class="table-wrapper">

                    @if($orders->count())

                        <table>
                            <thead>
                                <tr>
                                    <th>Order No.</th>
                                    <th>Customer</th>
                                    <th>Order Date</th>
                                    <th>Inventory Check</th>
                                    <th>Owner Decision</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td><span class="order-number">{{ $order->order_number }}</span></td>

                                    <td>
                                        <div class="customer-name">{{ $order->customer_name }}</div>
                                        @if($order->customer_contact)
                                            <div class="muted">{{ $order->customer_contact }}</div>
                                        @endif
                                    </td>

                                    <td>{{ optional($order->order_date)->format('M d, Y') }}</td>

                                    <td>
                                        @if($order->inventory_check_status === 'available')
                                            <span class="status s-green">Available</span>
                                        @elseif($order->inventory_check_status === 'insufficient')
                                            <span class="status s-red">Insufficient</span>
                                        @elseif($order->inventory_check_status)
                                            <span class="status s-blue">{{ ucwords(str_replace('_', ' ', $order->inventory_check_status)) }}</span>
                                        @else
                                            <span class="status s-amber">Pending</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($order->owner_decision)
                                            <span class="status s-gray">{{ ucwords(str_replace('_', ' ', $order->owner_decision)) }}</span>
                                        @else
                                            <span class="muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($order->status === 'confirmed')
                                            <span class="status s-green">Confirmed</span>
                                        @elseif($order->status === 'fulfilled')
                                            <span class="status s-green">Fulfilled</span>
                                        @elseif($order->status === 'pending_inventory_check')
                                            <span class="status s-amber">Pending Check</span>
                                        @elseif($order->status === 'inventory_checked')
                                            <span class="status s-blue">Inventory Checked</span>
                                        @else
                                            <span class="status s-gray">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('cashier.customer-orders.show', $order) }}" class="button button-secondary button-small">View</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    @else

                        <div class="empty">No customer orders found.</div>

                    @endif

                </div>

                @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="pagination-wrapper">
                        {{ $orders->links() }}
                    </div>
                @endif

            </section>

        @endisset

    </main>

</div>

</body>
</html>