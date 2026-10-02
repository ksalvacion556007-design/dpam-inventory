<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventory - DPAM IMS</title>

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

            <a href="{{ route('owner.inventory') }}" class="active">
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

            <a href="{{ route('owner.purchase-orders') }}" class="">
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
                <h1 class="page-title">Inventory</h1>
                <div class="page-description">Current stock levels, movements and releases.</div>
            </div>
            <div class="topbar-actions">
                <button type="button" class="btn btn-primary" onclick="openModal('stockInModal')">+ Stock In</button>
                <button type="button" class="btn btn-secondary" onclick="openModal('adjustModal')">Adjust Stock</button>
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



        <div class="inventory-tabs">
            <button type="button" class="inventory-tab active" onclick="showInventoryTab('currentInventorySection', this)">Current Inventory</button>
            <button type="button" class="inventory-tab" onclick="showInventoryTab('movementHistorySection', this)">All Movement History</button>
        </div>


        {{-- CURRENT INVENTORY --}}
        <div class="inventory-section active" id="currentInventorySection">

            <div class="filter-box">
                <input type="text" id="searchInput" placeholder="Search product..." oninput="filterInventory()">
                <select id="statusFilter" onchange="filterInventory()">
                    <option value="">All Stock Status</option>
                    <option value="In Stock">In Stock</option>
                    <option value="Needs Restocking">Needs Restocking</option>
                    <option value="Out of Stock">Out of Stock</option>
                </select>
            </div>

            <div class="table-panel">
                <div class="table-wrapper">

                    <table id="inventoryTable">

                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Current Stock</th>
                                <th>Reorder Level</th>
                                <th>Stock Status</th>
                                <th>Inventory Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($products as $product)

                                @php
                                    $currentStock = $product->inventory->current_stock ?? 0;
                                    $reorderLevel = $product->reorder_level;

                                    if ($currentStock <= 0) {
                                        $stockStatus = 'Out of Stock';
                                        $statusClass = 'status-out-stock';
                                    } elseif ($currentStock <= $reorderLevel) {
                                        $stockStatus = 'Needs Restocking';
                                        $statusClass = 'status-low-stock';
                                    } else {
                                        $stockStatus = 'In Stock';
                                        $statusClass = 'status-in-stock';
                                    }

                                    $inventoryAmount = $currentStock * (float) $product->unit_price;
                                @endphp

                                <tr data-product-name="{{ strtolower($product->product_name) }}" data-stock-status="{{ $stockStatus }}">
                                    <td><strong>{{ $product->product_name }}</strong></td>
                                    <td>{{ $product->category->category_name ?? '—' }}</td>
                                    <td>{{ $product->unit }}</td>
                                    <td><strong>{{ number_format($currentStock) }}</strong></td>
                                    <td>{{ number_format($reorderLevel) }}</td>
                                    <td><span class="status-badge {{ $statusClass }}">{{ $stockStatus }}</span></td>
                                    <td>₱{{ number_format($inventoryAmount, 2) }}</td>
                                    <td>
                                        <div class="actions">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="openViewModal({{ $product->id }})">View</button>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="openStockOutModal({{ $product->id }})">Stock Out</button>
                                        </div>
                                    </td>
                                </tr>

                            @empty

                                <tr><td colspan="8" class="empty-state">No inventory records found.</td></tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>
            </div>

        </div>


        {{-- MOVEMENT HISTORY --}}
        <div class="inventory-section" id="movementHistorySection">

            <div class="filter-box">
                <input type="text" id="movementSearchInput" placeholder="Search product in movement history..." oninput="filterMovements()">
                <select id="movementTypeFilter" onchange="filterMovements()">
                    <option value="">All Movement Types</option>
                    <option value="stock_in">Stock In</option>
                    <option value="stock_out">Stock Out</option>
                    <option value="adjustment">Adjustment</option>
                </select>
            </div>

            <div class="table-panel">
                <div class="table-wrapper">

                    <table id="movementHistoryTable" style="min-width:1250px;">

                        <thead>
                            <tr>
                                <th>Transaction Date</th>
                                <th>Recorded At</th>
                                <th>Product</th>
                                <th>Movement</th>
                                <th>Quantity</th>
                                <th>Before</th>
                                <th>After</th>
                                <th>Reason</th>
                                <th>Customer Order</th>
                                <th>Receipt No.</th>
                                <th>Received By</th>
                                <th>Reference</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($movements as $movement)

                                @php
                                    if ($movement->movement_type === 'stock_in') {
                                        $movementLabel = 'Stock In';
                                        $movementClass = 'movement-in';
                                    } elseif ($movement->movement_type === 'stock_out') {
                                        $movementLabel = 'Stock Out';
                                        $movementClass = 'movement-out';
                                    } else {
                                        $movementLabel = 'Adjustment';
                                        $movementClass = 'movement-adjustment';
                                    }

                                    $quantity = (int) $movement->quantity;
                                @endphp

                                <tr data-movement-product="{{ strtolower($movement->product->product_name ?? '') }}" data-movement-type="{{ $movement->movement_type }}">
                                    <td>{{ $movement->transaction_date ? $movement->transaction_date->format('M d, Y') : '—' }}</td>
                                    <td>{{ $movement->created_at ? $movement->created_at->format('M d, Y h:i A') : '—' }}</td>
                                    <td><strong>{{ $movement->product->product_name ?? '—' }}</strong></td>
                                    <td><span class="movement-badge {{ $movementClass }}">{{ $movementLabel }}</span></td>
                                    <td>
                                        @if($quantity > 0)
                                            <span class="movement-positive">+{{ number_format($quantity) }}</span>
                                        @elseif($quantity < 0)
                                            <span class="movement-negative">{{ number_format($quantity) }}</span>
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>{{ number_format($movement->stock_before) }}</td>
                                    <td>{{ number_format($movement->stock_after) }}</td>
                                    <td>{{ $movement->reason ?? '—' }}</td>
                                    <td>{{ $movement->customer_order_reference ?? '—' }}</td>
                                    <td>{{ $movement->receipt_number ?? '—' }}</td>
                                    <td>{{ $movement->received_by ?? '—' }}</td>
                                    <td>{{ $movement->reference ?? '—' }}</td>
                                    <td>{{ $movement->user->name ?? '—' }}</td>
                                </tr>

                            @empty

                                <tr><td colspan="13" class="empty-state">No inventory movements recorded yet.</td></tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>
            </div>

        </div>

    </main>


{{-- STOCK IN MODAL --}}
<div class="modal" id="stockInModal">
    <div class="modal-content">

        <div class="modal-header">
            <h2>Stock In</h2>
            <button type="button"
                    class="close-x"
                    onclick="closeModal('stockInModal')">
                &times;
            </button>
        </div>

        <div class="modal-body">

            <form action="{{ route('owner.inventory.stock-in') }}"
                  method="POST">

                @csrf

                {{-- TRANSACTION DATE --}}
                <div class="form-group">
                    <label>Transaction Date</label>

                    <input type="date"
                           name="transaction_date"
                           value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                           required>

                    <small>
                        Actual date the supplier delivery was received.
                    </small>
                </div>


                {{-- PURCHASE ORDER --}}
                <div class="form-group">
                    <label>Purchase Order</label>

                    <select name="purchase_order_id"
                            id="stockInPurchaseOrder"
                            required
                            onchange="populateStockInProducts(this.value)">

                        <option value="">
                            Select Purchase Order
                        </option>

                        @foreach($purchaseOrders as $purchaseOrder)

                            <option value="{{ $purchaseOrder->id }}">
                                {{ $purchaseOrder->po_number }}
                                —
                                {{ $purchaseOrder->supplier->supplier_name ?? 'No Supplier' }}
                                —
                                {{ ucfirst(str_replace('_', ' ', $purchaseOrder->status)) }}
                            </option>

                        @endforeach

                    </select>

                    <small>
                        Select an Approved or Partially Received Purchase Order.
                    </small>
                </div>


                {{-- PRODUCT --}}
                <div class="form-group">
                    <label>Product</label>

                    <select name="product_id"
                            id="stockInProduct"
                            required
                            disabled
                            onchange="updateStockInRemainingQuantity(this.value)">

                        <option value="">
                            Select Purchase Order first
                        </option>

                    </select>

                    <small>
                        Only products included in the selected Purchase Order
                        can be received.
                    </small>
                </div>


                {{-- REMAINING QUANTITY --}}
                <div class="form-group">
                    <label>Remaining Quantity</label>

                    <div id="stockInRemainingQuantity"
                         style="padding:10px 12px;
                                background:#f8fafc;
                                border:1px solid #e2e8f0;
                                border-radius:6px;">
                        —
                    </div>

                    <small>
                        Maximum quantity that can be received for this
                        Purchase Order item.
                    </small>
                </div>


                {{-- QUANTITY RECEIVED --}}
                <div class="form-group">
                    <label>Quantity Received</label>

                    <input type="number"
                           name="quantity"
                           id="stockInQuantity"
                           min="1"
                           step="1"
                           placeholder="Enter quantity actually received"
                           required
                           disabled>
                </div>


                {{-- UNIT COST --}}
                <div class="form-group">
                    <label>Unit Cost</label>

                    <input type="number"
                           name="unit_cost"
                           id="stockInUnitCost"
                           min="0"
                           step="0.01"
                           placeholder="Uses Purchase Order cost if blank">
                </div>


                {{-- REASON --}}
                <div class="form-group">
                    <label>Reason <small>(Optional)</small></label>

                    <input type="text"
                           name="reason"
                           value="{{ old('reason') }}"
                           placeholder="e.g. Supplier delivery received">
                </div>


                {{-- REFERENCE --}}
                <div class="form-group">
                    <label>Reference</label>

                    <input type="text"
                           name="reference"
                           id="stockInReference"
                           placeholder="Purchase Order number"
                           required>
                </div>


                {{-- ACTIONS --}}
                <div class="form-actions">

                    <button type="button"
                            class="btn btn-cancel"
                            onclick="closeModal('stockInModal')">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Save Stock In
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


{{-- STOCK OUT MODAL --}}
<div class="modal" id="stockOutModal">
    <div class="modal-content">

        <div class="modal-header">
            <h2>Record Customer Delivery / Stock Out</h2>
            <button type="button" class="close-x" onclick="closeModal('stockOutModal')">&times;</button>
        </div>

        <div class="modal-body">

            <form action="{{ route('owner.inventory.stock-out') }}" method="POST">

                @csrf

                <input type="hidden" name="product_id" id="stockOutProductId">

                <div class="form-group">
                    <label>Transaction Date</label>
                    <input type="date" name="transaction_date" id="stockOutTransactionDate" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                    <small>Actual date the products were released or delivered.</small>
                </div>

                <div class="stock-info">
                    <div class="stock-info-row"><span class="stock-info-label">Product</span><span class="stock-info-value" id="stockOutProductName"></span></div>
                    <div class="stock-info-row"><span class="stock-info-label">Current Stock</span><span class="stock-info-value" id="stockOutCurrentStock"></span></div>
                </div>

                <div class="form-group">
                    <label for="stockOutCustomerOrderReference">Customer Order Reference</label>
                    <select name="customer_order_id" id="stockOutCustomerOrderReference" onchange="populateReceiptDropdown(this.value)" required>
                        <option value="">Select Customer Order</option>
                    </select>
                    <small>Confirmed orders containing this product with quantity still needed.</small>
                </div>

                <div class="form-group">
                    <label for="stockOutReceiptNumber">Receipt Number</label>
                    <select name="receipt_number" id="stockOutReceiptNumber" required disabled>
                        <option value="">Select Customer Order first</option>
                    </select>
                    <small>Receipt issued by the Cashier for the selected order.</small>
                </div>

                <div class="form-group">
                    <label>Received By</label>
                    <input type="text" name="received_by" placeholder="Name of person who received the goods" required>
                </div>

                <div class="form-group">
                    <label>Quantity to Stock Out</label>
                    <input type="number" name="quantity" id="stockOutQuantity" min="1" step="1" placeholder="Enter quantity actually delivered" required>
                    <small>Cannot exceed current stock or the order's quantity still needed.</small>
                </div>

                <div class="form-group">
                    <label>Reason <small>(Optional)</small></label>
                    <input type="text" name="reason" value="Customer delivery / release" placeholder="e.g. Customer delivery / release">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('stockOutModal')">Cancel</button>
                    <button type="submit" class="btn btn-warning">Confirm Delivery / Stock Out</button>
                </div>

            </form>

        </div>

    </div>
</div>


{{-- ADJUST STOCK MODAL --}}
<div class="modal" id="adjustModal">
    <div class="modal-content">

        <div class="modal-header">
            <h2>Adjust Stock</h2>
            <button type="button" class="close-x" onclick="closeModal('adjustModal')">&times;</button>
        </div>

        <div class="modal-body">

            <form action="{{ route('owner.inventory.adjust') }}" method="POST">

                @csrf

                <div class="form-group">
                    <label>Transaction Date</label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                    <small>Actual date the physical count occurred.</small>
                </div>

                <div class="form-group">
                    <label>Product</label>
                    <select name="product_id" id="adjustProduct" onchange="updateAdjustmentStock()" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="stock-info">
                    <div class="stock-info-row"><span class="stock-info-label">Current System Stock</span><span class="stock-info-value" id="adjustCurrentStock">0</span></div>
                </div>

                <div class="form-group">
                    <label>Actual Physical Count</label>
                    <input type="number" name="actual_stock" id="actualStock" min="0" step="1" placeholder="Enter actual physical count" required>
                </div>

                <div class="form-group">
                    <label>Reason</label>
                    <input type="text" name="reason" placeholder="e.g. Physical count difference" required>
                </div>

                <div class="form-group">
                    <label>Reference <small>(Optional)</small></label>
                    <input type="text" name="reference" placeholder="e.g. Count sheet">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('adjustModal')">Cancel</button>
                    <button type="submit" class="btn btn-secondary">Save Adjustment</button>
                </div>

            </form>

        </div>

    </div>
</div>


{{-- VIEW INVENTORY MODAL --}}
<div class="modal" id="viewModal">
    <div class="modal-content modal-large">

        <div class="modal-header">
            <h2>Inventory Details</h2>
            <button type="button" class="close-x" onclick="closeModal('viewModal')">&times;</button>
        </div>

        <div class="modal-body">

            <div class="details-grid two">
                <div class="detail-item"><span class="detail-label">Product Name</span><span class="detail-value" id="viewProductName"></span></div>
                <div class="detail-item"><span class="detail-label">Category</span><span class="detail-value" id="viewCategory"></span></div>
                <div class="detail-item"><span class="detail-label">API</span><span class="detail-value" id="viewApi"></span></div>
                <div class="detail-item"><span class="detail-label">Base Oil</span><span class="detail-value" id="viewBaseOil"></span></div>
                <div class="detail-item"><span class="detail-label">Package Size</span><span class="detail-value" id="viewPackageSize"></span></div>
                <div class="detail-item"><span class="detail-label">Unit</span><span class="detail-value" id="viewUnit"></span></div>
                <div class="detail-item"><span class="detail-label">Unit Price</span><span class="detail-value" id="viewUnitPrice"></span></div>
                <div class="detail-item"><span class="detail-label">Reorder Level</span><span class="detail-value" id="viewReorderLevel"></span></div>
                <div class="detail-item"><span class="detail-label">Current Stock</span><span class="detail-value" id="viewCurrentStock"></span></div>
                <div class="detail-item"><span class="detail-label">Stock Status</span><span class="detail-value" id="viewStatus"></span></div>
            </div>

            <h3>Product Movement History</h3>

            <div class="movement-table">
                <table>
                    <thead>
                        <tr>
                            <th>Transaction Date</th>
                            <th>Recorded At</th>
                            <th>Movement</th>
                            <th>Quantity</th>
                            <th>Before</th>
                            <th>After</th>
                            <th>Reason</th>
                            <th>Customer Order</th>
                            <th>Receipt No.</th>
                            <th>Received By</th>
                            <th>Reference</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody id="viewMovementBody"></tbody>
                </table>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('viewModal')">Close</button>
            </div>

        </div>

    </div>
</div>


{{-- JAVASCRIPT DATA --}}
@php

    $inventoryJavascriptData = $products->map(function ($product) {

        return [
            'id' => $product->id,
            'product_name' => $product->product_name,
            'api' => $product->api,
            'base_oil' => $product->base_oil,
            'package_size' => $product->package_size,
            'unit' => $product->unit,
            'unit_price' => $product->unit_price,
            'reorder_level' => $product->reorder_level,
            'category' => [
                'category_name' => $product->category->category_name ?? null,
            ],
            'inventory' => [
                'current_stock' => $product->inventory->current_stock ?? 0,
            ],
            'inventory_movements' => $product->inventoryMovements
                ->map(function ($movement) {
                    return [
                        'movement_type' => $movement->movement_type,
                        'transaction_date' => $movement->transaction_date ? $movement->transaction_date->format('Y-m-d') : null,
                        'quantity' => $movement->quantity,
                        'stock_before' => $movement->stock_before,
                        'stock_after' => $movement->stock_after,
                        'reason' => $movement->reason,
                        'customer_order_reference' => $movement->customer_order_reference,
                        'receipt_number' => $movement->receipt_number,
                        'received_by' => $movement->received_by,
                        'reference' => $movement->reference,
                        'created_at' => $movement->created_at,
                        'user' => [
                            'name' => $movement->user->name ?? null,
                        ],
                    ];
                })
                ->values(),
        ];

    })->values();

    $customerOrderJavascriptData = collect($customerOrders ?? [])->map(function ($order) {

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'status' => $order->status,
            'items' => $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => (int) $item->quantity,
                    'reserved_quantity' => (int) ($item->reserved_quantity ?? 0),
                    'fulfilled_quantity' => (int) ($item->fulfilled_quantity ?? 0),
                ];
            })->values(),
        ];

    })->values();

    $paymentJavascriptData = collect($payments ?? [])->map(function ($payment) {

        return [
            'id' => $payment->id,
            'receipt_number' => $payment->receipt_number,
            'customer_order_id' => $payment->customer_order_id,
            'payment_method' => $payment->payment_method,
            'status' => $payment->status,
            'amount' => $payment->amount,
        ];

    })->values();

    $purchaseOrderJavascriptData = collect($purchaseOrders ?? [])->map(function ($purchaseOrder) {

        return [
            'id' => $purchaseOrder->id,
            'po_number' => $purchaseOrder->po_number,
            'status' => $purchaseOrder->status,

            'supplier' => [
                'supplier_name' => $purchaseOrder->supplier->supplier_name ?? null,
            ],

            'items' => $purchaseOrder->items->map(function ($item) {

                $orderedQuantity = (int) $item->quantity;

                $receivedQuantity = (int) ($item->received_quantity ?? 0);

                $remainingQuantity = max(
                    0,
                    $orderedQuantity - $receivedQuantity
                );

                return [
                    'product_id' => $item->product_id,

                    'product_name' =>
                        $item->product->product_name ?? 'Unknown Product',

                    'quantity' =>
                        $orderedQuantity,

                    'received_quantity' =>
                        $receivedQuantity,

                    'remaining_quantity' =>
                        $remainingQuantity,

                    'unit_cost' =>
                        $item->unit_cost,
                ];

            })->values(),

        ];

    })->values();
@endphp


<script>

    const inventoryProducts = @js($inventoryJavascriptData);

    const customerOrders = @js($customerOrderJavascriptData);

    const payments = @js($paymentJavascriptData);

    const purchaseOrders = @js($purchaseOrderJavascriptData);


    /* TABS */
    function showInventoryTab(sectionId, button) {
        document.querySelectorAll('.inventory-section').forEach(function (section) { section.classList.remove('active'); });
        document.querySelectorAll('.inventory-tab').forEach(function (tab) { tab.classList.remove('active'); });

        const section = document.getElementById(sectionId);
        if (section) section.classList.add('active');
        if (button) button.classList.add('active');
    }


    /* VIEW INVENTORY */
    function openViewModal(productId) {

        const product = inventoryProducts.find(function (product) { return Number(product.id) === Number(productId); });

        if (!product) return;

        const currentStock = Number(product.inventory?.current_stock ?? 0);
        const reorderLevel = Number(product.reorder_level ?? 0);

        let stockStatus;

        if (currentStock <= 0) {
            stockStatus = 'Out of Stock';
        } else if (currentStock <= reorderLevel) {
            stockStatus = 'Needs Restocking';
        } else {
            stockStatus = 'In Stock';
        }

        document.getElementById('viewProductName').textContent = product.product_name ?? '—';
        document.getElementById('viewCategory').textContent = product.category?.category_name ?? '—';
        document.getElementById('viewApi').textContent = product.api ?? '—';
        document.getElementById('viewBaseOil').textContent = product.base_oil ?? '—';
        document.getElementById('viewPackageSize').textContent = product.package_size ?? '—';
        document.getElementById('viewUnit').textContent = product.unit ?? '—';
        document.getElementById('viewUnitPrice').textContent = '₱' + Number(product.unit_price ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('viewReorderLevel').textContent = reorderLevel.toLocaleString();
        document.getElementById('viewCurrentStock').textContent = currentStock.toLocaleString();
        document.getElementById('viewStatus').textContent = stockStatus;

        renderMovementHistory(product);

        openModal('viewModal');
    }


    /* MOVEMENT HISTORY INSIDE VIEW */
    function renderMovementHistory(product) {

        const body = document.getElementById('viewMovementBody');

        body.innerHTML = '';

        const movements = product.inventory_movements ?? [];

        if (movements.length === 0) {
            body.innerHTML = '<tr><td colspan="12" style="text-align:center;padding:25px;">No movement history found.</td></tr>';
            return;
        }

        movements.forEach(function (movement) {

            let movementClass = 'movement-adjustment';
            let movementLabel = 'Adjustment';

            if (movement.movement_type === 'stock_in') {
                movementClass = 'movement-in';
                movementLabel = 'Stock In';
            } else if (movement.movement_type === 'stock_out') {
                movementClass = 'movement-out';
                movementLabel = 'Stock Out';
            }

            const quantity = Number(movement.quantity ?? 0);

            const transactionDate = movement.transaction_date
                ? new Date(movement.transaction_date + 'T00:00:00').toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' })
                : '—';

            const recordedAt = movement.created_at
                ? new Date(movement.created_at).toLocaleString('en-PH', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
                : '—';

            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${escapeHtml(transactionDate)}</td>
                <td>${escapeHtml(recordedAt)}</td>
                <td><span class="movement-badge ${movementClass}">${escapeHtml(movementLabel)}</span></td>
                <td>${quantity > 0 ? '+' : ''}${quantity.toLocaleString()}</td>
                <td>${Number(movement.stock_before ?? 0).toLocaleString()}</td>
                <td>${Number(movement.stock_after ?? 0).toLocaleString()}</td>
                <td>${escapeHtml(movement.reason ?? '—')}</td>
                <td>${escapeHtml(movement.customer_order_reference ?? '—')}</td>
                <td>${escapeHtml(movement.receipt_number ?? '—')}</td>
                <td>${escapeHtml(movement.received_by ?? '—')}</td>
                <td>${escapeHtml(movement.reference ?? '—')}</td>
                <td>${escapeHtml(movement.user?.name ?? '—')}</td>
            `;

            body.appendChild(row);

        });

    }

    /* STOCK IN - PURCHASE ORDER PRODUCTS */
    function populateStockInProducts(purchaseOrderId) {

        const productSelect =
            document.getElementById('stockInProduct');

        const quantityInput =
            document.getElementById('stockInQuantity');

        const remainingDisplay =
            document.getElementById('stockInRemainingQuantity');

        const unitCostInput =
            document.getElementById('stockInUnitCost');

        const referenceInput =
            document.getElementById('stockInReference');


        if (!productSelect) return;


        /*
        * Reset product dropdown.
        */
        productSelect.innerHTML =
            '<option value="">Select Product</option>';

        productSelect.disabled = true;


        /*
        * Reset quantity.
        */
        if (quantityInput) {
            quantityInput.value = '';
            quantityInput.max = '';
            quantityInput.disabled = true;
        }


        /*
        * Reset remaining quantity.
        */
        if (remainingDisplay) {
            remainingDisplay.textContent = '—';
        }


        /*
        * Reset unit cost.
        */
        if (unitCostInput) {
            unitCostInput.value = '';
        }


        /*
        * Find selected Purchase Order.
        */
        const purchaseOrder = purchaseOrders.find(function (order) {

            return Number(order.id) === Number(purchaseOrderId);

        });


        if (!purchaseOrder) return;


        /*
        * Automatically use the PO number
        * as the reference.
        */
        if (referenceInput) {
            referenceInput.value =
                purchaseOrder.po_number ?? '';
        }


        /*
        * Add only products that still have
        * quantity remaining to receive.
        */
        let hasProducts = false;


        (purchaseOrder.items ?? []).forEach(function (item) {

            const remainingQuantity =
                Number(item.remaining_quantity ?? 0);


            /*
            * Fully received items cannot be
            * selected again.
            */
            if (remainingQuantity <= 0) {
                return;
            }


            hasProducts = true;


            const option =
                document.createElement('option');


            option.value =
                item.product_id;


            option.textContent =
                `${item.product_name} — Remaining: ${remainingQuantity}`;


            option.dataset.remainingQuantity =
                remainingQuantity;


            option.dataset.unitCost =
                item.unit_cost ?? '';


            productSelect.appendChild(option);

        });


        if (!hasProducts) {

            const option =
                document.createElement('option');

            option.value = '';

            option.textContent =
                'All products in this PO are fully received';

            option.disabled = true;

            productSelect.appendChild(option);

            return;
        }


        productSelect.disabled = false;
    }


    /* STOCK IN - REMAINING QUANTITY */
    function updateStockInRemainingQuantity(productId) {

        const purchaseOrderSelect =
            document.getElementById('stockInPurchaseOrder');

        const productSelect =
            document.getElementById('stockInProduct');

        const quantityInput =
            document.getElementById('stockInQuantity');

        const remainingDisplay =
            document.getElementById('stockInRemainingQuantity');

        const unitCostInput =
            document.getElementById('stockInUnitCost');


        if (
            !purchaseOrderSelect ||
            !productSelect
        ) {
            return;
        }


        const purchaseOrderId =
            purchaseOrderSelect.value;


        const purchaseOrder =
            purchaseOrders.find(function (order) {

                return Number(order.id) ===
                    Number(purchaseOrderId);

            });


        if (!purchaseOrder) return;


        const item =
            (purchaseOrder.items ?? []).find(function (item) {

                return Number(item.product_id) ===
                    Number(productId);

            });


        if (!item) {

            if (remainingDisplay) {
                remainingDisplay.textContent = '—';
            }

            if (quantityInput) {
                quantityInput.value = '';
                quantityInput.max = '';
                quantityInput.disabled = true;
            }

            return;
        }


        const remainingQuantity =
            Number(item.remaining_quantity ?? 0);


        /*
        * Display remaining quantity.
        */
        if (remainingDisplay) {

            remainingDisplay.textContent =
                remainingQuantity.toLocaleString();

        }


        /*
        * Limit Stock In quantity.
        */
        if (quantityInput) {

            quantityInput.value = '';

            quantityInput.min = 1;

            quantityInput.max =
                remainingQuantity;

            quantityInput.disabled =
                remainingQuantity <= 0;

        }


        /*
        * Automatically show the PO unit cost.
        *
        * The user can still override it because
        * the field is optional.
        */
        if (unitCostInput) {

            unitCostInput.value =
                item.unit_cost ?? '';

        }
    }


    /* STOCK IN - RESET MODAL */
    function resetStockInModal() {

        const purchaseOrderSelect =
            document.getElementById('stockInPurchaseOrder');

        const productSelect =
            document.getElementById('stockInProduct');

        const quantityInput =
            document.getElementById('stockInQuantity');

        const remainingDisplay =
            document.getElementById('stockInRemainingQuantity');

        const unitCostInput =
            document.getElementById('stockInUnitCost');

        const referenceInput =
            document.getElementById('stockInReference');


        if (purchaseOrderSelect) {
            purchaseOrderSelect.value = '';
        }


        if (productSelect) {

            productSelect.innerHTML =
                '<option value="">Select Purchase Order first</option>';

            productSelect.disabled = true;

        }


        if (quantityInput) {

            quantityInput.value = '';

            quantityInput.max = '';

            quantityInput.disabled = true;

        }


        if (remainingDisplay) {
            remainingDisplay.textContent = '—';
        }


        if (unitCostInput) {
            unitCostInput.value = '';
        }


        if (referenceInput) {
            referenceInput.value = '';
        }

    }

    /* CUSTOMER ORDER DROPDOWN */
    function populateCustomerOrderDropdown(productId) {

        const select = document.getElementById('stockOutCustomerOrderReference');

        if (!select) return;

        select.innerHTML = '<option value="">Select Customer Order</option>';

        const eligibleStatuses = ['confirmed', 'partially_fulfilled'];

        let hasEligibleOrder = false;

        customerOrders.forEach(function (order) {

            if (!eligibleStatuses.includes(order.status)) return;

            const item = (order.items ?? []).find(function (orderItem) {
                return Number(orderItem.product_id) === Number(productId);
            });

            if (!item) return;

            const orderQuantity = Number(item.quantity ?? 0);
            const fulfilledQuantity = Number(item.fulfilled_quantity ?? 0);

            /* Quantity not yet physically fulfilled */
            const quantityStillNeeded = orderQuantity - fulfilledQuantity;

            if (quantityStillNeeded <= 0) return;

            hasEligibleOrder = true;

            const option = document.createElement('option');

            /* Submit the actual Customer Order ID */
            option.value = order.id;

            option.textContent = `${order.order_number} — ${order.customer_name} — Order: ${orderQuantity}`;

            option.dataset.productId = item.product_id;
            option.dataset.orderQuantity = orderQuantity;
            option.dataset.fulfilledQuantity = fulfilledQuantity;
            option.dataset.quantityStillNeeded = quantityStillNeeded;

            select.appendChild(option);

        });

        if (!hasEligibleOrder) {

            const option = document.createElement('option');

            option.value = '';
            option.textContent = 'No eligible customer order for this product';
            option.disabled = true;

            select.appendChild(option);

        }

    }


    /* RECEIPT DROPDOWN */
    function populateReceiptDropdown(customerOrderId) {

        const select = document.getElementById('stockOutReceiptNumber');

        if (!select) return;

        select.innerHTML = '';
        select.disabled = true;

        const defaultOption = document.createElement('option');

        defaultOption.value = '';
        defaultOption.textContent = 'Select Receipt';

        select.appendChild(defaultOption);

        if (!customerOrderId) {
            defaultOption.textContent = 'Select Customer Order first';
            return;
        }

        const matchingPayments = payments.filter(function (payment) {
            return Number(payment.customer_order_id) === Number(customerOrderId);
        });

        if (matchingPayments.length === 0) {
            defaultOption.textContent = 'No receipt found for this Customer Order';
            return;
        }

        matchingPayments.forEach(function (payment) {

            const option = document.createElement('option');

            option.value = payment.receipt_number;

            const method = payment.payment_method ? payment.payment_method.toUpperCase() : '—';
            const status = payment.status ? payment.status.toUpperCase() : '—';
            const amount = Number(payment.amount ?? 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            option.textContent = `${payment.receipt_number} — ${method} — ${status} — ₱${amount}`;

            select.appendChild(option);

        });

        select.disabled = false;

    }


    /* STOCK OUT */
    function openStockOutModal(productId) {

        const product = inventoryProducts.find(function (product) { return Number(product.id) === Number(productId); });

        if (!product) return;

        const currentStock = Number(product.inventory?.current_stock ?? 0);

        document.getElementById('stockOutProductId').value = product.id;
        document.getElementById('stockOutProductName').textContent = product.product_name;
        document.getElementById('stockOutCurrentStock').textContent = currentStock.toLocaleString();

        const quantityInput = document.getElementById('stockOutQuantity');

        quantityInput.value = '';
        quantityInput.max = currentStock;

        populateCustomerOrderDropdown(product.id);

        const receiptSelect = document.getElementById('stockOutReceiptNumber');

        if (receiptSelect) {
            receiptSelect.innerHTML = '<option value="">Select Customer Order first</option>';
            receiptSelect.disabled = true;
        }

        const transactionDateInput = document.getElementById('stockOutTransactionDate');

        if (transactionDateInput) {
            transactionDateInput.value = new Date().toISOString().split('T')[0];
        }

        openModal('stockOutModal');
    }


    /* ADJUST STOCK */
    function updateAdjustmentStock() {

        const productId = document.getElementById('adjustProduct').value;
        const stockDisplay = document.getElementById('adjustCurrentStock');

        if (!productId) {
            stockDisplay.textContent = '0';
            return;
        }

        const product = inventoryProducts.find(function (product) { return Number(product.id) === Number(productId); });

        if (!product) {
            stockDisplay.textContent = '0';
            return;
        }

        stockDisplay.textContent = Number(product.inventory?.current_stock ?? 0).toLocaleString();
    }


    /* CURRENT INVENTORY FILTER */
    function filterInventory() {

        const search = document.getElementById('searchInput').value.toLowerCase().trim();
        const status = document.getElementById('statusFilter').value;

        document.querySelectorAll('#inventoryTable tbody tr').forEach(function (row) {

            const productName = row.dataset.productName ?? '';
            const stockStatus = row.dataset.stockStatus ?? '';

            const matchesSearch = productName.includes(search);
            const matchesStatus = status === '' || stockStatus === status;

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';

        });

    }


    /* MOVEMENT FILTER */
    function filterMovements() {

        const search = document.getElementById('movementSearchInput').value.toLowerCase().trim();
        const movementType = document.getElementById('movementTypeFilter').value;

        document.querySelectorAll('#movementHistoryTable tbody tr').forEach(function (row) {

            const productName = row.dataset.movementProduct ?? '';
            const type = row.dataset.movementType ?? '';

            const matchesSearch = productName.includes(search);
            const matchesType = movementType === '' || type === movementType;

            row.style.display = matchesSearch && matchesType ? '' : 'none';

        });

    }

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