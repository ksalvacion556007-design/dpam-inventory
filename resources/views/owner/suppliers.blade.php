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

            <a href="{{ route('owner.suppliers') }}" class="active">
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
                <h1 class="page-title">Suppliers</h1>
                <div class="page-description">Manage supplier information used for purchasing.</div>
            </div>
            <div class="topbar-actions">
                <button type="button" class="btn btn-primary" onclick="openModal('addSupplierModal')">+ Add Supplier</button>
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
            <input type="text" id="supplierSearch" placeholder="Search supplier name, contact person, or contact number...">
            <select id="statusFilter">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="archived">Archived</option>
            </select>
        </div>

        <section class="table-panel">

            <div class="table-header">
                <h2>Supplier List</h2>
                <div class="table-count">{{ $suppliers->count() }} supplier(s)</div>
            </div>

            <div class="table-wrapper">
                <table>

                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Contact Person</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody id="supplierTableBody">

                        @forelse($suppliers as $supplier)

                            <tr data-name="{{ strtolower($supplier->supplier_name) }}" data-contact-person="{{ strtolower($supplier->contact_person ?? '') }}" data-contact-number="{{ strtolower($supplier->contact_number ?? '') }}" data-status="{{ $supplier->status }}">

                                <td>
                                    <strong>{{ $supplier->supplier_name }}</strong>
                                    @if($supplier->address)
                                        <div class="secondary-text">{{ $supplier->address }}</div>
                                    @endif
                                </td>

                                <td>{{ $supplier->contact_person ?: '—' }}</td>
                                <td>{{ $supplier->contact_number ?: '—' }}</td>
                                <td>{{ $supplier->email ?: '—' }}</td>

                                <td>
                                    <span class="status-badge status-{{ $supplier->status }}">{{ ucfirst($supplier->status) }}</span>
                                </td>

                                <td>
                                    <div class="actions">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="viewSupplier({{ $supplier->id }})">View</button>

                                        @if($supplier->status !== 'archived')
                                            <button type="button" class="btn btn-sm btn-warning" onclick="editSupplier({{ $supplier->id }})">Edit</button>
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="archiveSupplier({{ $supplier->id }})">Archive</button>
                                        @endif
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">No suppliers found. Add your first supplier to begin.</div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

        </section>

    </main>


<!-- ADD SUPPLIER MODAL -->
<div id="addSupplierModal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h2>Add Supplier</h2>
            <button type="button" class="close-x" onclick="closeModal('addSupplierModal')">&times;</button>
        </div>

        <form action="{{ route('owner.suppliers.store') }}" method="POST">

            @csrf

            <div class="modal-body">

                <div class="form-group">
                    <label>Supplier Name</label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" required>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}">
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" value="{{ old('contact_number') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address">{{ old('address') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('addSupplierModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Supplier</button>
                </div>

            </div>

        </form>

    </div>
</div>


<!-- VIEW SUPPLIER MODAL -->
<div id="viewSupplierModal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h2>Supplier Details</h2>
            <button type="button" class="close-x" onclick="closeModal('viewSupplierModal')">&times;</button>
        </div>

        <div class="modal-body">

            <div class="detail-row"><div class="detail-label">Supplier Name</div><div class="detail-value" id="viewSupplierName">—</div></div>
            <div class="detail-row"><div class="detail-label">Contact Person</div><div class="detail-value" id="viewContactPerson">—</div></div>
            <div class="detail-row"><div class="detail-label">Contact Number</div><div class="detail-value" id="viewContactNumber">—</div></div>
            <div class="detail-row"><div class="detail-label">Email</div><div class="detail-value" id="viewEmail">—</div></div>
            <div class="detail-row"><div class="detail-label">Address</div><div class="detail-value" id="viewAddress">—</div></div>
            <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value" id="viewStatus">—</div></div>

            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('viewSupplierModal')">Close</button>
            </div>

        </div>

    </div>
</div>


<!-- EDIT SUPPLIER MODAL -->
<div id="editSupplierModal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h2>Edit Supplier</h2>
            <button type="button" class="close-x" onclick="closeModal('editSupplierModal')">&times;</button>
        </div>

        <form id="editSupplierForm" method="POST">

            @csrf
            @method('PUT')

            <div class="modal-body">

                <div class="form-group">
                    <label>Supplier Name</label>
                    <input type="text" name="supplier_name" id="editSupplierName" required>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" name="contact_person" id="editContactPerson">
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" id="editContactNumber">
                    </div>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="editEmail">
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" id="editAddress"></textarea>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="editStatus" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('editSupplierModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>

            </div>

        </form>

    </div>
</div>


<!-- ARCHIVE SUPPLIER MODAL -->
<div id="archiveSupplierModal" class="modal">
    <div class="modal-content">

        <div class="modal-header">
            <h2>Archive Supplier</h2>
            <button type="button" class="close-x" onclick="closeModal('archiveSupplierModal')">&times;</button>
        </div>

        <form id="archiveSupplierForm" method="POST">

            @csrf
            @method('PATCH')

            <div class="modal-body">

                <p>Are you sure you want to archive <strong id="archiveSupplierName"></strong>?</p>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('archiveSupplierModal')">Cancel</button>
                    <button type="submit" class="btn btn-danger">Archive Supplier</button>
                </div>

            </div>

        </form>

    </div>
</div>


<script>

    const suppliers = @json($suppliers);

    function getSupplier(id) {
        return suppliers.find(function (supplier) { return Number(supplier.id) === Number(id); });
    }

    function viewSupplier(id) {
        const supplier = getSupplier(id);
        if (!supplier) return;

        document.getElementById('viewSupplierName').textContent = supplier.supplier_name || '—';
        document.getElementById('viewContactPerson').textContent = supplier.contact_person || '—';
        document.getElementById('viewContactNumber').textContent = supplier.contact_number || '—';
        document.getElementById('viewEmail').textContent = supplier.email || '—';
        document.getElementById('viewAddress').textContent = supplier.address || '—';

        document.getElementById('viewStatus').innerHTML =
            '<span class="status-badge status-' + supplier.status + '">' +
            supplier.status.charAt(0).toUpperCase() + supplier.status.slice(1) +
            '</span>';

        openModal('viewSupplierModal');
    }

    function editSupplier(id) {
        const supplier = getSupplier(id);
        if (!supplier) return;

        document.getElementById('editSupplierForm').action = '/owner/suppliers/' + supplier.id;
        document.getElementById('editSupplierName').value = supplier.supplier_name || '';
        document.getElementById('editContactPerson').value = supplier.contact_person || '';
        document.getElementById('editContactNumber').value = supplier.contact_number || '';
        document.getElementById('editEmail').value = supplier.email || '';
        document.getElementById('editAddress').value = supplier.address || '';
        document.getElementById('editStatus').value = supplier.status === 'archived' ? 'inactive' : supplier.status;

        openModal('editSupplierModal');
    }

    function archiveSupplier(id) {
        const supplier = getSupplier(id);
        if (!supplier) return;

        document.getElementById('archiveSupplierForm').action = '/owner/suppliers/' + supplier.id + '/archive';
        document.getElementById('archiveSupplierName').textContent = supplier.supplier_name;

        openModal('archiveSupplierModal');
    }

    const searchInput = document.getElementById('supplierSearch');
    const statusFilter = document.getElementById('statusFilter');

    function filterSuppliers() {
        const search = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;

        document.querySelectorAll('#supplierTableBody tr[data-name]').forEach(function (row) {
            const matchesSearch =
                (row.dataset.name || '').includes(search) ||
                (row.dataset.contactPerson || '').includes(search) ||
                (row.dataset.contactNumber || '').includes(search);

            const matchesStatus = selectedStatus === 'all' || (row.dataset.status || '') === selectedStatus;

            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterSuppliers);
    statusFilter.addEventListener('change', filterSuppliers);

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