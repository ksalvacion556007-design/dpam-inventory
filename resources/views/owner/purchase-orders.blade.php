<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Purchase Orders - DPAM IMS</title>

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

        body { margin: 0; font-family: "Segoe UI", system-ui, -apple-system, Roboto, Arial, sans-serif; background: var(--bg); color: var(--ink); }

        a { color: inherit; text-decoration: none; }

        h3 { font-size: 15px; margin: 0 0 10px; }

        .hidden { display: none !important; }

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
        .btn:disabled { opacity: .55; cursor: not-allowed; }
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
        .btn-light { background: #eff6ff; color: var(--accent); }
        .btn-light:hover:not(:disabled) { background: #dbeafe; }

        /* ALERTS / NOTICES */
        .alert-success, .alert-error, .info-notice, .warning-notice { padding: 12px 15px; border-radius: 10px; margin-bottom: 18px; font-size: 13px; line-height: 1.5; border: 1px solid; }
        .alert-success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
        .alert-error ul { margin: 8px 0 0 20px; padding: 0; }
        .info-notice { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
        .warning-notice { background: #fffbeb; color: #92400e; border-color: #fde68a; }

        /* FILTER */
        .filter-box { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 16px; margin-bottom: 18px; display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
        .filter-box input, .filter-box select { padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: #fff; font-family: inherit; }
        .filter-box input[type=text] { min-width: 300px; flex: 1; }
        .filter-box input:focus, .filter-box select:focus { border-color: var(--accent); }

        /* TABLE */
        .table-panel { background: #fff; border: 1px solid var(--line); border-radius: 14px; overflow: hidden; margin-bottom: 22px; }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid var(--line); text-align: left; font-size: 13px; vertical-align: middle; white-space: nowrap; }
        th { color: var(--muted); font-weight: 600; background: #f8fafc; }
        tbody tr:hover td { background: #fafbfd; }
        tbody tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }
        .empty-state, .empty { color: var(--muted); font-size: 14px; padding: 36px 20px; text-align: center; white-space: normal; }
        .secondary-text { color: var(--muted); font-size: 12px; margin-top: 3px; }
        .actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }

        /* PAGINATION */
        .pager { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; font-size: 12px; color: var(--muted); padding: 10px 4px 0; }
        .table-panel .pager { padding: 10px 16px; border-top: 1px solid var(--line); }
        .table-panel .pager:empty { display: none; }
        .pager-btns { display: flex; gap: 4px; align-items: center; flex-wrap: wrap; }
        .pager-btn { border: 1px solid #cbd5e1; background: #fff; color: #334155; border-radius: 6px; padding: 5px 11px; font: inherit; font-size: 12px; font-weight: 600; cursor: pointer; }
        .pager-btn:hover:not(:disabled) { background: #f1f5f9; }
        .pager-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }
        .pager-btn:disabled { opacity: .5; cursor: not-allowed; }
        .pager-gap { padding: 0 4px; }

        /* BADGES */
        .status-badge, .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .status-pending, .status-low-stock { background: #fef3c7; color: #92400e; }
        .status-in-stock, .status-received { background: #dcfce7; color: #166534; }
        .status-cancelled, .status-out-stock { background: #fee2e2; color: #991b1b; }
        .status-approved { background: #dbeafe; color: #1e40af; }
        .status-partial { background: #ffedd5; color: #9a3412; }
        .status-default, .status-draft { background: #e5e7eb; color: #374151; }

        /* MODAL */
        .modal { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, .55); align-items: center; justify-content: center; padding: 20px; z-index: 1000; }
        .modal.show { display: flex; }
        .modal-content { background: #fff; width: 100%; max-width: 650px; max-height: 92vh; overflow-y: auto; border-radius: 16px; box-shadow: 0 25px 60px rgba(0, 0, 0, .3); }
        .modal-large { max-width: 1100px; }
        .modal-xl { max-width: 1320px; }
        .modal-sm { max-width: 480px; }
        .modal-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 18px 22px; border-bottom: 1px solid var(--line); position: sticky; top: 0; background: #fff; z-index: 5; }
        .modal-header h2 { margin: 0; font-size: 18px; }
        .modal-header p { margin: 5px 0 0; color: var(--muted); font-size: 13px; }
        .modal-body { padding: 22px; }
        .modal-footer { position: sticky; bottom: 0; background: #fff; border-top: 1px solid var(--line); padding: 12px 22px; display: flex; justify-content: flex-end; align-items: center; gap: 10px; flex-wrap: wrap; z-index: 5; }
        .close-x { border: none; background: transparent; font-size: 26px; line-height: 1; cursor: pointer; color: var(--muted); border-radius: 6px; width: 34px; height: 34px; flex-shrink: 0; }
        .close-x:hover { background: #f1f5f9; color: var(--ink); }

        /* FORM */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 7px; color: #334155; }
        .form-group small { display: block; color: var(--muted); font-weight: 400; margin-top: 5px; line-height: 1.4; font-size: 12px; }
        .form-group label small { display: inline; margin: 0; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; font-family: inherit; background: #fff; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-grid-three { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0 16px; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; flex-wrap: wrap; }
        .required { color: var(--danger); font-weight: 700; }
        .pw-wrap { position: relative; }
        .pw-wrap input { padding-right: 42px !important; }
        .pw-toggle { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: var(--muted); padding: 4px; display: flex; }
        .pw-toggle:hover { color: var(--ink); }
        .pw-toggle svg { width: 18px; height: 18px; }

        /* SEARCH FIELD WITH ICON */
        .search-wrap { position: relative; }
        .search-wrap > svg { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--muted); pointer-events: none; }
        .form-group .search-wrap input, .search-wrap input { padding-left: 34px; }

        /* SUPPLIER PICKER */
        .sup-results { position: absolute; left: 0; right: 0; top: calc(100% + 4px); background: #fff; border: 1px solid #cbd5e1; border-radius: 10px; box-shadow: 0 12px 28px rgba(15, 23, 42, .16); max-height: 280px; overflow-y: auto; z-index: 20; }
        .sup-option { padding: 9px 12px; cursor: pointer; border-bottom: 1px solid #f1f5f9; }
        .sup-option:last-child { border-bottom: none; }
        .sup-option:hover, .sup-option.active { background: #eff6ff; }
        .sup-option strong { display: block; font-size: 13px; }
        .sup-option span { display: block; font-size: 12px; color: var(--muted); margin-top: 2px; }
        .sup-note { padding: 8px 12px; font-size: 12px; color: var(--muted); background: #f8fafc; border-top: 1px solid var(--line); }
        .sup-none { padding: 14px 12px; font-size: 13px; color: var(--muted); text-align: center; }
        .sup-selected { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 8px 8px 8px 12px; border: 1px solid #bfdbfe; background: #eff6ff; border-radius: 8px; min-height: 42px; }
        .sup-selected strong { font-size: 14px; color: #1e40af; }
        .sup-selected .secondary-text { margin: 0; }
        .sup-clear { border: none; background: transparent; color: var(--muted); font-size: 22px; line-height: 1; cursor: pointer; border-radius: 6px; width: 30px; height: 30px; flex-shrink: 0; }
        .sup-clear:hover { background: #dbeafe; color: var(--danger); }
        .sup-invalid .sup-selected, .sup-invalid .search-wrap input { border-color: var(--danger); }

        /* DETAILS */
        .details-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px 24px; margin-bottom: 22px; }
        .detail-item { padding-bottom: 12px; border-bottom: 1px solid var(--line); min-width: 0; }
        .detail-label { display: block; color: var(--muted); font-size: 12px; margin-bottom: 5px; }
        .detail-value { font-weight: 600; color: var(--ink); font-size: 14px; white-space: normal; word-break: break-word; }
        .decision-box { background: #f8fafc; border: 1px solid var(--line); border-radius: 10px; padding: 14px 15px; margin-top: 14px; }
        .decision-title { font-size: 12px; font-weight: 700; color: var(--muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .04em; }
        .decision-text { font-size: 13px; line-height: 1.5; color: #475569; }

        /* ORDER ITEMS (view modal) */
        .po-items-container { border: 1px solid var(--line); border-radius: 10px; overflow-x: auto; }
        .po-items-container table { min-width: 600px; }
        .order-total-box { display: flex; justify-content: flex-end; margin: 16px 0; }
        .order-total { display: flex; justify-content: space-between; align-items: center; gap: 50px; min-width: 300px; background: #f8fafc; border: 1px solid var(--line); padding: 14px 18px; border-radius: 10px; }
        .order-total-label { font-weight: 600; color: #334155; }
        .order-total-value { font-size: 20px; font-weight: 700; }

        /* NEW PURCHASE ORDER WORKSPACE */
        .po-card { background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 16px; }
        .po-section-title { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .07em; margin: 0 0 12px; }
        .po-layout { display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); gap: 16px; align-items: start; margin-top: 16px; }
        .po-col { display: flex; flex-direction: column; gap: 14px; min-width: 0; }
        .po-empty { color: var(--muted); text-align: center; padding: 26px 14px; font-size: 13px; }
        .pinfo-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px 16px; }
        .pinfo-grid .detail-item { padding-bottom: 8px; }
        .chip-sup { display: inline-block; background: #f1f5f9; border: 1px solid #e2e8f0; color: #334155; border-radius: 6px; padding: 2px 8px; font-size: 12px; margin: 0 4px 4px 0; }
        .grid-toolbar { display: flex; gap: 8px; align-items: center; justify-content: space-between; flex-wrap: wrap; margin-bottom: 10px; }
        .grid-toolbar .search-wrap { flex: 1; min-width: 200px; }
        .grid-toolbar input { width: 100%; padding: 9px 12px 9px 34px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; font-size: 13px; outline: none; }
        .grid-toolbar input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
        .grid-count { font-size: 12px; color: var(--muted); }
        .grid-box { border: 1px solid var(--line); border-radius: 10px; overflow: hidden; }
        .scroll-y { max-height: 330px; overflow: auto; }
        .grid-box th { position: sticky; top: 0; z-index: 1; font-size: 11.5px; text-transform: uppercase; letter-spacing: .03em; }
        .grid-box th, .grid-box td { padding: 9px 12px; }
        tr.selectable { cursor: pointer; }
        tr.selected td { background: #eff6ff !important; }
        tr.row-out td { background: #fff7f7; }

        /* SUPPLIER PRODUCT FILTER */
        .supplier-filter { font-size: 12.5px; color: #475569; background: #f8fafc; border: 1px solid var(--line); border-radius: 8px; padding: 8px 12px; margin-bottom: 10px; }
        .supplier-filter label { display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; margin: 0; }
        .supplier-filter input { width: auto; }

        .cart-notice { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; font-weight: 600; margin-bottom: 10px; }

        .po-cart { border: 1px solid var(--line); border-radius: 10px; max-height: 480px; overflow-y: auto; }
        .po-item { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 10px 8px; align-items: start; padding: 12px; border-bottom: 1px solid var(--line); }
        .po-item:last-child { border-bottom: none; }
        .po-item.invalid { background: #fff7f7; }
        .po-item.flash { animation: poFlash 1.2s ease; }
        @keyframes poFlash { 0% { background: #fef3c7; } 100% { background: transparent; } }
        .pi-main { min-width: 0; }
        .pi-name { font-weight: 700; font-size: 13px; }
        .pi-sub { color: var(--muted); font-size: 12px; margin-top: 3px; }
        .pi-fields { grid-column: 1 / -1; display: grid; grid-template-columns: minmax(80px, .8fr) minmax(110px, 1.2fr) minmax(90px, 1fr); gap: 10px 12px; align-items: end; }
        .pi-field { min-width: 0; }
        .pi-field label { display: block; font-size: 11px; font-weight: 700; color: var(--muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: .03em; }
        .pi-field input { width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; font-size: 13px; outline: none; background: #fff; }
        .pi-field input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
        .pi-field input.bad { border-color: #dc2626; background: #fee2e2; }
        .info-tip { display: inline-block; width: 14px; height: 14px; line-height: 14px; border-radius: 50%; background: #e2e8f0; color: #475569; font-size: 10px; font-weight: 700; text-align: center; cursor: help; margin-left: 3px; text-transform: none; }
        .pi-subtotal { text-align: right; min-width: 0; }
        .pi-subtotal span { display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .03em; margin-bottom: 4px; }
        .pi-subtotal strong { font-size: 14px; display: block; padding: 8px 0; }
        .remove-item { border: none; background: transparent; color: var(--danger); font-size: 22px; line-height: 1; cursor: pointer; border-radius: 6px; padding: 2px 8px; }
        .remove-item:hover { background: #fee2e2; }
        .pi-msg { grid-column: 1 / -1; color: #b91c1c; font-size: 12px; font-weight: 700; }
        .stock-display { font-weight: 700; }
        .po-total-row { display: flex; justify-content: space-between; align-items: center; gap: 20px; background: #f8fafc; border: 1px solid var(--line); border-radius: 10px; padding: 14px 18px; margin-top: 12px; }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .po-layout { grid-template-columns: 1fr; }
            .details-grid, .form-grid-three { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 800px) {
            .layout { flex-direction: column; }
            .sidebar { position: relative; width: 100%; }
            .main { margin-left: 0; padding: 20px; }
            .form-grid-three, .details-grid, .pinfo-grid { grid-template-columns: 1fr; }
            .form-grid-three .form-group { margin-bottom: 14px !important; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .filter-box input[type=text] { min-width: 100%; }
            .pi-fields { grid-template-columns: 1fr 1fr; }
            .pi-subtotal { text-align: left; }
            .modal { padding: 8px; }
            .modal-body { padding: 14px; }
            .modal-footer { padding: 10px 14px; }
        }
    </style>
</head>

<body>

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

            <a href="{{ route('owner.suppliers') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="12" height="9" rx="1"/><path d="M14 10h4l3 3v3h-7"/><circle cx="7" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/></svg>
                Suppliers
            </a>

            <a href="{{ route('owner.purchase-orders') }}" class="active">
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
                <h1 class="page-title">Purchase Orders</h1>
                <div class="page-description">Prepare and monitor orders placed with suppliers.</div>
            </div>
            <div class="topbar-actions">
                <button type="button" class="btn btn-primary" onclick="openCreatePurchaseOrderModal()">+ New Purchase Order</button>
                <div class="role-badge">OWNER</div>
            </div>
        </div>


        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        @if(session('warning'))
            <div class="warning-notice">{{ session('warning') }}</div>
        @endif

        @if(session('info'))
            <div class="info-notice">{{ session('info') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                <strong>Please check the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <div class="filter-box">

            <input type="text" id="poSearch" placeholder="Search PO number or supplier..." oninput="filterPurchaseOrders()">

            <select id="statusFilter" onchange="filterPurchaseOrders()">
                <option value="">All Purchase Order Statuses</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending Approval</option>
                <option value="approved">Approved</option>
                <option value="partially_received">Partially Received</option>
                <option value="received">Fully Received</option>
                <option value="cancelled">Cancelled</option>
            </select>

        </div>


        <section class="table-panel">

            <div class="table-wrapper">

                <table id="purchaseOrdersTable">

                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>PO Date</th>
                            <th>Customer Order</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($purchaseOrders as $purchaseOrder)

                            @php

                                $statusLabel = match($purchaseOrder->status) {
                                    'draft' => 'Draft',
                                    'pending' => 'Pending Approval',
                                    'approved' => 'Approved',
                                    'partially_received' => 'Partially Received',
                                    'received' => 'Fully Received',
                                    'cancelled' => 'Cancelled',
                                    default => ucfirst(str_replace('_', ' ', $purchaseOrder->status)),
                                };

                                $statusClass = match($purchaseOrder->status) {
                                    'draft' => 'status-draft',
                                    'pending' => 'status-pending',
                                    'approved' => 'status-approved',
                                    'partially_received' => 'status-partial',
                                    'received' => 'status-received',
                                    'cancelled' => 'status-cancelled',
                                    default => 'status-default',
                                };

                                $purchaseOrderTotal = $purchaseOrder->items->sum('subtotal');

                            @endphp

                            <tr data-po-number="{{ strtolower($purchaseOrder->po_number ?? '') }}" data-supplier-name="{{ strtolower($purchaseOrder->supplier->supplier_name ?? $purchaseOrder->supplier->name ?? '') }}" data-status="{{ $purchaseOrder->status }}">

                                <td><strong>{{ $purchaseOrder->po_number ?? '—' }}</strong></td>

                                <td><strong>{{ $purchaseOrder->supplier->supplier_name ?? $purchaseOrder->supplier->name ?? '—' }}</strong></td>

                                <td>{{ $purchaseOrder->po_date ? $purchaseOrder->po_date->format('M d, Y') : '—' }}</td>

                                <td>
                                    @if($purchaseOrder->customerOrder)
                                        {{ $purchaseOrder->customerOrder->order_number }}
                                    @else
                                        General Stock
                                    @endif
                                </td>

                                <td>{{ $purchaseOrder->items->count() }} {{ $purchaseOrder->items->count() === 1 ? 'item' : 'items' }}</td>

                                <td><strong>₱{{ number_format($purchaseOrderTotal, 2) }}</strong></td>

                                <td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>

                                <td>
                                    <div class="actions">

                                        <button type="button"
                                                class="btn btn-sm btn-secondary"
                                                onclick="viewPurchaseOrder({{ $purchaseOrder->id }})">
                                            View
                                        </button>

                                        @if($purchaseOrder->status === 'draft')

                                            <button type="button"
                                                    class="btn btn-sm btn-primary"
                                                    onclick="openDecisionModal({{ $purchaseOrder->id }}, 'submit')">
                                                Submit for Approval
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="openDecisionModal({{ $purchaseOrder->id }}, 'cancel')">
                                                Cancel
                                            </button>

                                        @elseif($purchaseOrder->status === 'pending')

                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    onclick="openDecisionModal({{ $purchaseOrder->id }}, 'approve')">
                                                Approve
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="openDecisionModal({{ $purchaseOrder->id }}, 'cancel')">
                                                Cancel
                                            </button>

                                        @elseif($purchaseOrder->status === 'approved' || $purchaseOrder->status === 'partially_received')

                                            {{-- Stock In is recorded on the Sales & Inventory page; it is the only action that adds inventory. --}}
                                            <a href="{{ route('owner.sales-inventory', ['tab' => 'inventory']) }}"
                                               class="btn btn-sm btn-primary">
                                                Stock In
                                            </a>

                                        @elseif($purchaseOrder->status === 'received')

                                            <span class="secondary-text" style="color:#166534;">
                                                Completed
                                            </span>

                                        @elseif($purchaseOrder->status === 'cancelled')

                                            <span class="secondary-text" style="color:#991b1b;">
                                                Cancelled
                                            </span>

                                        @endif

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr><td colspan="8" class="empty-state">No purchase orders recorded yet.</td></tr>

                        @endforelse

                        @if($purchaseOrders->count())
                            <tr id="poNoMatch" class="hidden"><td colspan="8" class="empty-state">No purchase orders match your search or filter.</td></tr>
                        @endif

                    </tbody>

                </table>

            </div>

            <div class="pager" id="poPager"></div>

        </section>

    </main>


{{-- CREATE PURCHASE ORDER MODAL --}}
<div class="modal" id="createPurchaseOrderModal">
    <div class="modal-content modal-xl">

        <div class="modal-header">
            <div>
                <h2>New Purchase Order</h2>
                <p>Prepare an order for a supplier.</p>
            </div>
            <button type="button" class="close-x" onclick="closeModal('createPurchaseOrderModal')">&times;</button>
        </div>

        <form action="{{ route('owner.purchase-orders.store') }}" method="POST" id="createPurchaseOrderForm" novalidate>

            @csrf

            <input type="hidden" name="status" id="createPOStatus" value="draft">

            <div class="modal-body">

                {{-- visible validation area --}}
                <div class="alert-error hidden" id="poFormErrors" role="alert"></div>

                {{-- TOP: PURCHASE ORDER INFORMATION --}}
                <div class="po-card">

                    <p class="po-section-title">Purchase Order Information</p>

                    <div class="form-grid-three">

                        {{-- SEARCHABLE SUPPLIER SELECTOR (submits supplier_id) - ACTIVE suppliers only --}}
                        <div class="form-group" id="supPicker" style="margin-bottom:0;">
                            <label>Supplier <span class="required">*</span></label>

                            <input type="hidden" name="supplier_id" id="poSupplier" value="">

                            <div class="sup-selected hidden" id="supSelected">
                                <div style="min-width:0;">
                                    <strong id="supSelectedName"></strong>
                                    <div class="secondary-text" id="supSelectedMeta"></div>
                                </div>
                                <button type="button" class="sup-clear" title="Clear supplier" aria-label="Clear supplier" onclick="clearSupplier()">&times;</button>
                            </div>

                            <div class="search-wrap" id="supSearchWrap">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                                <input type="text"
                                       id="supSearch"
                                       placeholder="Search supplier..."
                                       autocomplete="off"
                                       oninput="renderSupplierResults()"
                                       onfocus="renderSupplierResults()"
                                       onkeydown="supplierKey(event)">
                                <div class="sup-results hidden" id="supResults"></div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label>Customer Order <small>(optional)</small></label>
                            <select name="customer_order_id">
                                <option value="">None / General Stock Purchase</option>
                                @foreach($customerOrders as $customerOrder)
                                    <option value="{{ $customerOrder->id }}" {{ old('customer_order_id') == $customerOrder->id ? 'selected' : '' }}>
                                        {{ $customerOrder->order_number }} — {{ $customerOrder->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label>PO Date <span class="required">*</span></label>
                            <input type="date" name="po_date" id="poDate" value="{{ old('po_date', date('Y-m-d')) }}" required>
                        </div>

                    </div>

                </div>

                <div class="po-layout">

                    {{-- LEFT: PRODUCT INFORMATION + PRODUCT DATA GRID --}}
                    <div class="po-col">

                        <div class="po-card">

                            <p class="po-section-title">Product Information</p>

                            <div class="po-empty" id="poInfoEmpty">Select a product to view product information.</div>

                            <div class="hidden" id="poInfoBody">
                                <div class="pinfo-grid" id="poInfoGrid"></div>
                                <div class="hidden" id="poInfoSuppliersWrap" style="margin-top:10px;">
                                    <span class="detail-label">Suppliers</span>
                                    <div id="poInfoSuppliers"></div>
                                </div>
                                <div style="margin-top:10px;">
                                    <button type="button" class="btn btn-sm btn-primary" id="poInfoSelectBtn">Select this product</button>
                                </div>
                            </div>

                        </div>

                        <div class="po-card">

                            <div class="grid-toolbar">
                                <p class="po-section-title" style="margin:0;">Product Data Grid</p>
                                <span class="grid-count" id="poGridCount"></span>
                            </div>

                            <div class="grid-toolbar">
                                <div class="search-wrap">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                                    <input type="text" id="poProductSearch" placeholder="Search product, brand, category..." autocomplete="off" oninput="renderProductGrid()" onkeydown="if (event.key === 'Enter') { event.preventDefault(); }">
                                </div>
                            </div>

                            {{-- Appears once a supplier is selected (uses the product_supplier relationship) --}}
                            <div class="supplier-filter hidden" id="poSupplierFilter"></div>

                            <div class="grid-box">
                                <div class="table-wrapper scroll-y">
                                    <table id="poProductGrid">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Brand</th>
                                                <th>Category</th>
                                                <th class="text-right">Stock</th>
                                                <th>Unit</th>
                                                <th>Status</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="poProductGridBody"></tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="pager" id="poGridPager"></div>

                        </div>

                    </div>

                    {{-- RIGHT: SELECTED PRODUCTS / PURCHASE CART --}}
                    <div class="po-col">

                        <div class="po-card">

                            <div class="grid-toolbar">
                                <p class="po-section-title" style="margin:0;">Selected Products <span class="status-badge status-approved" id="poCartCount">0</span></p>
                            </div>

                            <div class="cart-notice hidden" id="poCartNotice" role="status"></div>

                            <div class="po-cart" id="poCart"></div>

                            <div class="po-total-row">
                                <span class="order-total-label">Total Purchase Order</span>
                                <span class="order-total-value" id="purchaseOrderTotal">₱0.00</span>
                            </div>

                        </div>

                        <div class="po-card">
                            <div class="form-group" style="margin-bottom:0;">
                                <label>Notes</label>
                                <textarea name="notes" maxlength="2000" placeholder="Additional purchasing notes...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-cancel"
                        onclick="closeModal('createPurchaseOrderModal')">
                    Cancel
                </button>

                <button type="submit"
                        class="btn btn-secondary"
                        id="poDraftBtn"
                        data-status="draft"
                        data-label="Save as Draft"
                        onclick="document.getElementById('createPOStatus').value='draft';">
                    Save as Draft
                </button>

                <button type="submit"
                        class="btn btn-primary"
                        id="poSubmitBtn"
                        data-status="pending"
                        data-label="Submit for Approval"
                        onclick="document.getElementById('createPOStatus').value='pending';">
                    Submit for Approval
                </button>

            </div>

        </form>

    </div>
</div>


{{-- VIEW PURCHASE ORDER MODAL --}}
<div class="modal" id="viewPurchaseOrderModal">
    <div class="modal-content modal-large">

        <div class="modal-header">
            <div>
                <h2>Purchase Order Details</h2>
                <p id="viewPONumber"></p>
            </div>
            <button type="button" class="close-x" onclick="closeModal('viewPurchaseOrderModal')">&times;</button>
        </div>

        <div class="modal-body">

            <div class="details-grid">
                <div class="detail-item"><span class="detail-label">Purchase Order</span><span class="detail-value" id="viewPONumberValue"></span></div>
                <div class="detail-item"><span class="detail-label">Supplier</span><span class="detail-value" id="viewSupplierName"></span></div>
                <div class="detail-item"><span class="detail-label">PO Date</span><span class="detail-value" id="viewPODate"></span></div>
                <div class="detail-item"><span class="detail-label">Customer Order</span><span class="detail-value" id="viewCustomerOrder"></span></div>
                <div class="detail-item"><span class="detail-label">Status</span><span class="detail-value" id="viewPOStatus"></span></div>
                <div class="detail-item"><span class="detail-label">Created By</span><span class="detail-value" id="viewCreatedBy"></span></div>
            </div>

            <h3>Products</h3>

            <div class="po-items-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="text-align:right;">Quantity</th>
                            <th style="text-align:right;">Unit Cost</th>
                            <th style="text-align:right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="viewPOItems"></tbody>
                </table>
            </div>

            <div class="pager" id="viewPOPager"></div>

            <div class="order-total-box">
                <div class="order-total">
                    <span class="order-total-label">Total</span>
                    <span class="order-total-value" id="viewPOTotal">₱0.00</span>
                </div>
            </div>

            <div class="decision-box">
                <div class="decision-title">Purchase Order Notes</div>
                <div class="decision-text" id="viewPONotes">—</div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('viewPurchaseOrderModal')">Close</button>
            </div>

        </div>

    </div>
</div>


{{-- PURCHASE ORDER ACTION MODAL (Submit / Approve / Cancel) - one form, action URL set from named routes --}}
<div class="modal" id="decisionModal">
    <div class="modal-content modal-sm">

        <div class="modal-header">
            <div>
                <h2 id="decisionTitle">Purchase Order</h2>
                <p id="decisionPONumber"></p>
            </div>
            <button type="button" class="close-x" onclick="closeModal('decisionModal')">&times;</button>
        </div>

        <div class="modal-body">

            <form method="POST" id="decisionForm">

                @csrf
                @method('PATCH')

                <div class="details-grid" style="grid-template-columns:repeat(2, 1fr);margin-bottom:0;">
                    <div class="detail-item"><span class="detail-label">Supplier</span><span class="detail-value" id="decisionSupplier">—</span></div>
                    <div class="detail-item"><span class="detail-label">Total</span><span class="detail-value" id="decisionTotal">—</span></div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('decisionModal')">Back</button>
                    <button type="submit" class="btn btn-primary" id="decisionSubmitButton">Continue</button>
                </div>

            </form>

        </div>

    </div>
</div>


{{-- JAVASCRIPT DATA --}}
@php

    /*
     * Existing POs keep their supplier name through the normal relationship,
     * even if that supplier is now inactive.
     */
    $purchaseOrderJavascriptData = $purchaseOrders->map(function ($purchaseOrder) {

        return [

            'id' => $purchaseOrder->id,
            'po_number' => $purchaseOrder->po_number ?? '—',
            'supplier_name' => $purchaseOrder->supplier->supplier_name ?? $purchaseOrder->supplier->name ?? '—',
            'po_date' => $purchaseOrder->po_date ? $purchaseOrder->po_date->format('M d, Y') : '—',
            'customer_order' => $purchaseOrder->customerOrder
                ? ($purchaseOrder->customerOrder->order_number . ' — ' . $purchaseOrder->customerOrder->customer_name)
                : 'None / General Stock',
            'status' => $purchaseOrder->status,
            'created_by' => $purchaseOrder->user->name ?? '—',
            'notes' => $purchaseOrder->notes ?? '',

            'items' => $purchaseOrder->items
                ->map(function ($item) {

                    return [
                        'product_name' => $item->product->product_name ?? '—',
                        'quantity' => (int) $item->quantity,
                        'unit_cost' => (float) $item->unit_cost,
                        'subtotal' => (float) $item->subtotal,
                    ];

                })
                ->values(),

        ];

    })->values();


    /* ACTIVE suppliers only (the controller already filters them). */
    $supplierJavascriptData = $suppliers->map(function ($supplier) {

        return [
            'id' => $supplier->id,
            'name' => $supplier->supplier_name ?? $supplier->name ?? 'Unnamed Supplier',
            'contact_person' => $supplier->contact_person ?? null,
            'contact_number' => $supplier->contact_number ?? null,
            'email' => $supplier->email ?? null,
        ];

    })->values();


    $productJavascriptData = $products->map(function ($product) {

        // Only values the product record already carries are used; anything missing stays null and shows as a dash.
        $attributes = $product->getAttributes();

        try {
            $categoryName = $product->category->category_name ?? null;
        } catch (\Throwable $e) {
            $categoryName = null;
        }

        $productSuppliers = $product->relationLoaded('suppliers') ? $product->suppliers : collect();

        $supplierNames = $productSuppliers
            ->map(fn ($supplier) => $supplier->supplier_name ?? $supplier->name ?? null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $supplierIds = $productSuppliers
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        return [
            'id' => $product->id,
            'name' => $product->product_name,
            'brand' => $product->brand ?? null,
            'category' => $categoryName,
            'api' => $attributes['api'] ?? null,
            'base_oil' => $attributes['base_oil'] ?? null,
            'package_size' => $attributes['package_size'] ?? null,
            'unit' => $product->unit,
            'stock' => $product->inventory ? (int) $product->inventory->current_stock : 0,
            'reorder' => (int) ($product->reorder_level ?? 0),
            'suppliers' => $supplierNames,
            'supplier_ids' => $supplierIds,
        ];

    })->values();

@endphp


<script>

    const PAGE_SIZE = 15;

    const $ = function (id) { return document.getElementById(id); };

    const purchaseOrders = @js($purchaseOrderJavascriptData);

    const suppliers = @js($supplierJavascriptData);

    const products = @js($productJavascriptData);

    const oldItems = @js(old('items', []));

    const oldSupplierId = @js(old('supplier_id'));

    const hasServerErrors = @js($errors->any());

    /*
     * Laravel-generated URLs for every Purchase Order action (named routes in web.php).
     * '__ID__' is replaced with the numeric purchase order id (route-model-binding key).
     */
    const poRoutes = {
        submit:  @js(route('owner.purchase-orders.submit', '__ID__')),
        approve: @js(route('owner.purchase-orders.approve', '__ID__')),
        cancel:  @js(route('owner.purchase-orders.cancel', '__ID__'))
    };


    /* ---------------- PAGINATION HELPERS ---------------- */
    function pageList(page, pages)
    {
        const set = new Set([1, pages, page - 1, page, page + 1]);
        const list = Array.from(set).filter(function (n) { return n >= 1 && n <= pages; }).sort(function (a, b) { return a - b; });
        const out = [];

        list.forEach(function (n, i) {
            if (i && n - list[i - 1] > 1) out.push('…');
            out.push(n);
        });

        return out;
    }

    function renderPager(container, total, page, size, onGo)
    {
        container.innerHTML = '';

        if (!total) return;

        const pages = Math.ceil(total / size);
        const from = (page - 1) * size + 1;
        const to = Math.min(total, page * size);

        const info = document.createElement('span');
        info.textContent = 'Showing ' + from + '–' + to + ' of ' + total;
        container.appendChild(info);

        if (pages <= 1) return;

        const box = document.createElement('div');
        box.className = 'pager-btns';

        const add = function (label, target, disabled, active) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'pager-btn' + (active ? ' active' : '');
            button.textContent = label;
            button.disabled = !!disabled;
            button.addEventListener('click', function () { onGo(target); });
            box.appendChild(button);
        };

        add('Previous', page - 1, page <= 1, false);

        pageList(page, pages).forEach(function (n) {
            if (n === '…') {
                const gap = document.createElement('span');
                gap.className = 'pager-gap';
                gap.textContent = '…';
                box.appendChild(gap);
            } else {
                add(String(n), n, false, n === page);
            }
        });

        add('Next', page + 1, page >= pages, false);

        container.appendChild(box);
    }


    /* ---------------- PURCHASE ORDERS TABLE: SEARCH + STATUS + PAGINATION ---------------- */
    let poPage = 1;

    function filterPurchaseOrders(keepPage)
    {
        if (keepPage !== true) poPage = 1;

        const search = $('poSearch').value.toLowerCase().trim();
        const status = $('statusFilter').value;

        const rows = Array.from(document.querySelectorAll('#purchaseOrdersTable tbody tr[data-status]'));

        const matched = rows.filter(function (row) {

            const poNumber = row.dataset.poNumber || '';
            const supplierName = row.dataset.supplierName || '';
            const rowStatus = row.dataset.status || '';

            const matchesSearch = poNumber.includes(search) || supplierName.includes(search);
            const matchesStatus = status === '' || rowStatus === status;

            return matchesSearch && matchesStatus;

        });

        const pages = Math.max(1, Math.ceil(matched.length / PAGE_SIZE));
        poPage = Math.min(Math.max(1, poPage), pages);

        const visible = new Set(matched.slice((poPage - 1) * PAGE_SIZE, poPage * PAGE_SIZE));

        rows.forEach(function (row) { row.style.display = visible.has(row) ? '' : 'none'; });

        const noMatch = $('poNoMatch');
        if (noMatch) noMatch.classList.toggle('hidden', matched.length > 0 || rows.length === 0);

        renderPager($('poPager'), matched.length, poPage, PAGE_SIZE, function (target) {
            poPage = target;
            filterPurchaseOrders(true);
        });
    }


    /* ---------------- SUPPLIER SEARCH SELECTOR (ACTIVE suppliers only) ---------------- */
    const SUPPLIER_RESULT_LIMIT = 8;
    let supMatches = [];
    let supActive = -1;

    function supplierById(id)
    {
        return suppliers.find(function (supplier) { return Number(supplier.id) === Number(id); });
    }

    function supplierMeta(supplier)
    {
        return [supplier.contact_person, supplier.contact_number].filter(function (value) { return value; }).join(' · ');
    }

    function renderSupplierResults()
    {
        const box = $('supResults');
        const query = ($('supSearch').value || '').toLowerCase().trim();

        const all = suppliers.filter(function (supplier) {
            if (!query) return true;
            return [supplier.name, supplier.contact_person, supplier.contact_number, supplier.email].join(' ').toLowerCase().includes(query);
        }).sort(function (a, b) { return String(a.name).localeCompare(String(b.name)); });

        supMatches = all.slice(0, SUPPLIER_RESULT_LIMIT);
        supActive = supMatches.length ? 0 : -1;

        if (!all.length) {

            box.innerHTML = '<div class="sup-none">No supplier found.</div>';

        } else {

            box.innerHTML = supMatches.map(function (supplier, i) {

                const meta = supplierMeta(supplier);

                return '<div class="sup-option' + (i === supActive ? ' active' : '') + '" data-index="' + i + '" onmousedown="event.preventDefault(); pickSupplier(' + Number(supplier.id) + ')">' +
                    '<strong>' + escapeHtml(supplier.name) + '</strong>' +
                    (meta ? '<span>' + escapeHtml(meta) + '</span>' : '') +
                    '</div>';

            }).join('') + (all.length > supMatches.length
                ? '<div class="sup-note">Showing ' + supMatches.length + ' of ' + all.length + ' suppliers. Keep typing to narrow the list.</div>'
                : '');

        }

        box.classList.remove('hidden');
    }

    function hideSupplierResults()
    {
        $('supResults').classList.add('hidden');
    }

    function highlightSupplier()
    {
        document.querySelectorAll('#supResults .sup-option').forEach(function (element, i) {
            element.classList.toggle('active', i === supActive);
            if (i === supActive && element.scrollIntoView) element.scrollIntoView({ block: 'nearest' });
        });
    }

    function supplierKey(event)
    {
        const open = !$('supResults').classList.contains('hidden');

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (!open) { renderSupplierResults(); return; }
            if (supMatches.length) { supActive = Math.min(supMatches.length - 1, supActive + 1); highlightSupplier(); }
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (supMatches.length) { supActive = Math.max(0, supActive - 1); highlightSupplier(); }
        } else if (event.key === 'Enter') {
            event.preventDefault();
            if (open && supActive >= 0 && supMatches[supActive]) pickSupplier(supMatches[supActive].id);
        } else if (event.key === 'Escape' && open) {
            event.stopPropagation();
            hideSupplierResults();
        }
    }

    function pickSupplier(id)
    {
        const supplier = supplierById(id);

        if (!supplier) return;

        $('poSupplier').value = supplier.id;
        $('supSelectedName').textContent = supplier.name;
        $('supSelectedMeta').textContent = supplierMeta(supplier);
        $('supSelectedMeta').classList.toggle('hidden', !supplierMeta(supplier));

        $('supSelected').classList.remove('hidden');
        $('supSearchWrap').classList.add('hidden');
        $('supPicker').classList.remove('sup-invalid');

        $('supSearch').value = '';
        hideSupplierResults();

        /* A newly picked supplier starts with the "supplied by" filter on. */
        supplierOnly = true;
        renderProductGrid();
    }

    function clearSupplier()
    {
        $('poSupplier').value = '';
        $('supSelected').classList.add('hidden');
        $('supSearchWrap').classList.remove('hidden');
        $('supSearch').value = '';
        $('supSearch').focus();

        renderProductGrid();
    }

    document.addEventListener('click', function (event) {
        const picker = $('supPicker');
        if (picker && !picker.contains(event.target)) hideSupplierResults();
    });


    /* ---------------- CREATE PURCHASE ORDER: STATE ---------------- */
    let cart = [];              // [{ product_id, qty, cost, dirty }]
    let gridPage = 1;
    let infoId = null;
    let submitAttempted = false;
    let noticeTimer = null;
    let supplierOnly = true;    // "only products supplied by the selected supplier"

    function productById(id)
    {
        return products.find(function (product) { return Number(product.id) === Number(id); });
    }

    function inCart(id)
    {
        return cart.some(function (item) { return Number(item.product_id) === Number(id); });
    }

    /* Stock status: 0 = OUT OF STOCK, 1..reorder level = LOW STOCK, above = IN STOCK. rank drives the grid order. */
    function stockStatus(product)
    {
        const stock = Number(product.stock) || 0;
        const reorder = Number(product.reorder) || 0;

        if (stock <= 0) return { label: 'OUT OF STOCK', cls: 'status-out-stock', rank: 0 };
        if (stock <= reorder) return { label: 'LOW STOCK', cls: 'status-low-stock', rank: 1 };
        return { label: 'IN STOCK', cls: 'status-in-stock', rank: 2 };
    }

    /* Urgency order: OUT OF STOCK, then LOW STOCK, then IN STOCK; inside each group the lowest stock comes first. */
    const sortedProducts = products.slice().sort(function (a, b) {

        const rankA = stockStatus(a).rank;
        const rankB = stockStatus(b).rank;

        if (rankA !== rankB) return rankA - rankB;

        const stockA = Number(a.stock) || 0;
        const stockB = Number(b.stock) || 0;

        if (stockA !== stockB) return stockA - stockB;

        return String(a.name).localeCompare(String(b.name));

    });

    function dash(value)
    {
        return (value === null || value === undefined || value === '') ? '—' : value;
    }


    /* ---------------- SUPPLIER -> PRODUCTS (product_supplier) ---------------- */
    function selectedSupplierId()
    {
        const value = $('poSupplier').value;
        return value ? Number(value) : null;
    }

    function suppliedBy(product, supplierId)
    {
        return (product.supplier_ids || []).map(Number).includes(Number(supplierId));
    }

    function supplierProductCount(supplierId)
    {
        return products.filter(function (product) { return suppliedBy(product, supplierId); }).length;
    }

    function toggleSupplierOnly(checked)
    {
        supplierOnly = !!checked;
        renderProductGrid();
    }

    function renderSupplierFilter()
    {
        const box = $('poSupplierFilter');
        const supplierId = selectedSupplierId();
        const supplier = supplierId ? supplierById(supplierId) : null;

        if (!supplier) {
            box.classList.add('hidden');
            box.innerHTML = '';
            return;
        }

        const count = supplierProductCount(supplierId);

        box.classList.remove('hidden');

        if (count === 0) {

            /* Nothing linked yet: never hide products, just explain. */
            box.innerHTML = 'No products are linked to ' + escapeHtml(supplier.name) + ' yet, so all products are shown.';
            return;

        }

        box.innerHTML = '<label><input type="checkbox"' + (supplierOnly ? ' checked' : '') + ' onchange="toggleSupplierOnly(this.checked)"> ' +
            'Only products supplied by <strong>' + escapeHtml(supplier.name) + '</strong> (' + count + ')</label>';
    }


    /* ---------------- OPEN MODAL ---------------- */
    function openCreatePurchaseOrderModal()
    {
        renderProductGrid();
        renderCart();
        hidePoErrors();
        openModal('createPurchaseOrderModal');
    }


    /* ---------------- PRODUCT DATA GRID ---------------- */
    function renderProductGrid(keepPage)
    {
        if (keepPage !== true) gridPage = 1;

        renderSupplierFilter();

        const search = ($('poProductSearch').value || '').toLowerCase().trim();

        const supplierId = selectedSupplierId();
        const restrict = !!supplierId && supplierOnly && supplierProductCount(supplierId) > 0;

        const list = sortedProducts.filter(function (product) {
            if (restrict && !suppliedBy(product, supplierId)) return false;
            if (!search) return true;
            return [product.name, product.brand, product.category].join(' ').toLowerCase().includes(search);
        });

        const pages = Math.max(1, Math.ceil(list.length / PAGE_SIZE));
        gridPage = Math.min(Math.max(1, gridPage), pages);

        const pageRows = list.slice((gridPage - 1) * PAGE_SIZE, gridPage * PAGE_SIZE);

        const body = $('poProductGridBody');

        if (!list.length) {

            body.innerHTML = '<tr><td colspan="7" class="empty-state">No products found.</td></tr>';

        } else {

            body.innerHTML = pageRows.map(function (product) {

                const status = stockStatus(product);
                const selected = inCart(product.id);

                return '<tr class="selectable' + (status.rank === 0 ? ' row-out' : '') + (Number(infoId) === Number(product.id) ? ' selected' : '') + '" onclick="showPoProduct(' + Number(product.id) + ')">' +
                    '<td><strong>' + escapeHtml(product.name) + '</strong></td>' +
                    '<td>' + escapeHtml(dash(product.brand)) + '</td>' +
                    '<td>' + escapeHtml(dash(product.category)) + '</td>' +
                    '<td class="text-right"><strong>' + Number(product.stock).toLocaleString() + '</strong></td>' +
                    '<td>' + escapeHtml(dash(product.unit)) + '</td>' +
                    '<td><span class="status-badge ' + status.cls + '">' + status.label + '</span></td>' +
                    '<td>' + (selected
                        ? '<button type="button" class="btn btn-sm btn-light" onclick="event.stopPropagation(); selectPoProduct(' + Number(product.id) + ')">Selected ✓</button>'
                        : '<button type="button" class="btn btn-sm btn-primary" onclick="event.stopPropagation(); selectPoProduct(' + Number(product.id) + ')">Select</button>') + '</td>' +
                    '</tr>';

            }).join('');

        }

        $('poGridCount').textContent = list.length + ' product' + (list.length === 1 ? '' : 's');

        renderPager($('poGridPager'), list.length, gridPage, PAGE_SIZE, function (target) {
            gridPage = target;
            renderProductGrid(true);
        });
    }


    /* ---------------- PRODUCT INFORMATION ---------------- */
    function showPoProduct(id)
    {
        const product = productById(id);

        if (!product) return;

        infoId = product.id;

        const status = stockStatus(product);

        const item = function (label, valueHtml) {
            return '<div class="detail-item"><span class="detail-label">' + label + '</span><span class="detail-value">' + valueHtml + '</span></div>';
        };

        $('poInfoGrid').innerHTML =
            item('Product Name', escapeHtml(dash(product.name))) +
            item('Brand', escapeHtml(dash(product.brand))) +
            item('Category', escapeHtml(dash(product.category))) +
            item('API', escapeHtml(dash(product.api))) +
            item('Base Oil', escapeHtml(dash(product.base_oil))) +
            item('Package Size', escapeHtml(dash(product.package_size))) +
            item('Unit', escapeHtml(dash(product.unit))) +
            item('Current Stock', Number(product.stock).toLocaleString()) +
            item('Reorder Level', Number(product.reorder).toLocaleString()) +
            item('Stock Status', '<span class="status-badge ' + status.cls + '">' + status.label + '</span>');

        const productSuppliers = Array.isArray(product.suppliers) ? product.suppliers : [];

        $('poInfoSuppliers').innerHTML = productSuppliers.map(function (name) { return '<span class="chip-sup">' + escapeHtml(name) + '</span>'; }).join('');
        $('poInfoSuppliersWrap').classList.toggle('hidden', productSuppliers.length === 0);

        const selectButton = $('poInfoSelectBtn');
        selectButton.textContent = inCart(product.id) ? 'Selected ✓' : 'Select this product';
        selectButton.onclick = function () { selectPoProduct(product.id); };

        $('poInfoEmpty').classList.add('hidden');
        $('poInfoBody').classList.remove('hidden');

        renderProductGrid(true);
    }


    /* ---------------- SELECT / REMOVE ---------------- */
    function flashNotice(message)
    {
        const notice = $('poCartNotice');

        notice.textContent = message;
        notice.classList.remove('hidden');

        if (noticeTimer) clearTimeout(noticeTimer);

        noticeTimer = setTimeout(function () { notice.classList.add('hidden'); }, 3500);
    }

    function selectPoProduct(id)
    {
        const product = productById(id);

        if (!product) return;

        if (inCart(product.id)) {

            /* Never add a duplicate row: warn and point to the existing one. */
            flashNotice('This product is already selected.');

            const index = cart.findIndex(function (item) { return Number(item.product_id) === Number(product.id); });
            const row = $('poItem' + index);

            if (row) {
                row.classList.remove('flash');
                void row.offsetWidth;
                row.classList.add('flash');
                if (row.scrollIntoView) row.scrollIntoView({ block: 'nearest' });
            }

        } else {

            /* Purchase Unit Cost starts blank: it is the supplier's price, never the product's selling price. */
            cart.push({ product_id: Number(product.id), qty: '1', cost: '', dirty: false });

            hidePoErrors();
            renderCart();

            const container = $('poCart');
            container.scrollTop = container.scrollHeight;

        }

        showPoProduct(product.id);
    }

    function removePoItem(index)
    {
        cart.splice(index, 1);

        renderCart();

        if (infoId !== null) showPoProduct(infoId); else renderProductGrid(true);
    }


    /* ---------------- ITEM VALIDATION ---------------- */
    function qtyIssue(item)
    {
        const text = String(item.qty === null || item.qty === undefined ? '' : item.qty).trim();
        const value = Number(text);

        if (text === '' || isNaN(value) || !Number.isInteger(value) || value < 1) {
            return 'Purchase quantity must be a whole number of at least 1.';
        }

        return '';
    }

    function costIssue(item)
    {
        const text = String(item.cost === null || item.cost === undefined ? '' : item.cost).trim();
        const value = Number(text);

        if (text === '') return 'Enter the supplier purchase unit cost.';

        if (isNaN(value) || !isFinite(value) || value <= 0) return 'Purchase unit cost must be greater than 0.';

        return '';
    }

    function itemIssues(item)
    {
        return [qtyIssue(item), costIssue(item)].filter(function (message) { return message !== ''; });
    }

    function showItemIssues(item)
    {
        return item.dirty || submitAttempted;
    }

    /* Subtotal is rounded to cents so the on-screen total matches what the server stores per line. */
    function itemSubtotal(item)
    {
        if (itemIssues(item).length) return null;

        return Math.round(Number(item.qty) * Number(item.cost) * 100) / 100;
    }


    /* ---------------- SELECTED PRODUCTS (CART) ---------------- */
    function renderCart()
    {
        const container = $('poCart');

        $('poCartCount').textContent = cart.length;

        if (!cart.length) {

            container.innerHTML = '<div class="po-empty">No products selected yet.<br>Choose a product from the grid and click <strong>Select</strong>.</div>';

        } else {

            /* Every render rebuilds the names, so items are always indexed 0, 1, 2 … */
            container.innerHTML = cart.map(function (item, i) {

                const product = productById(item.product_id) || { name: '—', brand: null, stock: 0, reorder: 0 };
                const status = stockStatus(product);
                const show = showItemIssues(item);
                const issues = show ? itemIssues(item) : [];
                const subtotal = itemSubtotal(item);

                return '<div class="po-item' + (issues.length ? ' invalid' : '') + '" id="poItem' + i + '">' +

                    '<input type="hidden" name="items[' + i + '][product_id]" value="' + Number(item.product_id) + '">' +

                    '<div class="pi-main">' +
                        '<div class="pi-name">' + escapeHtml(product.name) + '</div>' +
                        '<div class="pi-sub">' + (product.brand ? escapeHtml(product.brand) + ' &middot; ' : '') + 'Current stock: <span class="stock-display">' + Number(product.stock).toLocaleString() + '</span> ' +
                        '<span class="status-badge ' + status.cls + '">' + status.label + '</span></div>' +
                    '</div>' +

                    '<button type="button" class="remove-item" title="Remove product" aria-label="Remove product" onclick="removePoItem(' + i + ')">&times;</button>' +

                    '<div class="pi-fields">' +

                        '<div class="pi-field"><label>Purchase Qty</label>' +
                            '<input type="number" name="items[' + i + '][quantity]" class="' + (show && qtyIssue(item) ? 'bad' : '') + '" min="1" step="1" required value="' + escapeHtml(item.qty) + '" oninput="poSetField(' + i + ', \'qty\', this)">' +
                        '</div>' +

                        '<div class="pi-field"><label>Unit Cost (₱) <span class="info-tip" title="Cost charged by the supplier for this purchase order.">i</span></label>' +
                            '<input type="number" name="items[' + i + '][unit_cost]" class="' + (show && costIssue(item) ? 'bad' : '') + '" min="0" step="0.01" required placeholder="Supplier cost" value="' + escapeHtml(item.cost) + '" oninput="poSetField(' + i + ', \'cost\', this)">' +
                        '</div>' +

                        '<div class="pi-subtotal"><span>Subtotal</span><strong id="poSub' + i + '">' + (subtotal === null ? '—' : formatCurrency(subtotal)) + '</strong></div>' +

                    '</div>' +

                    '<div class="pi-msg' + (issues.length ? '' : ' hidden') + '" id="poMsg' + i + '">' + (issues.length ? '⚠ ' + escapeHtml(issues.join(' ')) : '') + '</div>' +

                    '</div>';

            }).join('');

        }

        updatePurchaseOrderTotal();
    }

    /* Typing never re-renders the row, so focus and the typed value are kept exactly as entered. */
    function poSetField(index, field, input)
    {
        const item = cart[index];

        if (!item) return;

        item[field] = input.value;
        item.dirty = true;

        const show = showItemIssues(item);
        const issues = show ? itemIssues(item) : [];
        const subtotal = itemSubtotal(item);

        const subtotalElement = $('poSub' + index);
        if (subtotalElement) subtotalElement.textContent = subtotal === null ? '—' : formatCurrency(subtotal);

        const message = $('poMsg' + index);
        if (message) {
            message.textContent = issues.length ? '⚠ ' + issues.join(' ') : '';
            message.classList.toggle('hidden', issues.length === 0);
        }

        const row = $('poItem' + index);
        if (row) {
            row.classList.toggle('invalid', issues.length > 0);
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            const costInput = row.querySelector('input[name$="[unit_cost]"]');
            if (qtyInput) qtyInput.classList.toggle('bad', show && qtyIssue(item) !== '');
            if (costInput) costInput.classList.toggle('bad', show && costIssue(item) !== '');
        }

        updatePurchaseOrderTotal();
    }

    /* TOTAL = sum of quantity × purchase unit cost (summed in cents to avoid floating-point drift) */
    function updatePurchaseOrderTotal()
    {
        let cents = 0;

        cart.forEach(function (item) {
            const subtotal = itemSubtotal(item);
            if (subtotal !== null) cents += Math.round(subtotal * 100);
        });

        $('purchaseOrderTotal').textContent = formatCurrency(cents / 100);
    }


    /* ---------------- FORM VALIDATION + DOUBLE-SUBMIT PROTECTION ---------------- */
    function showPoErrors(messages)
    {
        const box = $('poFormErrors');

        box.innerHTML = '<strong>Please fix the following before saving:</strong><ul>' +
            messages.map(function (message) { return '<li>' + escapeHtml(message) + '</li>'; }).join('') + '</ul>';

        box.classList.remove('hidden');

        const content = $('createPurchaseOrderModal').querySelector('.modal-content');
        if (content && content.scrollTo) content.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function hidePoErrors()
    {
        const box = $('poFormErrors');
        box.classList.add('hidden');
        box.innerHTML = '';
    }

    function resetPoSubmitButtons()
    {
        [$('poDraftBtn'), $('poSubmitBtn')].forEach(function (button) {
            button.disabled = false;
            button.textContent = button.dataset.label;
        });
    }

    $('createPurchaseOrderForm').addEventListener('submit', function (event) {

        const submitter = event.submitter;

        if (submitter && submitter.dataset && submitter.dataset.status) {
            $('createPOStatus').value = submitter.dataset.status;
        }

        const errors = [];

        const supplierId = $('poSupplier').value;

        if (!supplierId) {
            errors.push('Please select a supplier.');
            $('supPicker').classList.add('sup-invalid');
        } else if (!supplierById(supplierId)) {
            errors.push('The selected supplier is not valid. Please select the supplier again.');
            $('supPicker').classList.add('sup-invalid');
        }

        if (!$('poDate').value) errors.push('Please enter the PO date.');

        if (cart.length === 0) {
            errors.push('Please select at least one product to purchase.');
        }

        const seen = new Set();

        cart.forEach(function (item, i) {

            const product = productById(item.product_id);
            const name = product ? product.name : ('Item ' + (i + 1));

            if (!item.product_id || !product) {
                errors.push(name + ': the product is not valid. Please remove it and select it again.');
            }

            if (seen.has(Number(item.product_id))) {
                errors.push(name + ' was added more than once. Each product can only appear once.');
            }

            seen.add(Number(item.product_id));

            itemIssues(item).forEach(function (issue) { errors.push(name + ': ' + issue); });

        });

        if (errors.length) {
            event.preventDefault();
            submitAttempted = true;
            renderCart();
            showPoErrors(errors);
            return;
        }

        hidePoErrors();

        /* Lock both submit buttons after a valid submission (deferred so the browser still submits normally). */
        const pending = $('createPOStatus').value === 'pending';

        setTimeout(function () {
            $('poDraftBtn').disabled = true;
            $('poSubmitBtn').disabled = true;
            (pending ? $('poSubmitBtn') : $('poDraftBtn')).textContent = pending ? 'Submitting...' : 'Saving...';
        }, 0);

    });

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            resetPoSubmitButtons();
            $('decisionSubmitButton').disabled = false;
        }
    });


    /* ---------------- DRAFT SUBMIT: opens the same HTML modal (no browser alert / confirm) ---------------- */
    function submitDraftPurchaseOrder(purchaseOrderId)
    {
        openDecisionModal(purchaseOrderId, 'submit');
    }


    /* ---------------- VIEW PURCHASE ORDER (items paginated 15 per page) ---------------- */
    let viewItems = [];
    let viewPage = 1;

    function renderViewItems()
    {
        const container = $('viewPOItems');

        if (!viewItems.length) {

            container.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:25px;color:#64748b;">No products recorded.</td></tr>';
            renderPager($('viewPOPager'), 0, 1, PAGE_SIZE, function () {});
            return;

        }

        const pages = Math.max(1, Math.ceil(viewItems.length / PAGE_SIZE));
        viewPage = Math.min(Math.max(1, viewPage), pages);

        container.innerHTML = viewItems.slice((viewPage - 1) * PAGE_SIZE, viewPage * PAGE_SIZE).map(function (item) {

            return '<tr>' +
                '<td>' + escapeHtml(item.product_name) + '</td>' +
                '<td style="text-align:right;">' + Number(item.quantity || 0).toLocaleString() + '</td>' +
                '<td style="text-align:right;">' + formatCurrency(item.unit_cost) + '</td>' +
                '<td style="text-align:right;font-weight:bold;">' + formatCurrency(item.subtotal) + '</td>' +
                '</tr>';

        }).join('');

        renderPager($('viewPOPager'), viewItems.length, viewPage, PAGE_SIZE, function (target) {
            viewPage = target;
            renderViewItems();
        });
    }

    function viewPurchaseOrder(purchaseOrderId)
    {
        const purchaseOrder = purchaseOrders.find(function (order) { return Number(order.id) === Number(purchaseOrderId); });

        if (!purchaseOrder) return;

        $('viewPONumber').textContent = purchaseOrder.po_number;
        $('viewPONumberValue').textContent = purchaseOrder.po_number;
        $('viewSupplierName').textContent = purchaseOrder.supplier_name;
        $('viewPODate').textContent = purchaseOrder.po_date;
        $('viewCustomerOrder').textContent = purchaseOrder.customer_order;
        $('viewPOStatus').textContent = formatStatus(purchaseOrder.status);
        $('viewCreatedBy').textContent = purchaseOrder.created_by;
        $('viewPONotes').textContent = purchaseOrder.notes || 'No notes recorded.';

        viewItems = Array.isArray(purchaseOrder.items) ? purchaseOrder.items : [];
        viewPage = 1;

        let total = 0;
        viewItems.forEach(function (item) { total += Number(item.subtotal || 0); });

        $('viewPOTotal').textContent = formatCurrency(total);

        renderViewItems();

        openModal('viewPurchaseOrderModal');
    }


    /* ---------------- PURCHASE ORDER ACTIONS: submit / approve / cancel ---------------- */
    const decisionConfig = {
        submit:  { title: 'Submit for Approval',     button: 'Submit for Approval',     cls: 'btn btn-primary' },
        approve: { title: 'Approve Purchase Order',  button: 'Approve Purchase Order',  cls: 'btn btn-success' },
        cancel:  { title: 'Cancel Purchase Order',   button: 'Cancel Purchase Order',   cls: 'btn btn-danger' }
    };

    function openDecisionModal(purchaseOrderId, decision)
    {
        const purchaseOrder = purchaseOrders.find(function (order) { return Number(order.id) === Number(purchaseOrderId); });
        const config = decisionConfig[decision];

        if (!purchaseOrder || !config || !poRoutes[decision]) return;

        let total = 0;
        (purchaseOrder.items || []).forEach(function (item) { total += Number(item.subtotal || 0); });

        $('decisionTitle').textContent = config.title;
        $('decisionPONumber').textContent = purchaseOrder.po_number;
        $('decisionSupplier').textContent = purchaseOrder.supplier_name;
        $('decisionTotal').textContent = formatCurrency(total);

        const submitButton = $('decisionSubmitButton');
        submitButton.disabled = false;
        submitButton.textContent = config.button;
        submitButton.dataset.label = config.button;
        submitButton.className = config.cls;

        /* Named-route URL, numeric id, PATCH is supplied by @method('PATCH') inside the form. */
        $('decisionForm').action = poRoutes[decision].replace('__ID__', encodeURIComponent(purchaseOrder.id));

        openModal('decisionModal');
    }

    $('decisionForm').addEventListener('submit', function () {
        const button = $('decisionSubmitButton');
        setTimeout(function () {
            button.disabled = true;
            button.textContent = 'Processing...';
        }, 0);
    });


    /* ---------------- STATUS LABEL ---------------- */
    function formatStatus(status)
    {
        switch (status) {

            case 'draft': return 'Draft';
            case 'pending': return 'Pending Approval';
            case 'approved': return 'Approved';
            case 'partially_received': return 'Partially Received';
            case 'received': return 'Fully Received';
            case 'cancelled': return 'Cancelled';

            default:
                return String(status || '').replace(/_/g, ' ').replace(/\b\w/g, function (letter) { return letter.toUpperCase(); });

        }
    }


    /* ---------------- CURRENCY ---------------- */
    function formatCurrency(value)
    {
        return '₱' + Number(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }


    /* ---------------- INIT (runs after the shared helpers below are loaded) ---------------- */
    document.addEventListener('DOMContentLoaded', function () {

        filterPurchaseOrders();

        /* After a server-side validation error, put the supplier and items back so nothing the user typed is lost. */
        if (oldSupplierId && supplierById(oldSupplierId)) {
            pickSupplier(oldSupplierId);
        }

        if (Array.isArray(oldItems)) {
            oldItems.forEach(function (row) {
                if (!row || !row.product_id || !productById(row.product_id) || inCart(row.product_id)) return;
                cart.push({
                    product_id: Number(row.product_id),
                    qty: String(row.quantity === undefined || row.quantity === null ? '1' : row.quantity),
                    cost: String(row.unit_cost === undefined || row.unit_cost === null ? '' : row.unit_cost),
                    dirty: true
                });
            });
        }

        if (hasServerErrors && cart.length) {
            openCreatePurchaseOrderModal();
        }

    });

</script>

</div>

<script>

    /* ---------- Shared: modals ---------- */
    function openModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    document.addEventListener('click', function (event) {
        if (event.target.classList && event.target.classList.contains('modal')) {
            event.target.classList.remove('show');
            document.body.style.overflow = '';
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('.modal.show').forEach(function (modal) { modal.classList.remove('show'); });
        document.body.style.overflow = '';
    });

    /* ---------- Shared: red asterisk on every required field ---------- */
    document.querySelectorAll('input[required], select[required], textarea[required]').forEach(function (el) {
        if (el.type === 'hidden') return;
        let label = el.id ? document.querySelector('label[for="' + el.id + '"]') : null;
        if (!label) {
            const group = el.closest('.form-group');
            if (group) label = group.querySelector('label');
        }
        if (!label || label.querySelector('.required')) return;
        const star = document.createElement('span');
        star.className = 'required';
        star.textContent = ' *';
        label.appendChild(star);
    });

    /* ---------- Shared: show / hide toggle on every password input ---------- */
    (function () {
        const eye = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>';
        const eyeOff = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 6.1A10 10 0 0112 5c6.4 0 10 7 10 7a17 17 0 01-3.2 4M6.5 6.6C3.7 8.4 2 12 2 12s3.6 7 10 7a9.8 9.8 0 004.1-.9"/><path d="M9.9 9.9a3 3 0 004.2 4.2"/></svg>';

        document.querySelectorAll('input[type="password"]').forEach(function (input) {
            if (input.parentElement.classList.contains('pw-wrap')) return;
            const wrap = document.createElement('div');
            wrap.className = 'pw-wrap';
            input.parentNode.insertBefore(wrap, input);
            wrap.appendChild(input);
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pw-toggle';
            btn.setAttribute('aria-label', 'Show or hide password');
            btn.innerHTML = eye;
            btn.addEventListener('click', function () {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.innerHTML = show ? eyeOff : eye;
            });
            wrap.appendChild(btn);
        });
    })();

</script>

</body>

</html>