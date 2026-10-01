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
        .btn-sm { padding: 7px 11px; font-size: 12px; }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-dark); }
        .btn-secondary { background: #334155; color: #fff; }
        .btn-secondary:hover { background: #1e293b; }
        .btn-success { background: #16a34a; color: #fff; }
        .btn-success:hover { background: #15803d; }
        .btn-warning { background: var(--warn); color: #fff; }
        .btn-warning:hover { background: #b45309; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-cancel { background: #e2e8f0; color: #334155; }
        .btn-cancel:hover { background: #cbd5e1; }
        .btn-light { background: #eff6ff; color: var(--accent); }
        .btn-light:hover { background: #dbeafe; }

        /* ALERTS / NOTICES */
        .alert-success, .alert-error, .info-notice, .warning-notice { padding: 12px 15px; border-radius: 10px; margin-bottom: 18px; font-size: 13px; line-height: 1.5; border: 1px solid; }
        .alert-success { background: #dcfce7; color: #166534; border-color: #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border-color: #fecaca; }
        .alert-error ul { margin: 8px 0 0 20px; padding: 0; }
        .info-notice { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
        .warning-notice { background: #fffbeb; color: #92400e; border-color: #fde68a; }

        /* KPI CARDS */
        .cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 22px; }

        .card { display: block; background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 20px; position: relative; overflow: hidden; cursor: pointer; transition: transform .15s, box-shadow .15s, border-color .15s; }
        .card::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--card-color, var(--accent)); }
        .card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(15, 23, 42, .09); border-color: #cbd5e1; }
        .card-label { font-size: 13px; color: var(--muted); margin-bottom: 10px; }
        .card-value { font-size: 30px; font-weight: 700; }
        .card-link { margin-top: 10px; font-size: 12px; font-weight: 600; color: var(--card-color, var(--accent)); }

        /* PANELS */
        .panel { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 22px; }
        .panel-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; gap: 10px; }
        .panel h2 { margin: 0; font-size: 17px; }
        .panel-link { font-size: 13px; font-weight: 600; color: var(--accent); cursor: pointer; }
        .panel-link:hover { text-decoration: underline; }
        .chart-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 22px; }
        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }

        .donut-wrap { display: flex; flex-direction: column; align-items: center; gap: 16px; }
        .donut { width: 170px; height: 170px; }
        .donut a circle { transition: stroke-width .15s; cursor: pointer; }
        .donut a:hover circle { stroke-width: 26; }
        .donut-total { font-size: 26px; font-weight: 700; fill: var(--ink); }
        .donut-sub { font-size: 11px; fill: var(--muted); }
        .legend { width: 100%; display: flex; flex-direction: column; gap: 6px; }
        .legend a { display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; border-radius: 8px; font-size: 13px; cursor: pointer; transition: background .15s; }
        .legend a:hover { background: #f1f5f9; }
        .legend .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 8px; }

        .bar-list { display: flex; flex-direction: column; gap: 10px; }
        .bar-item { display: block; padding: 10px 12px; border-radius: 10px; cursor: pointer; transition: background .15s; }
        .bar-item:hover { background: #f1f5f9; }
        .bar-top { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 7px; }
        .bar-track { height: 10px; background: #e2e8f0; border-radius: 999px; overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 999px; background: var(--bar-color, var(--accent)); min-width: 3px; }

        /* FILTER */
        .filter-box { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 16px; margin-bottom: 18px; display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
        .filter-box input, .filter-box select { padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; background: #fff; font-family: inherit; }
        .filter-box input[type=text] { min-width: 300px; flex: 1; }
        .filter-box input:focus, .filter-box select:focus { border-color: var(--accent); }
        .filter-grid { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 12px; align-items: end; width: 100%; }
        .filter-grid.report-filter { grid-template-columns: 1fr 1fr auto auto; }
        .filter-actions { display: flex; gap: 8px; }

        /* TABLE */
        .table-panel { background: #fff; border: 1px solid var(--line); border-radius: 14px; overflow: hidden; margin-bottom: 22px; }
        .table-header, .section-header { padding: 16px 22px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .table-header h2, .section-header h2 { margin: 0; font-size: 16px; }
        .table-header p, .section-header p { margin: 4px 0 0; font-size: 12px; color: var(--muted); }
        .table-count { font-size: 13px; color: var(--muted); }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid var(--line); text-align: left; font-size: 13px; vertical-align: middle; white-space: nowrap; }
        th { color: var(--muted); font-weight: 600; background: #f8fafc; }
        tbody tr:hover td { background: #fafbfd; }
        tbody tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }
        .empty-state, .empty { color: var(--muted); font-size: 14px; padding: 36px 20px; text-align: center; }
        .secondary-text { color: var(--muted); font-size: 12px; margin-top: 3px; }
        .actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }

        /* BADGES */
        .status-badge, .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
        .status-pending, .status-low-stock, .badge-yellow, .status-inactive { background: #fef3c7; color: #92400e; }
        .status-available, .status-fulfilled, .status-in-stock, .status-received, .status-active, .badge-green { background: #dcfce7; color: #166534; }
        .status-insufficient, .status-cancelled, .status-out-stock, .badge-red { background: #fee2e2; color: #991b1b; }
        .status-confirmed, .status-approved, .badge-blue { background: #dbeafe; color: #1e40af; }
        .status-purchasing, .status-partial { background: #ffedd5; color: #9a3412; }
        .status-ready { background: #e0f2fe; color: #075985; }
        .status-delivered { background: #ede9fe; color: #6d28d9; }
        .status-default, .status-draft, .status-archived, .badge-gray { background: #e5e7eb; color: #374151; }
        .movement-badge { display: inline-block; padding: 4px 9px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .movement-in { background: #dcfce7; color: #166534; }
        .movement-out { background: #fee2e2; color: #991b1b; }
        .movement-adjustment { background: #e5e7eb; color: #374151; }
        .movement-positive { color: #166534; font-weight: 700; }
        .movement-negative { color: #991b1b; font-weight: 700; }
        .check-by { display: block; color: var(--muted); margin-top: 3px; font-size: 11px; }

        /* TABS */
        .tabs, .inventory-tabs { display: flex; gap: 6px; margin-bottom: 18px; flex-wrap: wrap; background: #fff; border: 1px solid var(--line); border-radius: 12px; padding: 5px; width: fit-content; max-width: 100%; }
        .tab-button, .inventory-tab { border: none; background: transparent; color: var(--muted); padding: 10px 18px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; font-family: inherit; }
        .tab-button:hover, .inventory-tab:hover { background: #f1f5f9; color: var(--ink); }
        .tab-button.active, .inventory-tab.active { background: var(--accent); color: #fff; }
        .tab-content, .inventory-section { display: none; }
        .tab-content.active, .inventory-section.active { display: block; }

        /* SUMMARY */
        .summary-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; margin-bottom: 22px; }
        .summary-grid.four { grid-template-columns: repeat(4, 1fr); }

        /* MODAL */
        .modal { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, .55); align-items: center; justify-content: center; padding: 20px; z-index: 1000; }
        .modal.show { display: flex; }
        .modal-content { background: #fff; width: 100%; max-width: 650px; max-height: 90vh; overflow-y: auto; border-radius: 16px; box-shadow: 0 25px 60px rgba(0, 0, 0, .3); }
        .modal-large { max-width: 1100px; }
        .modal-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 18px 22px; border-bottom: 1px solid var(--line); position: sticky; top: 0; background: #fff; z-index: 2; }
        .modal-header h2 { margin: 0; font-size: 18px; }
        .modal-header p { margin: 5px 0 0; color: var(--muted); font-size: 13px; }
        .modal-body { padding: 22px; }
        .close-x, .close-button { border: none; background: transparent; font-size: 26px; line-height: 1; cursor: pointer; color: var(--muted); border-radius: 6px; width: 34px; height: 34px; flex-shrink: 0; }
        .close-x:hover, .close-button:hover { background: #f1f5f9; color: var(--ink); }
        .data-modal-actions { display: flex; align-items: center; gap: 10px; }
        .data-modal-body { overflow: auto; }
        .data-modal-body .table-panel { border: none; border-radius: 0; margin: 0; }
        .modal-loading { padding: 50px; text-align: center; color: var(--muted); font-size: 14px; }

        /* FORM */
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 7px; color: #334155; }
        .form-group small { display: block; color: var(--muted); font-weight: 400; margin-top: 5px; line-height: 1.4; font-size: 12px; }
        .form-group label small { display: inline; margin: 0; }
        .form-group input, .form-group select, .form-group textarea, .order-items-container input, .order-items-container select, .po-items-container input, .po-items-container select { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; font-family: inherit; background: #fff; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0 16px; }
        .form-grid-three { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0 16px; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; flex-wrap: wrap; }
        .required { color: var(--danger); font-weight: 700; }
        .category-row { display: flex; gap: 8px; }
        .category-row select { flex: 1; }
        .pw-wrap { position: relative; }
        .pw-wrap input { padding-right: 42px !important; }
        .pw-toggle { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: none; background: transparent; cursor: pointer; color: var(--muted); padding: 4px; display: flex; }
        .pw-toggle:hover { color: var(--ink); }
        .pw-toggle svg { width: 18px; height: 18px; }

        /* DETAILS */
        .details-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px 24px; margin-bottom: 22px; }
        .details-grid.two { grid-template-columns: repeat(2, 1fr); }
        .detail-item { padding-bottom: 12px; border-bottom: 1px solid var(--line); }
        .detail-label { display: block; color: var(--muted); font-size: 12px; margin-bottom: 5px; }
        .detail-value { font-weight: 600; color: var(--ink); font-size: 14px; white-space: normal; }
        .detail-row { display: flex; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .detail-row .detail-label { width: 170px; margin: 0; }
        .detail-row .detail-value { flex: 1; }
        .status-box, .decision-box { background: #f8fafc; border: 1px solid var(--line); border-radius: 10px; padding: 14px 15px; margin-top: 14px; }
        .status-box-title, .decision-title { font-size: 12px; font-weight: 700; color: var(--muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: .04em; }
        .decision-text { font-size: 13px; line-height: 1.5; color: #475569; }

        /* ORDER ITEMS */
        .order-items-container, .po-items-container { border: 1px solid var(--line); border-radius: 10px; overflow-x: auto; }
        .order-items-container table, .po-items-container table, .movement-table table { min-width: 800px; }
        .movement-table { overflow-x: auto; border: 1px solid var(--line); border-radius: 10px; }
        .movement-table table { min-width: 1250px; }
        .stock-display { font-weight: 700; }
        .stock-available { color: #166534; } .stock-low { color: #92400e; } .stock-out { color: #991b1b; } .stock-neutral { color: #374151; }
        .remove-item { border: none; background: transparent; color: var(--danger); font-size: 20px; cursor: pointer; }
        .order-total-box { display: flex; justify-content: flex-end; margin: 16px 0; }
        .order-total { display: flex; justify-content: space-between; align-items: center; gap: 50px; min-width: 300px; background: #f8fafc; border: 1px solid var(--line); padding: 14px 18px; border-radius: 10px; }
        .order-total-label { font-weight: 600; color: #334155; }
        .order-total-value { font-size: 20px; font-weight: 700; }
        .check-result { padding: 14px; border-radius: 10px; margin-top: 16px; border: 1px solid var(--line); }
        .check-result.pending { background: #fffbeb; border-color: #fde68a; }
        .check-result.available { background: #f0fdf4; border-color: #bbf7d0; }
        .check-result.insufficient { background: #fef2f2; border-color: #fecaca; }
        .check-title { font-weight: 700; margin-bottom: 4px; }
        .check-text { font-size: 13px; line-height: 1.5; }
        .stock-info { background: #f8fafc; border: 1px solid var(--line); border-radius: 10px; padding: 12px 14px; margin-bottom: 16px; }
        .stock-info-row { display: flex; justify-content: space-between; gap: 15px; padding: 4px 0; font-size: 14px; }
        .stock-info-label { color: var(--muted); }
        .stock-info-value { font-weight: 700; }
        .section-row { display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap; margin-bottom: 12px; }
        .section-row h3 { margin: 0; }

        /* PRODUCT / STOCK CARD */
        .product-info { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 20px; margin-bottom: 20px; }
        .product-info h2, .stock-card-header h2 { margin: 0 0 14px; font-size: 16px; }
        .detail-box { background: #f8fafc; border: 1px solid var(--line); border-radius: 10px; padding: 13px; }
        .info-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .stock-card-table th, .stock-card-table td { border: 1px solid var(--line); }
        .stock-card-table th { text-align: center; }
        .receive-header { background: #dcfce7 !important; color: #166534 !important; }
        .sale-header { background: #fee2e2 !important; color: #991b1b !important; }
        .stock-header { background: #dbeafe !important; color: #1e40af !important; font-weight: 700; text-align: center; }
        .stock-in-row td { background: #f7fef9; } .stock-out-row td { background: #fffafa; } .adjustment-row td { background: #fffbeb; }
        .adjustment-info { color: #92400e; font-size: 12px; white-space: normal; }
        .select-message { background: #fff; border: 1px solid var(--line); border-radius: 14px; padding: 50px 20px; text-align: center; color: var(--muted); }
        .select-message h2 { margin-top: 0; color: #334155; }
        .print-only { display: none; }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .chart-grid { grid-template-columns: 1fr 1fr; }
            .chart-grid .panel:last-child { grid-column: 1 / -1; }
        }
        @media (max-width: 1000px) {
            .cards, .summary-grid.four, .info-grid { grid-template-columns: repeat(2, 1fr); }
            .details-grid, .form-grid-three { grid-template-columns: repeat(2, 1fr); }
            .dashboard-grid { grid-template-columns: 1fr; }
            .filter-grid, .filter-grid.report-filter { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 800px) {
            .layout { flex-direction: column; }
            .sidebar { position: relative; width: 100%; }
            .main { margin-left: 0; padding: 20px; }
            .chart-grid, .summary-grid { grid-template-columns: 1fr; }
            .form-grid, .form-grid-three, .details-grid, .details-grid.two { grid-template-columns: 1fr; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .filter-box input[type=text] { min-width: 100%; }
        }
        @media (max-width: 560px) {
            .cards, .summary-grid.four, .info-grid, .filter-grid, .filter-grid.report-filter { grid-template-columns: 1fr; }
        }

        /* PRINT (Save as PDF) */
        @media print {
            .sidebar, .topbar-actions, .filter-box, .tabs, .no-print, .modal, .actions, .alert-success, .alert-error { display: none !important; }
            .main { margin-left: 0; padding: 0; }
            body { background: #fff; }
            .tab-content { display: block !important; }
            .table-wrapper { overflow: visible; }
            table { min-width: 0 !important; }
            .table-panel, .panel, .card, .product-info { box-shadow: none; border: 1px solid #ccc; break-inside: avoid; }
            .print-only { display: block; }
            .cards { grid-template-columns: repeat(4, 1fr); }
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

            <a href="{{ route('owner.products') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5v8l9 5 9-5z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
                Products
            </a>

            <a href="{{ route('owner.inventory') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/></svg>
                Inventory
            </a>

            <a href="{{ route('owner.suppliers') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="12" height="9" rx="1"/><path d="M14 10h4l3 3v3h-7"/><circle cx="7" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/></svg>
                Suppliers
            </a>

            <a href="{{ route('owner.customer-orders') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18H6z"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>
                Customer Orders
            </a>

            <a href="{{ route('owner.purchase-orders') }}" class="active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.5 10h10L20 7H7"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></svg>
                Purchase Orders
            </a>

            <a href="{{ route('owner.stock-card') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                Stock Card
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

        @if(session('warning'))
            <div class="warning-notice">{{ session('warning') }}</div>
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

                                        <button type="button" class="btn btn-sm btn-secondary" onclick="viewPurchaseOrder({{ $purchaseOrder->id }})">View</button>

                                        @if($purchaseOrder->status === 'pending')

                                            <button type="button" class="btn btn-sm btn-success" onclick="openDecisionModal({{ $purchaseOrder->id }}, 'approve')">Approve</button>

                                            <button type="button" class="btn btn-sm btn-danger" onclick="openDecisionModal({{ $purchaseOrder->id }}, 'cancel')">Cancel</button>

                                        @elseif($purchaseOrder->status === 'draft')

                                            <span class="secondary-text">Draft</span>

                                        @elseif($purchaseOrder->status === 'approved')

                                            <span class="secondary-text" style="color:#1e40af;">Waiting for supplier delivery</span>

                                        @elseif($purchaseOrder->status === 'partially_received')

                                            <a href="{{ route('owner.inventory') }}" class="btn btn-sm btn-primary">Stock In</a>

                                        @elseif($purchaseOrder->status === 'received')

                                            <span class="secondary-text" style="color:#166534;">Completed</span>

                                        @elseif($purchaseOrder->status === 'cancelled')

                                            <span class="secondary-text" style="color:#991b1b;">Cancelled</span>

                                        @endif

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr><td colspan="8" class="empty-state">No purchase orders recorded yet.</td></tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </main>


{{-- CREATE PURCHASE ORDER MODAL --}}
<div class="modal" id="createPurchaseOrderModal">
    <div class="modal-content modal-large">

        <div class="modal-header">
            <div>
                <h2>New Purchase Order</h2>
                <p>Prepare an order for a supplier.</p>
            </div>
            <button type="button" class="close-x" onclick="closeModal('createPurchaseOrderModal')">&times;</button>
        </div>

        <div class="modal-body">

            <form action="{{ route('owner.purchase-orders.store') }}" method="POST" id="createPurchaseOrderForm">

                @csrf

                <input type="hidden" name="status" value="pending">

                <div class="form-grid-three">

                    <div class="form-group">
                        <label>Supplier</label>
                        <select name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name ?? $supplier->name ?? 'Unnamed Supplier' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Customer Order</label>
                        <select name="customer_order_id">
                            <option value="">None / General Stock Purchase</option>
                            @foreach($customerOrders as $customerOrder)
                                <option value="{{ $customerOrder->id }}" {{ old('customer_order_id') == $customerOrder->id ? 'selected' : '' }}>
                                    {{ $customerOrder->order_number }} — {{ $customerOrder->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>PO Date</label>
                        <input type="date" name="po_date" value="{{ old('po_date', date('Y-m-d')) }}" required>
                    </div>

                </div>

                <div class="section-row">
                    <h3>Products to Purchase <span class="required">*</span></h3>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addPurchaseOrderItem()">+ Add Product</button>
                </div>

                <div class="po-items-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Product <span class="required">*</span></th>
                                <th style="width:130px;">Current Stock</th>
                                <th style="width:140px;">Purchase Qty <span class="required">*</span></th>
                                <th style="width:160px;">Unit Cost <span class="required">*</span></th>
                                <th style="width:150px;">Subtotal</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="purchaseOrderItemsContainer"></tbody>
                    </table>
                </div>

                <div class="order-total-box">
                    <div class="order-total">
                        <span class="order-total-label">Purchase Order Total</span>
                        <span class="order-total-value" id="purchaseOrderTotal">₱0.00</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" maxlength="2000" placeholder="Additional purchasing notes...">{{ old('notes') }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('createPurchaseOrderModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Purchase Order</button>
                </div>

            </form>

        </div>

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


{{-- OWNER DECISION MODAL --}}
<div class="modal" id="decisionModal">
    <div class="modal-content">

        <div class="modal-header">
            <div>
                <h2>Purchase Order Decision</h2>
                <p id="decisionPONumber"></p>
            </div>
            <button type="button" class="close-x" onclick="closeModal('decisionModal')">&times;</button>
        </div>

        <div class="modal-body">

            <div class="info-notice" id="decisionMessage"></div>

            <form method="POST" id="decisionForm">

                @csrf
                @method('PATCH')

                <div class="decision-box">
                    <div class="decision-title" id="decisionTitle"></div>
                    <div class="decision-text" id="decisionDescription"></div>
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


    $productJavascriptData = $products->map(function ($product) {

        return [
            'id' => $product->id,
            'name' => $product->product_name,
            'unit' => $product->unit,
            'price' => (float) $product->unit_price,
            'stock' => $product->inventory ? (int) $product->inventory->current_stock : 0,
        ];

    })->values();

@endphp


<script>

    const purchaseOrders = @js($purchaseOrderJavascriptData);

    const products = @js($productJavascriptData);


    /* CREATE PURCHASE ORDER */
    function openCreatePurchaseOrderModal()
    {
        const container = document.getElementById('purchaseOrderItemsContainer');

        if (container.children.length === 0) {
            addPurchaseOrderItem();
        }

        openModal('createPurchaseOrderModal');
    }


    /* ADD PRODUCT */
    function addPurchaseOrderItem()
    {
        const container = document.getElementById('purchaseOrderItemsContainer');

        const index = container.children.length;

        const row = document.createElement('tr');

        row.innerHTML = `

            <td>
                <select name="items[${index}][product_id]" class="product-select" required onchange="updatePurchaseProductRow(this)">
                    <option value="">Select Product</option>
                    ${
                        products.map(function (product) {
                            return `<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">${escapeHtml(product.name)}</option>`;
                        }).join('')
                    }
                </select>
            </td>

            <td><span class="stock-display">—</span></td>

            <td>
                <input type="number" name="items[${index}][quantity]" class="quantity-input" value="1" min="1" step="1" required oninput="updatePurchaseRowSubtotal(this)">
            </td>

            <td>
                <input type="number" name="items[${index}][unit_cost]" class="unit-cost-input" value="0.00" min="0" step="0.01" required oninput="updatePurchaseRowSubtotal(this)">
            </td>

            <td><span class="subtotal-display">₱0.00</span></td>

            <td style="text-align:center;">
                <button type="button" class="remove-item" onclick="removePurchaseOrderItem(this)" title="Remove product">&times;</button>
            </td>

        `;

        container.appendChild(row);
    }


    /* PRODUCT CHANGE */
    function updatePurchaseProductRow(select)
    {
        const row = select.closest('tr');

        const option = select.options[select.selectedIndex];

        if (!option) return;

        const price = Number(option.dataset.price || 0);
        const stock = Number(option.dataset.stock || 0);

        const stockDisplay = row.querySelector('.stock-display');
        const unitCostInput = row.querySelector('.unit-cost-input');

        stockDisplay.textContent = stock.toLocaleString();

        stockDisplay.classList.remove('stock-available', 'stock-low', 'stock-out');

        if (stock <= 0) {
            stockDisplay.classList.add('stock-out');
        } else if (stock <= 5) {
            stockDisplay.classList.add('stock-low');
        } else {
            stockDisplay.classList.add('stock-available');
        }

        /* Product unit price is only a suggested starting cost; the Owner can change it. */
        if (unitCostInput && (Number(unitCostInput.value) === 0 || unitCostInput.value === '')) {
            unitCostInput.value = price.toFixed(2);
        }

        updatePurchaseRowSubtotal(unitCostInput);
    }


    /* SUBTOTAL */
    function updatePurchaseRowSubtotal(input)
    {
        if (!input) return;

        const row = input.closest('tr');

        if (!row) return;

        const quantity = Number(row.querySelector('.quantity-input')?.value || 0);
        const unitCost = Number(row.querySelector('.unit-cost-input')?.value || 0);

        row.querySelector('.subtotal-display').textContent = formatCurrency(quantity * unitCost);

        updatePurchaseOrderTotal();
    }


    /* TOTAL */
    function updatePurchaseOrderTotal()
    {
        let total = 0;

        document.querySelectorAll('#purchaseOrderItemsContainer .subtotal-display').forEach(function (element) {

            const value = element.textContent.replace('₱', '').replace(/,/g, '');

            total += Number(value) || 0;

        });

        document.getElementById('purchaseOrderTotal').textContent = formatCurrency(total);
    }


    /* REMOVE ITEM */
    function removePurchaseOrderItem(button)
    {
        const row = button.closest('tr');

        if (row) row.remove();

        reindexPurchaseOrderItems();

        updatePurchaseOrderTotal();
    }


    /* REINDEX */
    function reindexPurchaseOrderItems()
    {
        document.querySelectorAll('#purchaseOrderItemsContainer tr').forEach(function (row, index) {

            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            const unitCostInput = row.querySelector('.unit-cost-input');

            if (productSelect) productSelect.name = `items[${index}][product_id]`;
            if (quantityInput) quantityInput.name = `items[${index}][quantity]`;
            if (unitCostInput) unitCostInput.name = `items[${index}][unit_cost]`;

        });
    }


    /* VIEW PURCHASE ORDER */
    function viewPurchaseOrder(purchaseOrderId)
    {
        const purchaseOrder = purchaseOrders.find(function (order) { return Number(order.id) === Number(purchaseOrderId); });

        if (!purchaseOrder) return;

        document.getElementById('viewPONumber').textContent = purchaseOrder.po_number;
        document.getElementById('viewPONumberValue').textContent = purchaseOrder.po_number;
        document.getElementById('viewSupplierName').textContent = purchaseOrder.supplier_name;
        document.getElementById('viewPODate').textContent = purchaseOrder.po_date;
        document.getElementById('viewCustomerOrder').textContent = purchaseOrder.customer_order;
        document.getElementById('viewPOStatus').textContent = formatStatus(purchaseOrder.status);
        document.getElementById('viewCreatedBy').textContent = purchaseOrder.created_by;
        document.getElementById('viewPONotes').textContent = purchaseOrder.notes || 'No notes recorded.';

        const container = document.getElementById('viewPOItems');

        container.innerHTML = '';

        let total = 0;

        if (!purchaseOrder.items || purchaseOrder.items.length === 0) {

            container.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:25px;color:#64748b;">No products recorded.</td></tr>';

        } else {

            purchaseOrder.items.forEach(function (item) {

                total += Number(item.subtotal || 0);

                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${escapeHtml(item.product_name)}</td>
                    <td style="text-align:right;">${Number(item.quantity || 0).toLocaleString()}</td>
                    <td style="text-align:right;">${formatCurrency(item.unit_cost)}</td>
                    <td style="text-align:right;font-weight:bold;">${formatCurrency(item.subtotal)}</td>
                `;

                container.appendChild(row);

            });

        }

        document.getElementById('viewPOTotal').textContent = formatCurrency(total);

        openModal('viewPurchaseOrderModal');
    }


    /* OWNER DECISION */
    function openDecisionModal(purchaseOrderId, decision)
    {
        const purchaseOrder = purchaseOrders.find(function (order) { return Number(order.id) === Number(purchaseOrderId); });

        if (!purchaseOrder) return;

        document.getElementById('decisionPONumber').textContent = purchaseOrder.po_number;

        const message = document.getElementById('decisionMessage');
        const title = document.getElementById('decisionTitle');
        const description = document.getElementById('decisionDescription');
        const submitButton = document.getElementById('decisionSubmitButton');
        const form = document.getElementById('decisionForm');

        if (decision === 'approve') {

            message.className = 'info-notice';
            message.innerHTML = '<strong>Approve Purchase Order</strong><br>Inventory will not increase until Stock In is recorded.';

            title.textContent = 'Approve Purchase Order';
            description.textContent = 'The supplier may proceed with the order.';

            submitButton.textContent = 'Approve Purchase Order';
            submitButton.className = 'btn btn-success';

            form.action = "{{ url('/owner/purchase-orders') }}/" + purchaseOrder.id + "/approve";

        } else if (decision === 'cancel') {

            message.className = 'warning-notice';
            message.innerHTML = '<strong>Cancel Purchase Order</strong><br>This order will no longer proceed.';

            title.textContent = 'Cancel Purchase Order';
            description.textContent = 'Use this when the order should not go ahead with the supplier.';

            submitButton.textContent = 'Cancel Purchase Order';
            submitButton.className = 'btn btn-danger';

            form.action = "{{ url('/owner/purchase-orders') }}/" + purchaseOrder.id + "/cancel";

        }

        openModal('decisionModal');
    }


    /* FILTER */
    function filterPurchaseOrders()
    {
        const search = document.getElementById('poSearch').value.toLowerCase().trim();
        const status = document.getElementById('statusFilter').value;

        document.querySelectorAll('#purchaseOrdersTable tbody tr').forEach(function (row) {

            const poNumber = row.dataset.poNumber || '';
            const supplierName = row.dataset.supplierName || '';
            const rowStatus = row.dataset.status || '';

            const matchesSearch = poNumber.includes(search) || supplierName.includes(search);
            const matchesStatus = status === '' || rowStatus === status;

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';

        });
    }


    /* STATUS LABEL */
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


    /* CURRENCY */
    function formatCurrency(value)
    {
        return '₱' + Number(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }


    /* CREATE PO VALIDATION */
    document.getElementById('createPurchaseOrderForm').addEventListener('submit', function (event) {

        const rows = document.querySelectorAll('#purchaseOrderItemsContainer tr');

        if (rows.length === 0) {
            event.preventDefault();
            alert('Please add at least one product.');
            return;
        }

        let hasEmptyProduct = false;
        let hasInvalidQuantity = false;
        let hasInvalidUnitCost = false;
        let hasDuplicateProduct = false;

        const selectedProducts = new Set();

        rows.forEach(function (row) {

            const product = row.querySelector('.product-select').value;
            const quantity = Number(row.querySelector('.quantity-input').value);
            const unitCost = Number(row.querySelector('.unit-cost-input').value);

            if (!product) hasEmptyProduct = true;
            if (!quantity || quantity < 1) hasInvalidQuantity = true;
            if (isNaN(unitCost) || unitCost < 0) hasInvalidUnitCost = true;
            if (product && selectedProducts.has(product)) hasDuplicateProduct = true;
            if (product) selectedProducts.add(product);

        });

        if (hasEmptyProduct) {
            event.preventDefault();
            alert('Please select a product for every Purchase Order item.');
            return;
        }

        if (hasInvalidQuantity) {
            event.preventDefault();
            alert('Every purchase quantity must be at least 1.');
            return;
        }

        if (hasInvalidUnitCost) {
            event.preventDefault();
            alert('Every unit cost must be a valid amount of 0 or higher.');
            return;
        }

        if (hasDuplicateProduct) {
            event.preventDefault();
            alert('The same product cannot be added more than once. Combine the quantity into one line.');
            return;
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