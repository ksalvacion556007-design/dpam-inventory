<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventory - Secretary | DPAM IMS</title>

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
        .action-row { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:22px; }

        /* ALERTS */
        .alert { padding:14px 16px; border-radius:10px; margin-bottom:20px; font-size:13px; }
        .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
        .alert-error { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
        .alert ul { margin:8px 0 0; padding-left:20px; }

        /* PANELS */
        .panel { background:#fff; border:1px solid var(--line); border-radius:14px; padding:22px; margin-bottom:22px; }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
        .panel h2 { margin:0; font-size:17px; }
        .count-text { font-size:13px; color:var(--muted); }
        .table-panel { padding:0; overflow:hidden; }
        .table-panel > .panel-head { padding:18px 22px; margin:0; border-bottom:1px solid var(--line); }

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
        .pill-gray { background:#e5e7eb; color:#374151; }
        .empty-state { color:var(--muted); font-size:14px; padding:40px 20px; text-align:center; }

        /* BUTTONS */
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; border:none; border-radius:8px; padding:10px 16px; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s; }
        .btn-primary { background:var(--accent); color:#fff; }
        .btn-primary:hover { background:var(--accent-dark); }
        .btn-danger { background:var(--danger); color:#fff; }
        .btn-danger:hover { background:#b91c1c; }
        .btn-ghost { background:#e2e8f0; color:#334155; }
        .btn-ghost:hover { background:#cbd5e1; }

        /* MODAL + FORM */
        .modal { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); align-items:center; justify-content:center; padding:20px; z-index:1000; }
        .modal.show { display:flex; }
        .modal-box { background:#fff; width:100%; max-width:680px; max-height:90vh; display:flex; flex-direction:column; border-radius:16px; box-shadow:0 25px 60px rgba(0,0,0,.3); overflow:hidden; }
        .modal-header { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:18px 22px; border-bottom:1px solid var(--line); }
        .modal-header h2 { margin:0; font-size:18px; }
        .close-button { border:none; background:transparent; font-size:26px; line-height:1; cursor:pointer; color:var(--muted); border-radius:6px; width:34px; height:34px; }
        .close-button:hover { background:#f1f5f9; color:var(--ink); }
        .modal-body { padding:22px; overflow:auto; }
        .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
        .form-group.full { grid-column:1 / -1; }
        .form-group label { display:block; font-size:12px; font-weight:600; color:#475569; margin-bottom:6px; }
        .req { color:var(--danger); margin-left:2px; }
        .form-control { width:100%; height:42px; border:1px solid #cbd5e1; border-radius:8px; padding:0 12px; background:#fff; font-size:13px; color:var(--ink); }
        textarea.form-control { height:auto; min-height:90px; padding:10px 12px; resize:vertical; }
        .form-control:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(37,99,235,.12); }
        .form-actions { margin-top:22px; display:flex; justify-content:flex-end; gap:10px; }

        /* RESPONSIVE */
        @media (max-width:800px) { .layout { flex-direction:column; } .sidebar { width:100%; height:auto; position:static; } .main { padding:20px; } .topbar { flex-direction:column; align-items:flex-start; } .form-grid { grid-template-columns:1fr; } }
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
            <a href="{{ route('secretary.inventory') }}" class="active">
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
                <h1 class="page-title">Inventory</h1>
                <div class="page-description">Current stock and inventory movements</div>
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

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- ACTIONS --}}
        <div class="action-row">
            <button type="button" class="btn btn-primary" onclick="openModal('stockInModal')">+ Stock In</button>
            <button type="button" class="btn btn-danger" onclick="openModal('stockOutModal')">Stock Out</button>
            <button type="button" class="btn btn-ghost" onclick="openModal('adjustModal')">Adjust Stock</button>
        </div>


        {{-- CURRENT INVENTORY --}}
        <section class="panel table-panel" id="panel-inventory">

            <div class="panel-head">
                <h2>Current Inventory</h2>
                <span class="count-text">{{ $products->count() }} product(s)</span>
            </div>

            <div class="table-wrapper">
                <table class="data-table">

                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th class="text-right">Current Stock</th>
                            <th class="text-right">Reorder Level</th>
                            <th>Stock Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($products as $product)

                            @php
                                $currentStock = $product->inventory?->current_stock ?? 0;
                                $reorderLevel = $product->reorder_level ?? 0;

                                if ($currentStock <= 0) {
                                    $status = 'Out of Stock';
                                    $statusClass = 'pill-red';
                                } elseif ($currentStock <= $reorderLevel) {
                                    $status = 'Low Stock';
                                    $statusClass = 'pill-amber';
                                } else {
                                    $status = 'Available';
                                    $statusClass = 'pill-green';
                                }
                            @endphp

                            <tr>
                                <td class="strong">{{ $product->product_name }}</td>
                                <td>{{ $product->category?->category_name ?? '—' }}</td>
                                <td>{{ $product->unit }}</td>
                                <td class="text-right strong">{{ number_format($currentStock) }}</td>
                                <td class="text-right">{{ number_format($reorderLevel) }}</td>
                                <td><span class="pill {{ $statusClass }}">{{ $status }}</span></td>
                            </tr>

                        @empty

                            <tr><td colspan="6" class="empty-state">No active products found.</td></tr>

                        @endforelse
                    </tbody>

                </table>
            </div>

        </section>


        {{-- MOVEMENT HISTORY --}}
        <section class="panel table-panel" id="panel-movements">

            <div class="panel-head">
                <h2>Inventory Movement History</h2>
                <span class="count-text">{{ $movements->count() }} movement(s)</span>
            </div>

            <div class="table-wrapper">
                <table class="data-table" style="min-width:1100px">

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Movement</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Stock Before</th>
                            <th class="text-right">Stock After</th>
                            <th>Supplier / Customer</th>
                            <th>Receipt Number</th>
                            <th>Performed By</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($movements as $movement)

                            <tr>
                                <td>{{ $movement->transaction_date?->format('M d, Y') ?? '—' }}</td>
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
                                <td class="text-right">
                                    @if($movement->quantity > 0)
                                        +{{ number_format($movement->quantity) }}
                                    @else
                                        {{ number_format($movement->quantity) }}
                                    @endif
                                </td>
                                <td class="text-right">{{ number_format($movement->stock_before) }}</td>
                                <td class="text-right">{{ number_format($movement->stock_after) }}</td>
                                <td>{{ $movement->supplier_customer ?? '—' }}</td>
                                <td>{{ $movement->receipt_number ?? '—' }}</td>
                                <td>{{ $movement->user?->name ?? '—' }}</td>
                            </tr>

                        @empty

                            <tr><td colspan="9" class="empty-state">No inventory movements recorded yet.</td></tr>

                        @endforelse
                    </tbody>

                </table>
            </div>

        </section>

    </main>

</div>


{{-- STOCK IN MODAL --}}
<div class="modal" id="stockInModal">
    <div class="modal-box">

        <div class="modal-header">
            <h2>Stock In</h2>
            <button type="button" class="close-button" onclick="closeModal('stockInModal')" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">

            <form action="{{ route('secretary.inventory.stock-in') }}" method="POST">

                @csrf

                <div class="form-grid">

                    <div class="form-group full">
                        <label>Product <span class="req">*</span></label>
                        <select name="product_id" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->product_name }} - Current Stock: {{ $product->inventory?->current_stock ?? 0 }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Transaction Date <span class="req">*</span></label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Quantity Received <span class="req">*</span></label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Supplier</label>
                        <input type="text" name="supplier_customer" class="form-control" placeholder="Supplier name">
                    </div>

                    <div class="form-group">
                        <label>Unit Cost</label>
                        <input type="number" name="unit_cost" class="form-control" min="0" step="0.01" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>Reason</label>
                        <input type="text" name="reason" class="form-control" value="Supplier delivery">
                    </div>

                    <div class="form-group">
                        <label>Purchase Order Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="Example: PO-2026-0001">
                    </div>

                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('stockInModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Stock In</button>
                </div>

            </form>

        </div>

    </div>
</div>


{{-- STOCK OUT MODAL --}}
<div class="modal" id="stockOutModal">
    <div class="modal-box">

        <div class="modal-header">
            <h2>Stock Out</h2>
            <button type="button" class="close-button" onclick="closeModal('stockOutModal')" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">

            <form action="{{ route('secretary.inventory.stock-out') }}" method="POST">

                @csrf

                <div class="form-grid">

                    <div class="form-group full">
                        <label>Product <span class="req">*</span></label>
                        <select name="product_id" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->product_name }} - Current Stock: {{ $product->inventory?->current_stock ?? 0 }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Transaction Date <span class="req">*</span></label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Quantity <span class="req">*</span></label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Customer Order Reference <span class="req">*</span></label>
                        <input type="text" name="customer_order_reference" class="form-control" placeholder="Customer Order reference" required>
                    </div>

                    <div class="form-group">
                        <label>Receipt Number <span class="req">*</span></label>
                        <input type="text" name="receipt_number" class="form-control" placeholder="Receipt number" required>
                    </div>

                    <div class="form-group">
                        <label>Received By <span class="req">*</span></label>
                        <input type="text" name="received_by" class="form-control" placeholder="Customer / recipient name" required>
                    </div>

                    <div class="form-group">
                        <label>Customer</label>
                        <input type="text" name="supplier_customer" class="form-control" placeholder="Customer name">
                    </div>

                    <div class="form-group">
                        <label>Unit Price</label>
                        <input type="number" name="unit_price" class="form-control" min="0" step="0.01" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label>Reason</label>
                        <input type="text" name="reason" class="form-control" value="Customer delivery / release">
                    </div>

                    <div class="form-group">
                        <label>Additional Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="Supporting reference">
                    </div>

                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('stockOutModal')">Cancel</button>
                    <button type="submit" class="btn btn-danger">Record Stock Out</button>
                </div>

            </form>

        </div>

    </div>
</div>


{{-- ADJUST STOCK MODAL --}}
<div class="modal" id="adjustModal">
    <div class="modal-box">

        <div class="modal-header">
            <h2>Adjust Stock</h2>
            <button type="button" class="close-button" onclick="closeModal('adjustModal')" aria-label="Close">&times;</button>
        </div>

        <div class="modal-body">

            <form action="{{ route('secretary.inventory.adjust') }}" method="POST">

                @csrf

                <div class="form-grid">

                    <div class="form-group full">
                        <label>Product <span class="req">*</span></label>
                        <select name="product_id" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->product_name }} - System Stock: {{ $product->inventory?->current_stock ?? 0 }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Transaction Date <span class="req">*</span></label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Actual Physical Count <span class="req">*</span></label>
                        <input type="number" name="actual_stock" class="form-control" min="0" required>
                    </div>

                    <div class="form-group full">
                        <label>Reason <span class="req">*</span></label>
                        <textarea name="reason" class="form-control" placeholder="Reason for the adjustment" required></textarea>
                    </div>

                    <div class="form-group full">
                        <label>Reference</label>
                        <input type="text" name="reference" class="form-control" placeholder="Example: Count Sheet">
                    </div>

                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('adjustModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Adjustment</button>
                </div>

            </form>

        </div>

    </div>
</div>


<script>

    function openModal(id)
    {
        const modal = document.getElementById(id);

        if (!modal) return;

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }


    function closeModal(id)
    {
        const modal = document.getElementById(id);

        if (!modal) return;

        modal.classList.remove('show');
        document.body.style.overflow = '';
    }


    document.querySelectorAll('.modal').forEach(function (modal)
    {
        modal.addEventListener('click', function (event)
        {
            if (event.target === modal)
            {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });


    document.addEventListener('keydown', function (event)
    {
        if (event.key !== 'Escape') return;

        document.querySelectorAll('.modal.show').forEach(function (modal) { modal.classList.remove('show'); });

        document.body.style.overflow = '';
    });

</script>

</body>

</html>