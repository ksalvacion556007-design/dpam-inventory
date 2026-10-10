<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Suppliers - DPAM IMS</title>

    <style>
        :root {
            --bg: #f1f5f9;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --sidebar: #0b1220;
            --accent: #2563eb;
            --accent-dark: #1d4ed8;
            --danger: #dc2626;
            --success: #059669;
            --warn: #d97706;
        }

        * { box-sizing: border-box; }

        [hidden] { display: none !important; }

        body { margin: 0; font-family: "Segoe UI", system-ui, -apple-system, Roboto, Arial, sans-serif; background: var(--bg); color: var(--ink); }

        a { color: inherit; text-decoration: none; }

        h3 { font-size: 15px; margin: 0 0 10px; }

        /* SIDEBAR */
        .layout { display: flex; min-height: 100vh; }

        .sidebar { width: 256px; flex-shrink: 0; background: var(--sidebar); color: #fff; padding: 24px 14px; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; overflow-y: auto; }

        .brand { font-size: 20px; font-weight: 700; padding: 0 12px; }

        .subtitle { font-size: 12px; color: #94a3b8; line-height: 1.5; padding: 0 12px; margin: 4px 0 26px; }

        .menu-title { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .08em; margin: 6px 12px 8px; }

        .menu a, .logout-button { display: flex; align-items: center; gap: 11px; width: 100%; padding: 11px 12px; margin-bottom: 4px; border-radius: 8px; color: #cbd5e1; background: transparent; border: none; font-size: 14px; font-family: inherit; cursor: pointer; text-align: left; transition: background .15s, color .15s; }

        .menu a svg, .logout-button svg { width: 18px; height: 18px; flex-shrink: 0; }

        .menu a:hover, .logout-button:hover { background: #1e293b; color: #fff; }

        .menu a.active { background: var(--accent); color: #fff; font-weight: 600; }

        .logout-form { margin-top: auto; padding-top: 24px; }

        /* MAIN */
        .main { flex: 1; min-width: 0; margin-left: 256px; padding: 30px 34px; }

        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 26px; flex-wrap: wrap; }

        .topbar-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

        .page-title { margin: 0; font-size: 26px; font-weight: 700; }

        .page-description { margin-top: 5px; color: var(--muted); font-size: 14px; }

        .role-badge { background: #dbeafe; color: #1e40af; padding: 7px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; letter-spacing: .05em; }

        /* BUTTONS */
        .btn { border: none; border-radius: 8px; padding: 10px 16px; cursor: pointer; font-size: 14px; font-weight: 600; font-family: inherit; display: inline-block; transition: background .15s; text-align: center; }
        .btn:disabled { opacity: .6; cursor: not-allowed; }
        .btn-sm { padding: 7px 11px; font-size: 12px; }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover:not(:disabled) { background: var(--accent-dark); }
        .btn-secondary { background: #334155; color: #fff; }
        .btn-secondary:hover:not(:disabled) { background: #1e293b; }
        .btn-success { background: #16a34a; color: #fff; }
        .btn-success:hover:not(:disabled) { background: #15803d; }
        .btn-warning { background: var(--warn); color: #fff; }
        .btn-warning:hover:not(:disabled) { background: #b45309; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover:not(:disabled) { background: #b91c1c; }
        .btn-cancel { background: #e2e8f0; color: #334155; }
        .btn-cancel:hover:not(:disabled) { background: #cbd5e1; }

        /* ALERTS */
        .alert-success, .alert-error, .warning-notice { padding: 12px 15px; border-radius: 10px; margin-bottom: 18px; font-size: 13px; line-height: 1.5; border: 1px solid; }
        .alert-success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
        .alert-error ul { margin: 8px 0 0 20px; padding: 0; }
        .warning-notice { background: #fffbeb; color: #92400e; border-color: #fde68a; }

        /* FILTER */
        .filter-box { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 16px; margin-bottom: 18px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .filter-box input, .filter-box select { padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: #fff; font-family: inherit; }
        .filter-box input[type=text] { min-width: 300px; flex: 1; }
        .filter-box input:focus, .filter-box select:focus { border-color: var(--accent); }

        /* TABLE */
        .table-panel { background: #fff; border: 1px solid var(--line); border-radius: 14px; overflow: hidden; margin-bottom: 22px; }
        .table-header { padding: 16px 22px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .table-header h2 { margin: 0; font-size: 16px; }
        .table-count { font-size: 13px; color: var(--muted); }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid var(--line); text-align: left; font-size: 13px; vertical-align: middle; white-space: nowrap; }
        th { color: var(--muted); font-weight: 600; background: #f8fafc; }
        tbody tr:hover td { background: #fafbfd; }
        tbody tr:last-child td { border-bottom: none; }
        .empty-state { color: var(--muted); font-size: 14px; padding: 36px 20px; text-align: center; white-space: normal; }
        .secondary-text { color: var(--muted); font-size: 12px; margin-top: 3px; }
        .actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .muted { color: var(--muted); }

        /* PAGINATION */
        .pager { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; padding: 12px 22px; border-top: 1px solid var(--line); font-size: 13px; color: var(--muted); }
        .pager-buttons { display: flex; gap: 8px; align-items: center; }

        /* BADGES */
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .status-inactive { background: #fef3c7; color: #92400e; }
        .status-active { background: #dcfce7; color: #166534; }
        .status-archived { background: #e5e7eb; color: #374151; }

        /* MODAL */
        .modal { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, .55); align-items: center; justify-content: center; padding: 20px; z-index: 1000; }
        .modal.show { display: flex; }
        .modal-content { background: #fff; width: 100%; max-width: 700px; max-height: 92vh; overflow-y: auto; border-radius: 16px; box-shadow: 0 25px 60px rgba(0, 0, 0, .3); }
        .modal-medium { max-width: 820px; }
        .modal-small { max-width: 480px; }
        .modal-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 18px 22px; border-bottom: 1px solid var(--line); position: sticky; top: 0; background: #fff; z-index: 2; }
        .modal-header h2 { margin: 0; font-size: 18px; }
        .modal-body { padding: 22px; }
        .close-x { border: none; background: transparent; font-size: 26px; line-height: 1; cursor: pointer; color: var(--muted); border-radius: 6px; width: 34px; height: 34px; flex-shrink: 0; }
        .close-x:hover { background: #f1f5f9; color: var(--ink); }

        /* FORM */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 7px; color: #334155; }
        .form-group small { display: block; color: var(--muted); font-weight: 400; margin-top: 5px; line-height: 1.4; font-size: 12px; }
        .form-group input[type=text], .form-group input[type=email], .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; font-family: inherit; background: #fff; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0 16px; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; flex-wrap: wrap; }
        .req { color: var(--danger); font-weight: 700; margin-left: 2px; }
        .legend-req { font-size: 12px; color: var(--muted); margin: 0 0 14px; }
        .field-error { display: block; color: #b91c1c; font-size: 12px; margin-top: 5px; font-weight: 600; }

        /* DETAILS */
        .detail-row { display: flex; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .detail-label { display: block; color: var(--muted); font-size: 12px; width: 170px; flex-shrink: 0; }
        .detail-value { font-weight: 600; color: var(--ink); font-size: 14px; white-space: normal; flex: 1; }

        /* SECTIONS, CHIPS, PICKERS */
        .section-block { margin-top: 22px; }
        .section-block:first-of-type { margin-top: 0; }
        .section-heading { font-size: 12px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; margin: 0 0 12px; padding-bottom: 8px; border-bottom: 2px solid var(--line); }
        .chip-sup { display: inline-block; background: #f1f5f9; border: 1px solid #e2e8f0; color: #334155; border-radius: 6px; padding: 3px 9px; font-size: 12px; margin: 0 5px 5px 0; white-space: nowrap; font-family: inherit; }
        .chip-more { background: #eff6ff; border-color: #bfdbfe; color: var(--accent); cursor: pointer; font-weight: 600; }
        .chip-more:hover { background: #dbeafe; }
        .chip-edit { display: inline-flex; align-items: center; gap: 6px; }
        .chip-edit button { border: none; background: transparent; cursor: pointer; color: var(--muted); font-size: 15px; line-height: 1; padding: 0; }
        .chip-edit button:hover { color: var(--danger); }
        .brand-cell { white-space: normal; min-width: 150px; max-width: 260px; }
        .brand-box { border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; background: #fff; }
        .brand-box:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
        .brand-box input[type=text] { border: none !important; box-shadow: none !important; padding: 4px 2px !important; width: 100% !important; min-width: 140px; outline: none; font-family: inherit; font-size: 14px; }
        .picker { border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; }
        .picker-search { display: block; width: 100% !important; border: none !important; border-bottom: 1px solid var(--line) !important; border-radius: 0 !important; box-shadow: none !important; }
        .picker-list { max-height: 200px; overflow-y: auto; }
        .pick-row { display: flex; gap: 10px; align-items: flex-start; padding: 8px 12px; cursor: pointer; font-size: 13px; border-bottom: 1px solid #f1f5f9; margin: 0 !important; font-weight: 400 !important; }
        .pick-row:hover { background: #f8fafc; }
        .pick-row input { margin-top: 3px; flex-shrink: 0; }
        .picker-empty { padding: 14px 16px; font-size: 13px; color: var(--muted); }
        .picker-foot { padding: 7px 12px; font-size: 12px; color: var(--muted); background: #f8fafc; border-top: 1px solid var(--line); }
        .rel-table { border: 1px solid var(--line); border-radius: 10px; overflow: auto; max-height: 280px; }
        .rel-table th { position: sticky; top: 0; z-index: 1; }
        .rel-empty { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 14px 16px; font-size: 13px; color: var(--muted); }

        /* RESPONSIVE */
        @media (max-width: 800px) {
            .layout { flex-direction: column; }
            .sidebar { position: relative; width: 100%; }
            .main { margin-left: 0; padding: 20px; }
            .form-grid { grid-template-columns: 1fr; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .filter-box input[type=text] { min-width: 100%; }
            .detail-row { flex-direction: column; gap: 4px; }
            .detail-label { width: auto; }
        }

        /* PRINT */
        @media print {
            .sidebar, .topbar-actions, .filter-box, .modal, .actions, .pager, .alert-success, .alert-error { display: none !important; }
            .main { margin-left: 0; padding: 0; }
            body { background: #fff; }
            .table-wrapper { overflow: visible; }
            .table-panel { box-shadow: none; border: 1px solid #ccc; }
        }
    </style>
</head>

<body>

@php
    // Input that must be restored after a failed Add / Edit validation.
    $oldInput = \Illuminate\Support\Arr::only(old(), [
        '_form', '_edit_id',
        'supplier_name', 'contact_person', 'contact_number',
        'email', 'address', 'status',
        'brands', 'product_ids',
    ]);

    // Add / Edit validation errors are shown inside the modal.
    $formFailed = $errors->any() && in_array(old('_form'), ['add', 'edit'], true);

    $statusCounts = collect($supplierRows)->countBy('status');
@endphp

<div class="layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="brand">DPAM IMS</div>

        <div class="subtitle">Industrial Supplies & Services Inc.</div>

        <div class="menu-title">Owner</div>

        <nav class="menu">

            <a href="{{ route('owner.dashboard') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                Dashboard
            </a>

            <a href="{{ route('owner.sales-inventory') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/></svg>
                Sales & Inventory
            </a>

            <a href="{{ route('owner.suppliers') }}" class="active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="12" height="9" rx="1"/><path d="M14 10h4l3 3v3h-7"/><circle cx="7" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/></svg>
                Suppliers
            </a>

            <a href="{{ route('owner.purchase-orders') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.5 10h10L20 7H7"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></svg>
                Purchase Orders
            </a>

            <a href="{{ route('owner.reports') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-8M22 20H2"/></svg>
                Reports
            </a>

            <a href="{{ route('owner.archive') }}" class="">
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

    {{-- MAIN CONTENT --}}
    <main class="main">

        <div class="topbar">
            <div>
                <h1 class="page-title">Suppliers</h1>
                <div class="page-description">Active suppliers, the brands they supply, and the products they supply to DPAM.</div>
            </div>
            <div class="topbar-actions">
                <button type="button" class="btn btn-primary" onclick="openAdd()">+ Add Supplier</button>
                <div class="role-badge">OWNER</div>
            </div>
        </div>


        @if(session('success'))
            <div class="alert-success" role="status">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert-error" role="alert">{{ session('error') }}</div>
        @endif

        @if(session('warning'))
            <div class="warning-notice" role="alert">{{ session('warning') }}</div>
        @endif

        {{-- Add / Edit errors are shown inside their own modal. --}}
        @if($errors->any() && !$formFailed)
            <div class="alert-error" role="alert">
                <strong>Please check the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="filter-box">
            <input type="text" id="supplierSearch" placeholder="Search supplier, contact person, contact number, brand or product...">

            <select id="statusView" aria-label="Show suppliers">
                <option value="active">Active ({{ $statusCounts['active'] ?? 0 }})</option>
                <option value="inactive">Inactive ({{ $statusCounts['inactive'] ?? 0 }})</option>
                <option value="archived">Archived ({{ $statusCounts['archived'] ?? 0 }})</option>
            </select>
        </div>

        <section class="table-panel">

            <div class="table-header">
                <h2 id="tableTitle">Active Suppliers</h2>
                <div class="table-count" id="supplierCount">0 supplier(s)</div>
            </div>

            <div class="table-wrapper">
                <table>

                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Contact Person</th>
                            <th>Contact</th>
                            <th>Brands</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="supplierTableBody">

                        @foreach($supplierRows as $s)

                            @php
                                $brandList = $s['brands'];
                                $productCount = count($s['products']);

                                $searchText = mb_strtolower(implode(' | ', array_merge(
                                    [$s['supplier_name'], $s['contact_person'] ?? '', $s['contact_number'] ?? ''],
                                    $brandList,
                                    array_column($s['products'], 'name')
                                )));
                            @endphp

                            <tr data-id="{{ $s['id'] }}" data-status="{{ $s['status'] }}" data-search="{{ $searchText }}">

                                <td>
                                    <strong>{{ $s['supplier_name'] }}</strong>
                                    @if($s['address'])
                                        <div class="secondary-text">{{ $s['address'] }}</div>
                                    @endif
                                </td>

                                <td>{{ $s['contact_person'] ?: '—' }}</td>

                                <td>
                                    @if($s['contact_number'] || $s['email'])
                                        @if($s['contact_number'])<div>{{ $s['contact_number'] }}</div>@endif
                                        @if($s['email'])<div class="secondary-text">{{ $s['email'] }}</div>@endif
                                    @else
                                        —
                                    @endif
                                </td>

                                <td class="brand-cell">
                                    @if(count($brandList))
                                        @foreach(array_slice($brandList, 0, 3) as $brandName)
                                            <span class="chip-sup">{{ $brandName }}</span>
                                        @endforeach
                                        @if(count($brandList) > 3)
                                            <button type="button" class="chip-sup chip-more" onclick="viewSupplier({{ $s['id'] }})">+{{ count($brandList) - 3 }} more</button>
                                        @endif
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($productCount)
                                        <button type="button" class="chip-sup chip-more" onclick="viewSupplier({{ $s['id'] }})">{{ $productCount }} {{ $productCount === 1 ? 'Product' : 'Products' }}</button>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    <span class="status-badge status-{{ $s['status'] }}">{{ ucfirst($s['status']) }}</span>
                                </td>

                                <td>
                                    <div class="actions">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="viewSupplier({{ $s['id'] }})">View</button>

                                        @if($s['status'] !== 'archived')
                                            <button type="button" class="btn btn-sm btn-warning" onclick="openEdit({{ $s['id'] }})">Edit</button>
                                        @endif

                                        @if($s['status'] === 'inactive')
                                            <button type="button" class="btn btn-sm btn-success" onclick="restoreSupplier({{ $s['id'] }})">Restore</button>
                                        @endif

                                        @if($s['status'] !== 'archived')
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="archiveSupplier({{ $s['id'] }})">Archive</button>
                                        @endif
                                    </div>
                                </td>

                            </tr>

                        @endforeach

                        <tr id="noMatchRow" hidden>
                            <td colspan="7"><div class="empty-state" id="noMatchText">No suppliers found.</div></td>
                        </tr>

                    </tbody>

                </table>
            </div>

            <div class="pager" id="pager" hidden>
                <div id="pagerInfo"></div>
                <div class="pager-buttons">
                    <button type="button" class="btn btn-sm btn-cancel" id="prevPage">Previous</button>
                    <span id="pageLabel"></span>
                    <button type="button" class="btn btn-sm btn-cancel" id="nextPage">Next</button>
                </div>
            </div>

        </section>

    </main>

</div>


{{-- ADD / EDIT SUPPLIER MODAL --}}
<div id="supplierModal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h2 id="supplierModalTitle">Add Supplier</h2>
            <button type="button" class="close-x" aria-label="Close" onclick="closeModal('supplierModal')">&times;</button>
        </div>

        <form id="supplierForm" action="{{ route('owner.suppliers.store') }}" method="POST">

            @csrf

            <input type="hidden" name="_method" value="PUT" id="methodField" disabled>
            <input type="hidden" name="_form" id="formKind" value="add">
            <input type="hidden" name="_edit_id" id="editId" value="">
            <input type="hidden" name="relations_submitted" value="1">

            <div class="modal-body">

                <p class="legend-req"><span class="req" aria-hidden="true">*</span> Required field</p>

                @if($formFailed)
                    <div class="alert-error srv-err" role="alert">
                        <strong>Please check the following:</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- SUPPLIER INFORMATION --}}
                <div class="section-block">
                    <h3 class="section-heading">Supplier Information</h3>

                    <div class="form-group">
                        <label for="f_name">Supplier Name <span class="req" aria-hidden="true">*</span></label>
                        <input type="text" id="f_name" name="supplier_name" maxlength="255" required>
                        @if($formFailed)@error('supplier_name')<span class="field-error srv-err">{{ $message }}</span>@enderror @endif
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="f_person">Contact Person <span class="req" aria-hidden="true">*</span></label>
                            <input type="text" id="f_person" name="contact_person" maxlength="255" required>
                            @if($formFailed)@error('contact_person')<span class="field-error srv-err">{{ $message }}</span>@enderror @endif
                        </div>

                        <div class="form-group">
                            <label for="f_number">Contact Number <span class="req" aria-hidden="true">*</span></label>
                            <input type="text" id="f_number" name="contact_number" maxlength="50" required>
                            @if($formFailed)@error('contact_number')<span class="field-error srv-err">{{ $message }}</span>@enderror @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="f_email">Email</label>
                        <input type="email" id="f_email" name="email" maxlength="255">
                        @if($formFailed)@error('email')<span class="field-error srv-err">{{ $message }}</span>@enderror @endif
                    </div>

                    <div class="form-group">
                        <label for="f_address">Address</label>
                        <textarea id="f_address" name="address" maxlength="1000"></textarea>
                        @if($formFailed)@error('address')<span class="field-error srv-err">{{ $message }}</span>@enderror @endif
                    </div>

                    <div class="form-group">
                        <label for="f_status">Status <span class="req" aria-hidden="true">*</span></label>
                        <select id="f_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <small>Inactive suppliers cannot be used for new Purchase Orders. Their history is kept.</small>
                        @if($formFailed)@error('status')<span class="field-error srv-err">{{ $message }}</span>@enderror @endif
                    </div>
                </div>

                {{-- BRANDS SUPPLIED --}}
                <div class="section-block">
                    <h3 class="section-heading">Brands Supplied</h3>

                    <div class="form-group">
                        <div class="brand-box">
                            <span id="brandChips"></span>
                            <input type="text" id="brandInput" list="brandList" maxlength="255" placeholder="Search or type a brand, press Enter" autocomplete="off">
                        </div>
                        <small>Press Enter or comma to add. Click × on a brand to remove it.</small>
                        @if($formFailed)@error('brands.*')<span class="field-error srv-err">{{ $message }}</span>@enderror @endif
                        @if($formFailed)@error('brands')<span class="field-error srv-err">{{ $message }}</span>@enderror @endif
                    </div>

                    <datalist id="brandList">
                        @foreach($brandOptions as $brandOption)
                            <option value="{{ $brandOption }}"></option>
                        @endforeach
                    </datalist>
                </div>

                {{-- PRODUCTS SUPPLIED TO DPAM --}}
                <div class="section-block">
                    <h3 class="section-heading">Products Supplied to DPAM</h3>

                    <div class="form-group">
                        <div class="picker">
                            <input type="text" class="picker-search" id="prodSearch" placeholder="Search products..." autocomplete="off">
                            <div class="picker-list" id="prodList"></div>
                            <div class="picker-foot"><span id="prodCount">0 selected</span></div>
                        </div>
                        <small>Purchase cost is entered on each Purchase Order, not here.</small>
                        @if($formFailed)@error('product_ids.*')<span class="field-error srv-err">{{ $message }}</span>@enderror @endif
                        @if($formFailed)@error('product_ids')<span class="field-error srv-err">{{ $message }}</span>@enderror @endif
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('supplierModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn" data-label="Add Supplier">Add Supplier</button>
                </div>

            </div>

        </form>

    </div>
</div>


{{-- VIEW SUPPLIER MODAL --}}
<div id="viewSupplierModal" class="modal">
    <div class="modal-content modal-medium">

        <div class="modal-header">
            <h2 id="viewSupplierTitle">Supplier Details</h2>
            <button type="button" class="close-x" aria-label="Close" onclick="closeModal('viewSupplierModal')">&times;</button>
        </div>

        <div class="modal-body">

            <div class="section-block">
                <h3 class="section-heading">Supplier Information</h3>

                <div class="detail-row"><div class="detail-label">Supplier Name</div><div class="detail-value" id="viewSupplierName">—</div></div>
                <div class="detail-row"><div class="detail-label">Contact Person</div><div class="detail-value" id="viewContactPerson">—</div></div>
                <div class="detail-row"><div class="detail-label">Contact Number</div><div class="detail-value" id="viewContactNumber">—</div></div>
                <div class="detail-row"><div class="detail-label">Email</div><div class="detail-value" id="viewEmail">—</div></div>
                <div class="detail-row"><div class="detail-label">Address</div><div class="detail-value" id="viewAddress">—</div></div>
                <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value" id="viewStatus">—</div></div>
            </div>

            <div class="section-block">
                <h3 class="section-heading">Brands Supplied</h3>
                <div id="viewBrands"></div>
            </div>

            <div class="section-block">
                <h3 class="section-heading">Products Supplied to DPAM</h3>
                <div id="viewProducts"></div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('viewSupplierModal')">Close</button>
            </div>

        </div>

    </div>
</div>


{{-- ARCHIVE SUPPLIER MODAL --}}
<div id="archiveSupplierModal" class="modal">
    <div class="modal-content modal-small">

        <div class="modal-header">
            <h2>Archive Supplier</h2>
            <button type="button" class="close-x" aria-label="Close" onclick="closeModal('archiveSupplierModal')">&times;</button>
        </div>

        <form id="archiveSupplierForm" method="POST">

            @csrf
            @method('PATCH')

            <div class="modal-body">

                <p>Are you sure you want to archive <strong id="archiveSupplierName"></strong>?</p>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('archiveSupplierModal')">Cancel</button>
                    <button type="submit" class="btn btn-danger" id="archiveBtn" data-label="Archive Supplier">Archive Supplier</button>
                </div>

            </div>

        </form>

    </div>
</div>


<script>

    const suppliers = @json($supplierRows);
    const allProducts = @json($productOptions);
    const OLD = @json($oldInput);

    const URLS = {
        store: @json(route('owner.suppliers.store')),
        update: @json(route('owner.suppliers.update', ['supplier' => '__ID__'])),
        archive: @json(route('owner.suppliers.archive', ['supplier' => '__ID__']))
    };

    const $ = function (id) { return document.getElementById(id); };

    /* ---------- modals ---------- */
    function openModal(id) {
        const modal = $(id);
        if (!modal) return;
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const modal = $(id);
        if (!modal) return;
        modal.classList.remove('show');
        if (!document.querySelector('.modal.show')) document.body.style.overflow = '';
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    document.addEventListener('click', function (event) {
        if (event.target.classList && event.target.classList.contains('modal')) {
            event.target.classList.remove('show');
            if (!document.querySelector('.modal.show')) document.body.style.overflow = '';
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('.modal.show').forEach(function (modal) { modal.classList.remove('show'); });
        document.body.style.overflow = '';
    });

    function getSupplier(id) {
        return suppliers.find(function (s) { return Number(s.id) === Number(id); });
    }

    function clearServerErrors() {
        document.querySelectorAll('#supplierModal .srv-err').forEach(function (el) { el.remove(); });
    }

    /* ---------- brands supplied (supplier_brands) ---------- */
    function addBrand(raw) {
        const name = String(raw || '').replace(/,/g, ' ').replace(/\s+/g, ' ').trim();
        if (!name) return;

        const box = $('brandChips');

        const exists = Array.prototype.some.call(box.querySelectorAll('input'), function (input) {
            return input.value.toLowerCase() === name.toLowerCase();
        });
        if (exists) return;

        const chip = document.createElement('span');
        chip.className = 'chip-sup chip-edit';
        chip.appendChild(document.createTextNode(name));

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'brands[]';
        hidden.value = name;
        chip.appendChild(hidden);

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.setAttribute('aria-label', 'Remove brand');
        remove.innerHTML = '&times;';
        remove.addEventListener('click', function () { chip.remove(); });
        chip.appendChild(remove);

        box.appendChild(chip);
    }

    function setBrands(names) {
        $('brandChips').innerHTML = '';
        (names || []).forEach(addBrand);
        $('brandInput').value = '';
    }

    (function () {
        const input = $('brandInput');

        function commit() { addBrand(input.value); input.value = ''; }

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ',') { event.preventDefault(); commit(); }
        });
        input.addEventListener('change', commit);
        input.addEventListener('blur', commit);
    })();

    /* ---------- products supplied (product_supplier), searchable ---------- */
    function updatePickCount() {
        $('prodCount').textContent = $('prodList').querySelectorAll('input:checked').length + ' selected';
    }

    function renderPicker(selectedIds, extraProducts) {
        const selected = new Set((selectedIds || []).map(Number));
        const pool = new Map();

        allProducts.forEach(function (p) {
            pool.set(Number(p.id), { id: Number(p.id), name: p.name, brand: p.brand, inactive: false });
        });

        // Keep products already linked to this supplier even if they are inactive, so saving never drops them.
        (extraProducts || []).forEach(function (p) {
            if (!pool.has(Number(p.id))) pool.set(Number(p.id), { id: Number(p.id), name: p.name, brand: p.brand, inactive: true });
        });

        const list = Array.from(pool.values()).sort(function (a, b) { return String(a.name).localeCompare(String(b.name)); });
        const box = $('prodList');

        box.innerHTML = list.length ? '' : '<div class="picker-empty">No products available.</div>';

        list.forEach(function (p) {
            const row = document.createElement('label');
            row.className = 'pick-row';
            row.dataset.search = (p.name + ' ' + (p.brand || '')).toLowerCase();
            row.innerHTML =
                '<input type="checkbox" name="product_ids[]" value="' + p.id + '"' + (selected.has(p.id) ? ' checked' : '') + '>' +
                '<span><strong>' + escapeHtml(p.name) + '</strong>' +
                (p.brand ? ' <span class="muted">· ' + escapeHtml(p.brand) + '</span>' : '') +
                (p.inactive ? ' <span class="muted">(inactive product)</span>' : '') + '</span>';
            box.appendChild(row);
        });

        $('prodSearch').value = '';
        updatePickCount();
    }

    (function () {
        const search = $('prodSearch');

        search.addEventListener('keydown', function (event) { if (event.key === 'Enter') event.preventDefault(); });

        search.addEventListener('input', function () {
            const q = search.value.toLowerCase().trim();
            $('prodList').querySelectorAll('.pick-row').forEach(function (row) {
                row.style.display = (row.dataset.search || '').includes(q) ? '' : 'none';
            });
        });

        $('prodList').addEventListener('change', updatePickCount);
    })();

    /* ---------- add / edit form ---------- */
    function resetSaveButton() {
        const form = $('supplierForm');
        delete form.dataset.busy;
        const button = $('saveBtn');
        button.disabled = false;
        button.textContent = button.dataset.label;
    }

    function setMode(kind, id) {
        const edit = kind === 'edit';

        $('formKind').value = kind;
        $('editId').value = edit ? id : '';
        $('methodField').disabled = !edit;
        $('supplierForm').action = edit ? URLS.update.replace('__ID__', id) : URLS.store;
        $('supplierModalTitle').textContent = edit ? 'Edit Supplier' : 'Add Supplier';

        const label = edit ? 'Save Changes' : 'Add Supplier';
        $('saveBtn').dataset.label = label;
        $('saveBtn').textContent = label;
    }

    function fillForm(d, extraProducts) {
        $('f_name').value = d.supplier_name || '';
        $('f_person').value = d.contact_person || '';
        $('f_number').value = d.contact_number || '';
        $('f_email').value = d.email || '';
        $('f_address').value = d.address || '';
        $('f_status').value = d.status === 'inactive' ? 'inactive' : 'active';

        setBrands(d.brands || []);
        renderPicker(d.product_ids || [], extraProducts || []);
    }

    function openAdd() {
        clearServerErrors();
        resetSaveButton();
        setMode('add');
        fillForm({ status: 'active', brands: [], product_ids: [] });
        openModal('supplierModal');
    }

    function openEdit(id, old, forceStatus) {
        const supplier = getSupplier(id);
        if (!supplier) return;

        resetSaveButton();
        setMode('edit', id);

        if (old) {
            fillForm({
                supplier_name: old.supplier_name,
                contact_person: old.contact_person,
                contact_number: old.contact_number,
                email: old.email,
                address: old.address,
                status: old.status,
                brands: old.brands || [],
                product_ids: old.product_ids || []
            }, supplier.products);
        } else {
            clearServerErrors();
            fillForm({
                supplier_name: supplier.supplier_name,
                contact_person: supplier.contact_person,
                contact_number: supplier.contact_number,
                email: supplier.email,
                address: supplier.address,
                status: forceStatus || supplier.status,
                brands: supplier.brands,
                product_ids: supplier.products.map(function (p) { return p.id; })
            }, supplier.products);
        }

        openModal('supplierModal');
    }

    /* Restore = open Edit with Status set to Active (uses the existing update route). */
    function restoreSupplier(id) {
        openEdit(id, null, 'active');
    }

    /* Duplicate-submit protection + loading state */
    $('supplierForm').addEventListener('submit', function (event) {
        const form = $('supplierForm');
        if (form.dataset.busy) { event.preventDefault(); return; }
        form.dataset.busy = '1';
        const button = $('saveBtn');
        setTimeout(function () { button.disabled = true; button.textContent = 'Saving…'; }, 0);
    });

    $('archiveSupplierForm').addEventListener('submit', function (event) {
        const form = $('archiveSupplierForm');
        if (form.dataset.busy) { event.preventDefault(); return; }
        form.dataset.busy = '1';
        const button = $('archiveBtn');
        setTimeout(function () { button.disabled = true; button.textContent = 'Archiving…'; }, 0);
    });

    window.addEventListener('pageshow', function () {
        resetSaveButton();
        const form = $('archiveSupplierForm');
        delete form.dataset.busy;
        $('archiveBtn').disabled = false;
        $('archiveBtn').textContent = $('archiveBtn').dataset.label;
    });

    /* ---------- view ---------- */
    function viewSupplier(id) {
        const supplier = getSupplier(id);
        if (!supplier) return;

        $('viewSupplierTitle').textContent = supplier.supplier_name || 'Supplier Details';
        $('viewSupplierName').textContent = supplier.supplier_name || '—';
        $('viewContactPerson').textContent = supplier.contact_person || '—';
        $('viewContactNumber').textContent = supplier.contact_number || '—';
        $('viewEmail').textContent = supplier.email || '—';
        $('viewAddress').textContent = supplier.address || '—';

        const status = supplier.status || 'active';
        $('viewStatus').innerHTML = '<span class="status-badge status-' + escapeHtml(status) + '">' +
            escapeHtml(status.charAt(0).toUpperCase() + status.slice(1)) + '</span>';

        const brands = supplier.brands || [];
        $('viewBrands').innerHTML = brands.length
            ? brands.map(function (b) { return '<span class="chip-sup">' + escapeHtml(b) + '</span>'; }).join('')
            : '<div class="rel-empty">No brands recorded for this supplier.</div>';

        const prods = supplier.products || [];
        $('viewProducts').innerHTML = prods.length
            ? '<div class="rel-table"><table><thead><tr><th>Product</th><th>Brand</th><th>Category</th></tr></thead><tbody>' +
              prods.map(function (p) {
                  return '<tr><td><strong>' + escapeHtml(p.name) + '</strong>' +
                         (p.status && p.status !== 'active' ? ' <span class="muted">(inactive)</span>' : '') +
                         '</td><td>' + escapeHtml(p.brand || '—') + '</td><td>' + escapeHtml(p.category || '—') + '</td></tr>';
              }).join('') + '</tbody></table></div>'
            : '<div class="rel-empty">No products recorded for this supplier.</div>';

        openModal('viewSupplierModal');
    }

    /* ---------- archive ---------- */
    function archiveSupplier(id) {
        const supplier = getSupplier(id);
        if (!supplier) return;

        $('archiveSupplierForm').action = URLS.archive.replace('__ID__', supplier.id);
        $('archiveSupplierName').textContent = supplier.supplier_name;

        openModal('archiveSupplierModal');
    }

    /* ---------- status view + search + pagination (15 per page) ---------- */
    const PER_PAGE = 15;
    let currentPage = 1;

    const searchInput = $('supplierSearch');
    const statusView = $('statusView');
    const allRows = Array.prototype.slice.call(document.querySelectorAll('#supplierTableBody tr[data-id]'));

    const VIEW_TITLES = { active: 'Active Suppliers', inactive: 'Inactive Suppliers', archived: 'Archived Suppliers' };

    function renderTable(resetPage) {
        const q = searchInput.value.toLowerCase().trim();
        const view = statusView.value;

        const matched = allRows.filter(function (row) {
            return row.dataset.status === view && (row.dataset.search || '').includes(q);
        });

        if (resetPage) currentPage = 1;

        const pages = Math.max(1, Math.ceil(matched.length / PER_PAGE));
        if (currentPage > pages) currentPage = pages;

        const start = (currentPage - 1) * PER_PAGE;
        const visible = matched.slice(start, start + PER_PAGE);

        allRows.forEach(function (row) { row.style.display = 'none'; });
        visible.forEach(function (row) { row.style.display = ''; });

        $('tableTitle').textContent = VIEW_TITLES[view] || 'Suppliers';
        $('supplierCount').textContent = matched.length + ' supplier(s)';

        const noMatch = $('noMatchRow');
        noMatch.hidden = matched.length > 0;

        if (matched.length === 0) {
            $('noMatchText').textContent = q
                ? 'No suppliers match your search.'
                : (view === 'active'
                    ? 'No active suppliers. Add a supplier to begin.'
                    : 'No ' + view + ' suppliers.');
        }

        const pager = $('pager');
        pager.hidden = matched.length <= PER_PAGE;

        if (matched.length > 0) {
            $('pagerInfo').textContent = 'Showing ' + (start + 1) + '–' + (start + visible.length) + ' of ' + matched.length;
        }

        $('pageLabel').textContent = 'Page ' + currentPage + ' of ' + pages;
        $('prevPage').disabled = currentPage <= 1;
        $('nextPage').disabled = currentPage >= pages;
    }

    searchInput.addEventListener('input', function () { renderTable(true); });
    statusView.addEventListener('change', function () { renderTable(true); });
    $('prevPage').addEventListener('click', function () { currentPage--; renderTable(false); });
    $('nextPage').addEventListener('click', function () { currentPage++; renderTable(false); });

    renderTable(true);

    /* ---------- re-open the form that failed validation, with old input restored ---------- */
    (function () {
        if (!OLD || !OLD._form) return;

        if (OLD._form === 'add') {
            resetSaveButton();
            setMode('add');
            fillForm(OLD, []);
            openModal('supplierModal');
        } else if (OLD._form === 'edit' && OLD._edit_id) {
            openEdit(OLD._edit_id, OLD);
        }
    })();

</script>

</body>

</html>