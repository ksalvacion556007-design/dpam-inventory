<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reports - DPAM IMS</title>

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

            <a href="{{ route('owner.purchase-orders') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.5 10h10L20 7H7"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></svg>
                Purchase Orders
            </a>

            <a href="{{ route('owner.stock-card') }}" class="">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>
                Stock Card
            </a>

            <a href="{{ route('owner.reports') }}" class="active">
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
                <h1 class="page-title">Reports</h1>
                <div class="page-description">Inventory and transaction reports for DPAM Industrial Supplies and Services Inc.</div>
            </div>
            <div class="topbar-actions">
                <button type="button" class="btn btn-secondary" onclick="window.print()">Print PDF</button>
                <div class="role-badge">OWNER</div>
            </div>
        </div>


        {{-- DATE FILTER --}}
        <div class="filter-box">

            <form method="GET" action="{{ route('owner.reports') }}" style="width:100%;">

                <div class="filter-grid report-filter">

                    <div class="form-group" style="margin:0;">
                        <label for="date_from">Date From</label>
                        <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}">
                    </div>

                    <div class="form-group" style="margin:0;">
                        <label for="date_to">Date To</label>
                        <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Generate Report</button>

                    <a href="{{ route('owner.reports') }}" class="btn btn-cancel">Clear</a>

                </div>

            </form>

        </div>


        {{-- PRINT TITLE --}}
        <div class="print-only" style="margin-bottom: 20px;">
            <h1>DPAM Industrial Supplies and Services Inc.</h1>
            <h2>Inventory Reports</h2>

            @if ($dateFrom || $dateTo)
                <p>
                    Reporting Period:
                    {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y') : 'Beginning' }}
                    -
                    {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('M d, Y') : 'Present' }}
                </p>
            @else
                <p>Reporting Period: All Records</p>
            @endif
        </div>


        {{-- KPI CARDS (click to open the related table) --}}
        <div class="cards">

            <a href="{{ route('owner.products') }}" class="card js-modal" data-title="Active Products" style="--card-color:#2563eb">
                <div class="card-label">Active Products</div>
                <div class="card-value">{{ number_format($totalProducts) }}</div>
                <div class="card-link">View details</div>
            </a>

            <div class="card js-modal" data-title="Inventory Summary" data-target="#reportInventory" style="--card-color:#059669">
                <div class="card-label">Inventory Quantity</div>
                <div class="card-value">{{ number_format($totalInventoryQuantity) }}</div>
                <div class="card-link">View details</div>
            </div>

            <div class="card js-modal" data-title="Inventory Amount" data-target="#reportInventory" style="--card-color:#0ea5e9">
                <div class="card-label">Inventory Amount</div>
                <div class="card-value">₱{{ number_format($totalInventoryValue, 2) }}</div>
                <div class="card-link">View details</div>
            </div>

            <div class="card js-modal" data-title="Low / Out of Stock" data-target="#reportLowOut" style="--card-color:#f59e0b">
                <div class="card-label">Low / Out of Stock</div>
                <div class="card-value">{{ number_format($lowStockCount + $outOfStockCount) }}</div>
                <div class="card-link">{{ $lowStockCount }} low · {{ $outOfStockCount }} out</div>
            </div>

            <div class="card js-modal" data-title="Stock In Report" data-target="#reportStockIn" style="--card-color:#16a34a">
                <div class="card-label">Total Stock In</div>
                <div class="card-value">{{ number_format($totalStockInQuantity) }}</div>
                <div class="card-link">₱{{ number_format($totalStockInAmount, 2) }}</div>
            </div>

            <div class="card js-modal" data-title="Stock Out Report" data-target="#reportStockOut" style="--card-color:#dc2626">
                <div class="card-label">Total Stock Out</div>
                <div class="card-value">{{ number_format($totalStockOutQuantity) }}</div>
                <div class="card-link">₱{{ number_format($totalStockOutAmount, 2) }}</div>
            </div>

            <div class="card js-modal" data-title="Purchase Order Report" data-target="#reportPurchaseOrders" style="--card-color:#7c3aed">
                <div class="card-label">Purchase Orders</div>
                <div class="card-value">{{ number_format($purchaseOrders->count()) }}</div>
                <div class="card-link">View details</div>
            </div>

            <div class="card js-modal" data-title="Customer Order Report" data-target="#reportCustomerOrders" style="--card-color:#0f766e">
                <div class="card-label">Customer Orders</div>
                <div class="card-value">{{ number_format($customerOrders->count()) }}</div>
                <div class="card-link">View details</div>
            </div>

        </div>


        {{-- INVENTORY SUMMARY --}}
        <div class="table-panel" id="reportInventory">

            <div class="section-header">
                <div>
                    <h2>Inventory Summary</h2>
                    <p>Current inventory status of active products.</p>
                </div>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th class="text-right">Current Stock</th>
                            <th class="text-right">Reorder Level</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Inventory Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($inventory as $item)

                            @php
                                $stock = $item->current_stock;
                                $reorder = $item->reorder_level;

                                if ($stock <= 0) {
                                    $status = 'Out of Stock';
                                    $statusClass = 'badge-red';
                                } elseif ($stock <= $reorder) {
                                    $status = 'Low Stock';
                                    $statusClass = 'badge-yellow';
                                } else {
                                    $status = 'In Stock';
                                    $statusClass = 'badge-green';
                                }

                                $inventoryAmount = $stock * $item->unit_price;
                            @endphp

                            <tr>
                                <td><strong>{{ $item->product_name }}</strong></td>
                                <td>{{ $item->product?->category?->name ?? $item->product?->category?->category_name ?? '—' }}</td>
                                <td>{{ $item->unit ?? '—' }}</td>
                                <td class="text-right">{{ number_format($stock) }}</td>
                                <td class="text-right">{{ number_format($reorder) }}</td>
                                <td class="text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right">₱{{ number_format($inventoryAmount, 2) }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $status }}</span></td>
                            </tr>

                        @empty

                            <tr><td colspan="8" class="empty">No inventory records found.</td></tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- STOCK IN REPORT --}}
        <div class="table-panel" id="reportStockIn">

            <div class="section-header">
                <div>
                    <h2>Stock In Report</h2>
                    <p>Actual supplier deliveries recorded through Stock In.</p>
                </div>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Supplier</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Unit Cost</th>
                            <th class="text-right">Amount</th>
                            <th>Reference</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($stockIns as $movement)

                            <tr>
                                <td>{{ $movement->transaction_date?->format('M d, Y') ?? '—' }}</td>
                                <td><strong>{{ $movement->product?->product_name ?? '—' }}</strong></td>
                                <td>{{ $movement->supplier_customer ?? '—' }}</td>
                                <td class="text-right">{{ number_format(abs($movement->quantity)) }}</td>
                                <td class="text-right">₱{{ number_format($movement->unit_cost ?? 0, 2) }}</td>
                                <td class="text-right">₱{{ number_format($movement->amount ?? 0, 2) }}</td>
                                <td>{{ $movement->reference ?? '—' }}</td>
                                <td>{{ $movement->user?->name ?? '—' }}</td>
                            </tr>

                        @empty

                            <tr><td colspan="8" class="empty">No Stock In transactions found for the selected period.</td></tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- STOCK OUT REPORT --}}
        <div class="table-panel" id="reportStockOut">

            <div class="section-header">
                <div>
                    <h2>Stock Out Report</h2>
                    <p>Actual customer releases/deliveries recorded through Stock Out.</p>
                </div>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Receipt Number</th>
                            <th>Customer Order</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Amount</th>
                            <th>Received By</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($stockOuts as $movement)

                            <tr>
                                <td>{{ $movement->transaction_date?->format('M d, Y') ?? '—' }}</td>
                                <td><strong>{{ $movement->product?->product_name ?? '—' }}</strong></td>
                                <td>{{ $movement->supplier_customer ?? '—' }}</td>
                                <td>{{ $movement->receipt_number ?? '—' }}</td>
                                <td>{{ $movement->customer_order_reference ?? '—' }}</td>
                                <td class="text-right">{{ number_format(abs($movement->quantity)) }}</td>
                                <td class="text-right">₱{{ number_format($movement->unit_price ?? 0, 2) }}</td>
                                <td class="text-right">₱{{ number_format($movement->amount ?? 0, 2) }}</td>
                                <td>{{ $movement->received_by ?? '—' }}</td>
                                <td>{{ $movement->user?->name ?? '—' }}</td>
                            </tr>

                        @empty

                            <tr><td colspan="10" class="empty">No Stock Out transactions found for the selected period.</td></tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- LOW / OUT OF STOCK --}}
        <div class="table-panel" id="reportLowOut">

            <div class="section-header">
                <div>
                    <h2>Low / Out of Stock Report</h2>
                    <p>Products that require monitoring or replenishment.</p>
                </div>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Unit</th>
                            <th class="text-right">Current Stock</th>
                            <th class="text-right">Reorder Level</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($stockStatusReport as $item)

                            @php
                                if ($item->current_stock <= 0) {
                                    $status = 'Out of Stock';
                                    $statusClass = 'badge-red';
                                } else {
                                    $status = 'Low Stock';
                                    $statusClass = 'badge-yellow';
                                }
                            @endphp

                            <tr>
                                <td><strong>{{ $item->product_name }}</strong></td>
                                <td>{{ $item->product?->category?->name ?? $item->product?->category?->category_name ?? '—' }}</td>
                                <td>{{ $item->unit ?? '—' }}</td>
                                <td class="text-right">{{ number_format($item->current_stock) }}</td>
                                <td class="text-right">{{ number_format($item->reorder_level) }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $status }}</span></td>
                            </tr>

                        @empty

                            <tr><td colspan="6" class="empty">No low or out-of-stock products found.</td></tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- PURCHASE ORDERS --}}
        <div class="table-panel" id="reportPurchaseOrders">

            <div class="section-header">
                <div>
                    <h2>Purchase Order Report</h2>
                    <p>Purchase orders prepared for suppliers.</p>
                </div>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($purchaseOrders as $purchaseOrder)

                            @php
                                $poStatus = strtolower($purchaseOrder->status ?? 'unknown');

                                $poClass = match ($poStatus) {
                                    'approved', 'confirmed', 'received', 'completed' => 'badge-green',
                                    'pending', 'draft' => 'badge-yellow',
                                    'cancelled', 'canceled' => 'badge-red',
                                    default => 'badge-gray',
                                };
                            @endphp

                            <tr>
                                <td>{{ $purchaseOrder->created_at?->format('M d, Y') ?? '—' }}</td>
                                <td>{{ $purchaseOrder->po_number ?? $purchaseOrder->purchase_order_number ?? $purchaseOrder->id }}</td>
                                <td>{{ $purchaseOrder->supplier?->name ?? $purchaseOrder->supplier?->supplier_name ?? '—' }}</td>
                                <td><span class="badge {{ $poClass }}">{{ ucfirst(str_replace('_', ' ', $purchaseOrder->status ?? 'Unknown')) }}</span></td>
                                <td class="text-right">₱{{ number_format($purchaseOrder->total_amount ?? $purchaseOrder->total ?? 0, 2) }}</td>
                            </tr>

                        @empty

                            <tr><td colspan="5" class="empty">No purchase orders found for the selected period.</td></tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- CUSTOMER ORDERS --}}
        <div class="table-panel" id="reportCustomerOrders">

            <div class="section-header">
                <div>
                    <h2>Customer Order Report</h2>
                    <p>Customer requests and their inventory/fulfillment status.</p>
                </div>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order Reference</th>
                            <th>Customer</th>
                            <th>Inventory Check</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($customerOrders as $order)

                            @php
                                $checkStatus = strtolower($order->inventory_check_status ?? $order->inventory_check ?? 'pending');

                                $checkClass = match ($checkStatus) {
                                    'available' => 'badge-green',
                                    'insufficient' => 'badge-red',
                                    default => 'badge-yellow',
                                };

                                $orderStatus = strtolower($order->status ?? 'unknown');

                                $orderClass = match ($orderStatus) {
                                    'confirmed', 'fulfilled' => 'badge-green',
                                    'pending_inventory_check', 'pending' => 'badge-yellow',
                                    'for_purchasing', 'partially_fulfilled' => 'badge-blue',
                                    'cancelled', 'canceled' => 'badge-red',
                                    default => 'badge-gray',
                                };
                            @endphp

                            <tr>
                                <td>{{ $order->created_at?->format('M d, Y') ?? '—' }}</td>
                                <td>{{ $order->order_number ?? $order->customer_order_reference ?? $order->reference ?? $order->id }}</td>
                                <td>{{ $order->customer_name ?? $order->customer ?? '—' }}</td>
                                <td><span class="badge {{ $checkClass }}">{{ ucfirst(str_replace('_', ' ', $checkStatus)) }}</span></td>
                                <td><span class="badge {{ $orderClass }}">{{ ucfirst(str_replace('_', ' ', $order->status ?? 'Unknown')) }}</span></td>
                            </tr>

                        @empty

                            <tr><td colspan="5" class="empty">No customer orders found for the selected period.</td></tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- TRANSACTION TOTALS --}}
        <div class="table-panel" id="reportTotals">

            <div class="section-header">
                <div>
                    <h2>Transaction Totals</h2>
                    <p>Summary of actual inventory movements for the selected period.</p>
                </div>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Transaction</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td><strong>Stock In</strong></td>
                            <td class="text-right">{{ number_format($totalStockInQuantity) }}</td>
                            <td class="text-right">₱{{ number_format($totalStockInAmount, 2) }}</td>
                        </tr>

                        <tr>
                            <td><strong>Stock Out</strong></td>
                            <td class="text-right">{{ number_format($totalStockOutQuantity) }}</td>
                            <td class="text-right">₱{{ number_format($totalStockOutAmount, 2) }}</td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

        <div class="empty" style="padding:10px 0 25px; font-size:12px;">
            DPAM Industrial Supplies and Services Inc. — Inventory Management System
        </div>

    </main>


{{-- DATA POPUP MODAL --}}
<div id="dataModal" class="modal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2 id="dataModalTitle">Details</h2>
            <div class="data-modal-actions">
                <a href="#" id="dataModalFull" class="btn btn-light btn-sm">Open full page</a>
                <button type="button" class="close-x" onclick="closeDataModal()" aria-label="Close">&times;</button>
            </div>
        </div>
        <div id="dataModalBody" class="data-modal-body"></div>
    </div>
</div>

<script>
(function () {

    const dataModal = document.getElementById('dataModal');
    const dataModalTitle = document.getElementById('dataModalTitle');
    const dataModalBody = document.getElementById('dataModalBody');
    const dataModalFull = document.getElementById('dataModalFull');

    /* View-only: remove buttons, forms, scripts and the Actions column */
    function cleanModalContent(panel) {
        panel.querySelectorAll('script, form, button, .no-print').forEach(function (el) { el.remove(); });
        panel.querySelectorAll('[id]').forEach(function (el) { el.removeAttribute('id'); });
        panel.querySelectorAll('table').forEach(function (table) {
            const headers = Array.from(table.querySelectorAll('thead th'));
            const idx = headers.findIndex(function (th) { return th.textContent.trim().toLowerCase() === 'actions'; });
            if (idx === -1) return;
            table.querySelectorAll('tr').forEach(function (row) {
                const cell = row.children[idx];
                if (cell) cell.remove();
            });
        });
    }

    function applyFilter(panel, attr, value) {
        if (!attr || !value) return;
        panel.querySelectorAll('tbody tr[' + attr + ']').forEach(function (row) {
            if (row.getAttribute(attr) !== value) row.remove();
        });
    }

    function showPanel(panel, trigger) {
        cleanModalContent(panel);
        applyFilter(panel, trigger.getAttribute('data-filter-attr'), trigger.getAttribute('data-filter-value'));
        dataModalBody.innerHTML = '';
        dataModalBody.appendChild(document.importNode(panel, true));
    }

    async function loadModalData(trigger) {
        dataModalTitle.textContent = trigger.getAttribute('data-title') || 'Details';
        dataModalBody.innerHTML = '<div class="modal-loading">Loading...</div>';
        dataModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        const target = trigger.getAttribute('data-target');

        /* Data already on this page */
        if (target) {
            dataModalFull.style.display = 'none';
            const source = document.querySelector(target);
            if (!source) { dataModalBody.innerHTML = '<div class="modal-loading">Unable to load data.</div>'; return; }
            showPanel(source.cloneNode(true), trigger);
            return;
        }

        /* Data from another page */
        const url = trigger.getAttribute('href');
        dataModalFull.style.display = '';
        dataModalFull.href = url;

        try {
            const response = await fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('Request failed');
            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const panel = doc.querySelector(trigger.getAttribute('data-selector') || '.table-panel');
            if (!panel) throw new Error('No data');
            showPanel(panel, trigger);
        } catch (error) {
            dataModalBody.innerHTML = '<div class="modal-loading">Unable to load data.</div>';
        }
    }

    window.closeDataModal = function () {
        dataModal.classList.remove('show');
        dataModalBody.innerHTML = '';
        document.body.style.overflow = '';
    };

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('.js-modal');
        if (trigger && !dataModal.contains(trigger)) {
            event.preventDefault();
            loadModalData(trigger);
        }
    });

})();
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