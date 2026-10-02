<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Orders - DPAM IMS</title>

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

            <a href="{{ route('owner.customer-orders') }}" class="active">
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
                <h1 class="page-title">Customer Orders</h1>
                <div class="page-description">Record customer requests and monitor inventory availability.</div>
            </div>
            <div class="topbar-actions">
                <button type="button" class="btn btn-primary" onclick="openCreateOrderModal()">+ New Customer Order</button>
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

            <input type="text" id="orderSearch" placeholder="Search order number or customer..." oninput="filterOrders()">

            <select id="statusFilter" onchange="filterOrders()">
                <option value="">All Order Statuses</option>
                <option value="pending_inventory_check">Pending Inventory Check</option>
                <option value="confirmed">Confirmed</option>
                <option value="partially_fulfilled">Partially Fulfilled</option>
                <option value="fulfilled">Fulfilled</option>
                <option value="for_purchasing">For Purchasing</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <select id="inventoryFilter" onchange="filterOrders()">
                <option value="">All Inventory Check Results</option>
                <option value="pending">Pending Check</option>
                <option value="available">Stock Available</option>
                <option value="insufficient">Insufficient Stock</option>
            </select>

        </div>


        <section class="table-panel">

            <div class="table-wrapper">

                <table id="ordersTable">

                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Order Date</th>
                            <th>Products</th>
                            <th>Total</th>
                            <th>Inventory Check</th>
                            <th>Order Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($orders as $order)

                            @php

                                $statusLabel = match($order->status) {
                                    'pending_inventory_check' => 'Pending Inventory Check',
                                    'confirmed' => 'Confirmed',
                                    'partially_fulfilled' => 'Partially Fulfilled',
                                    'fulfilled' => 'Fulfilled',
                                    'for_purchasing' => 'For Purchasing',
                                    'cancelled' => 'Cancelled',
                                    default => ucfirst(str_replace('_', ' ', $order->status)),
                                };

                                $statusClass = match($order->status) {
                                    'pending_inventory_check' => 'status-pending',
                                    'confirmed' => 'status-confirmed',
                                    'partially_fulfilled' => 'status-partial',
                                    'fulfilled' => 'status-fulfilled',
                                    'for_purchasing' => 'status-purchasing',
                                    'cancelled' => 'status-cancelled',
                                    default => 'status-default',
                                };

                                $inventoryLabel = match($order->inventory_check_status) {
                                    'pending' => 'Pending Check',
                                    'available' => 'Stock Available',
                                    'insufficient' => 'Insufficient Stock',
                                    default => ucfirst(str_replace('_', ' ', $order->inventory_check_status)),
                                };

                                $inventoryClass = match($order->inventory_check_status) {
                                    'pending' => 'status-pending',
                                    'available' => 'status-available',
                                    'insufficient' => 'status-insufficient',
                                    default => 'status-default',
                                };

                                $orderTotal = $order->items->sum('subtotal');

                            @endphp

                            <tr data-order-number="{{ strtolower($order->order_number) }}" data-customer-name="{{ strtolower($order->customer_name) }}" data-status="{{ $order->status }}" data-inventory-status="{{ $order->inventory_check_status }}">

                                <td><strong>{{ $order->order_number }}</strong></td>

                                <td>
                                    <strong>{{ $order->customer_name }}</strong>
                                    @if($order->customer_contact)
                                        <div class="secondary-text">{{ $order->customer_contact }}</div>
                                    @endif
                                </td>

                                <td>{{ $order->order_date ? $order->order_date->format('M d, Y') : '—' }}</td>

                                <td>{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'items' }}</td>

                                <td><strong>₱{{ number_format($orderTotal, 2) }}</strong></td>

                                <td>
                                    <span class="status-badge {{ $inventoryClass }}">{{ $inventoryLabel }}</span>
                                    @if($order->inventoryCheckedBy)
                                        <span class="check-by">Checked by: {{ $order->inventoryCheckedBy->name }}</span>
                                    @endif
                                </td>

                                <td><span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span></td>

                                <td>
                                    <div class="actions">

                                        <button type="button" class="btn btn-sm btn-secondary" onclick="viewOrder({{ $order->id }})">View</button>

                                        @if($order->status === 'pending_inventory_check')
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary"
                                                onclick="openOwnerDecisionModal({{ $order->id }})"
                                            >
                                                Owner Decision
                                            </button>
                                        @endif

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr><td colspan="8" class="empty-state">No customer orders recorded yet.</td></tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </section>

    </main>


{{-- CREATE ORDER MODAL --}}
<div class="modal" id="createOrderModal">
    <div class="modal-content modal-large">

        <div class="modal-header">
            <div>
                <h2>New Customer Order</h2>
                <p>Record the customer's request for inventory checking.</p>
            </div>
            <button type="button" class="close-x" onclick="closeModal('createOrderModal')">&times;</button>
        </div>

        <div class="modal-body">

            <form action="{{ route('owner.customer-orders.store') }}" method="POST" id="createOrderForm">

                @csrf

                <h3>Customer Information</h3>

                <div class="form-grid-three">

                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" name="customer_name" required maxlength="255" placeholder="Enter customer name">
                    </div>

                    <div class="form-group">
                        <label>Contact</label>
                        <input type="text" name="customer_contact" maxlength="255" placeholder="Contact number">
                    </div>

                    <div class="form-group">
                        <label>Order Date</label>
                        <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required>
                    </div>

                </div>

                <div class="section-row">
                    <h3>Products Requested <span class="required">*</span></h3>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addOrderItem()">+ Add Product</button>
                </div>

                <div class="order-items-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Product <span class="required">*</span></th>
                                <th style="width:130px;">System Stock</th>
                                <th style="width:120px;">Requested Qty <span class="required">*</span></th>
                                <th style="width:140px;">Unit Price</th>
                                <th style="width:150px;">Subtotal</th>
                                <th style="width:60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="orderItemsContainer"></tbody>
                    </table>
                </div>

                <div class="order-total-box">
                    <div class="order-total">
                        <span class="order-total-label">Order Total</span>
                        <span class="order-total-value" id="orderTotal">₱0.00</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" maxlength="5000" placeholder="Additional customer order notes..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('createOrderModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Customer Order</button>
                </div>

            </form>

        </div>

    </div>
</div>


{{-- VIEW ORDER MODAL --}}
<div class="modal" id="viewOrderModal">
    <div class="modal-content modal-large">

        <div class="modal-header">
            <div>
                <h2>Customer Order Details</h2>
                <p id="viewOrderNumber"></p>
            </div>
            <button type="button" class="close-x" onclick="closeModal('viewOrderModal')">&times;</button>
        </div>

        <div class="modal-body">

            <div class="details-grid">
                <div class="detail-item"><span class="detail-label">Customer</span><span class="detail-value" id="viewCustomerName"></span></div>
                <div class="detail-item"><span class="detail-label">Contact</span><span class="detail-value" id="viewCustomerContact"></span></div>
                <div class="detail-item"><span class="detail-label">Order Date</span><span class="detail-value" id="viewOrderDate"></span></div>
                <div class="detail-item"><span class="detail-label">Order Status</span><span class="detail-value" id="viewOrderStatus"></span></div>
                <div class="detail-item"><span class="detail-label">Inventory Check</span><span class="detail-value" id="viewInventoryCheck"></span></div>
                <div class="detail-item"><span class="detail-label">Checked By</span><span class="detail-value" id="viewCheckedBy"></span></div>
            </div>

            <h3>Products Requested</h3>

            <div class="order-items-container">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="text-align:right;">Requested</th>
                            <th style="text-align:right;">Reserved</th>
                            <th style="text-align:right;">Fulfilled</th>
                            <th style="text-align:right;">Unit Price</th>
                            <th style="text-align:right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="viewOrderItems"></tbody>
                </table>
            </div>

            <div class="order-total-box">
                <div class="order-total">
                    <span class="order-total-label">Total</span>
                    <span class="order-total-value" id="viewOrderTotal">₱0.00</span>
                </div>
            </div>

            <div class="check-result pending" id="viewInventoryResultBox">
                <div class="check-title" id="viewInventoryResultTitle"></div>
                <div class="check-text" id="viewInventoryResultText"></div>
            </div>

            <div class="status-box">
                <div class="status-box-title">Owner Decision</div>
                <div id="viewOwnerDecision">—</div>
            </div>

            <div class="status-box">
                <div class="status-box-title">Inventory Check Notes</div>
                <div id="viewInventoryNotes">—</div>
            </div>

            <div class="status-box">
                <div class="status-box-title">Fulfillment Information</div>
                <div id="viewFulfillmentInfo" style="line-height:1.5;">No fulfillment recorded yet.</div>
            </div>

            <div class="status-box">
                <div class="status-box-title">Order Notes</div>
                <div id="viewOrderNotes">—</div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="closeModal('viewOrderModal')">Close</button>
            </div>

        </div>

    </div>
</div>


{{-- OWNER DECISION MODAL --}}
<div class="modal" id="ownerDecisionModal">
    <div class="modal-content">

        <div class="modal-header">
            <div>
                <h2>Owner Decision</h2>
                <p id="decisionOrderNumber"></p>
            </div>
            <button type="button" class="close-x" onclick="closeModal('ownerDecisionModal')">&times;</button>
        </div>

        <div class="modal-body">

            <div id="decisionInventoryMessage" class="info-notice"></div>

            <form method="POST" id="ownerDecisionForm">

                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label>Decision</label>
                    <select name="decision" id="ownerDecisionSelect" required onchange="updateDecisionDescription()">
                        <option value="">Select Decision</option>
                    </select>
                </div>

                <div id="decisionDescription" class="warning-notice">Select a decision.</div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('ownerDecisionModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Owner Decision</button>
                </div>

            </form>

        </div>

    </div>
</div>


{{-- JAVASCRIPT DATA --}}
@php

    $customerOrderJavascriptData = $orders->map(function ($order) {

        return [

            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_contact' => $order->customer_contact,
            'order_date' => $order->order_date ? $order->order_date->format('M d, Y') : '—',
            'status' => $order->status,
            'inventory_check_status' => $order->inventory_check_status,
            'inventory_checked_by' => $order->inventoryCheckedBy ? $order->inventoryCheckedBy->name : null,
            'inventory_checked_at' => $order->inventory_checked_at ? $order->inventory_checked_at->format('M d, Y h:i A') : null,
            'inventory_check_notes' => $order->inventory_check_notes,
            'owner_decision' => $order->owner_decision,
            'notes' => $order->notes,

            'items' => $order->items
                ->map(function ($item) {

                    return [
                        'product_name' => $item->product->product_name ?? '—',
                        'quantity' => (int) $item->quantity,
                        'reserved_quantity' => isset($item->reserved_quantity) ? (int) $item->reserved_quantity : 0,
                        'fulfilled_quantity' => isset($item->fulfilled_quantity) ? (int) $item->fulfilled_quantity : 0,
                        'unit_price' => (float) $item->unit_price,
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

    const customerOrders = @js($customerOrderJavascriptData);

    const products = @js($productJavascriptData);


    /* CREATE ORDER */
    function openCreateOrderModal()
    {
        const container = document.getElementById('orderItemsContainer');

        if (container.children.length === 0) {
            addOrderItem();
        }

        openModal('createOrderModal');
    }


    /* ADD ORDER ITEM */
    function addOrderItem()
    {
        const container = document.getElementById('orderItemsContainer');

        const index = container.children.length;

        const row = document.createElement('tr');

        row.innerHTML = `

            <td>
                <select name="items[${index}][product_id]" class="product-select" required onchange="updateProductRow(this)">
                    <option value="">Select Product</option>
                    ${
                        products.map(function (product) {
                            return `<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">${escapeHtml(product.name)}</option>`;
                        }).join('')
                    }
                </select>
            </td>

            <td><span class="stock-display stock-neutral">—</span></td>

            <td>
                <input type="number" name="items[${index}][quantity]" class="quantity-input" value="1" min="1" step="1" required oninput="updateRowSubtotal(this)">
            </td>

            <td><span class="price-display">₱0.00</span></td>

            <td><span class="subtotal-display">₱0.00</span></td>

            <td style="text-align:center;">
                <button type="button" class="remove-item" onclick="removeOrderItem(this)" title="Remove product">&times;</button>
            </td>

        `;

        container.appendChild(row);
    }


    /* PRODUCT CHANGE */
    function updateProductRow(select)
    {
        const row = select.closest('tr');

        const option = select.options[select.selectedIndex];

        if (!option) return;

        const price = Number(option.dataset.price || 0);
        const stock = Number(option.dataset.stock || 0);

        const stockDisplay = row.querySelector('.stock-display');
        const priceDisplay = row.querySelector('.price-display');

        stockDisplay.textContent = stock.toLocaleString();

        stockDisplay.classList.remove('stock-available', 'stock-low', 'stock-out', 'stock-neutral');

        if (stock <= 0) {
            stockDisplay.classList.add('stock-out');
        } else if (stock <= 5) {
            stockDisplay.classList.add('stock-low');
        } else {
            stockDisplay.classList.add('stock-available');
        }

        priceDisplay.textContent = formatCurrency(price);

        updateRowSubtotal(row.querySelector('.quantity-input'));
    }


    /* UPDATE SUBTOTAL */
    function updateRowSubtotal(input)
    {
        const row = input.closest('tr');

        const select = row.querySelector('.product-select');

        const option = select.options[select.selectedIndex];

        const price = Number(option?.dataset.price || 0);

        const quantity = Number(input.value || 0);

        row.querySelector('.subtotal-display').textContent = formatCurrency(price * quantity);

        updateOrderTotal();
    }


    /* ORDER TOTAL */
    function updateOrderTotal()
    {
        let total = 0;

        document.querySelectorAll('#orderItemsContainer .subtotal-display').forEach(function (element) {

            const value = element.textContent.replace('₱', '').replace(/,/g, '');

            total += Number(value) || 0;

        });

        document.getElementById('orderTotal').textContent = formatCurrency(total);
    }


    /* REMOVE ORDER ITEM */
    function removeOrderItem(button)
    {
        const row = button.closest('tr');

        if (row) row.remove();

        reindexOrderItems();

        updateOrderTotal();
    }


    /* REINDEX */
    function reindexOrderItems()
    {
        document.querySelectorAll('#orderItemsContainer tr').forEach(function (row, index) {

            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');

            if (productSelect) productSelect.name = `items[${index}][product_id]`;
            if (quantityInput) quantityInput.name = `items[${index}][quantity]`;

        });
    }


    /* VIEW ORDER */
    function viewOrder(orderId)
    {
        const order = customerOrders.find(function (order) { return Number(order.id) === Number(orderId); });

        if (!order) return;

        document.getElementById('viewOrderNumber').textContent = order.order_number;
        document.getElementById('viewCustomerName').textContent = order.customer_name || '—';
        document.getElementById('viewCustomerContact').textContent = order.customer_contact || '—';
        document.getElementById('viewOrderDate').textContent = order.order_date || '—';
        document.getElementById('viewOrderStatus').textContent = formatStatus(order.status);
        document.getElementById('viewInventoryCheck').textContent = formatInventoryStatus(order.inventory_check_status);
        document.getElementById('viewCheckedBy').textContent = order.inventory_checked_by || 'Not checked yet';
        document.getElementById('viewOwnerDecision').textContent = formatOwnerDecision(order.owner_decision);
        document.getElementById('viewInventoryNotes').textContent = order.inventory_check_notes || 'No inventory-check notes recorded.';
        document.getElementById('viewOrderNotes').textContent = order.notes || '—';


        /* Inventory Result Box */
        const resultBox = document.getElementById('viewInventoryResultBox');
        const resultTitle = document.getElementById('viewInventoryResultTitle');
        const resultText = document.getElementById('viewInventoryResultText');

        resultBox.className = 'check-result';

        if (order.inventory_check_status === 'available') {

            resultBox.classList.add('available');
            resultTitle.textContent = 'Stock Available';
            resultText.textContent = 'The requested quantity can currently be supplied.';

        } else if (order.inventory_check_status === 'insufficient') {

            resultBox.classList.add('insufficient');
            resultTitle.textContent = 'Insufficient Stock';
            resultText.textContent = 'Available quantity is not enough for this order.';

        } else {

            resultBox.classList.add('pending');
            resultTitle.textContent = 'Inventory Check Pending';
            resultText.textContent = 'The inventory check has not been completed yet.';

        }


        /* Order Items */
        const container = document.getElementById('viewOrderItems');

        container.innerHTML = '';

        let total = 0;

        if (!order.items || order.items.length === 0) {

            container.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:25px;color:#64748b;">No products recorded.</td></tr>';

        } else {

            order.items.forEach(function (item) {

                total += Number(item.subtotal) || 0;

                const requested = Number(item.quantity || 0);
                const reserved = Number(item.reserved_quantity || 0);
                const fulfilled = Number(item.fulfilled_quantity || 0);

                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${escapeHtml(item.product_name)}</td>
                    <td style="text-align:right;">${requested.toLocaleString()}</td>
                    <td style="text-align:right;color:#1e40af;font-weight:bold;">${reserved.toLocaleString()}</td>
                    <td style="text-align:right;color:#166534;font-weight:bold;">${fulfilled.toLocaleString()}</td>
                    <td style="text-align:right;">${formatCurrency(item.unit_price)}</td>
                    <td style="text-align:right;font-weight:bold;">${formatCurrency(item.subtotal)}</td>
                `;

                container.appendChild(row);

            });

        }

        document.getElementById('viewOrderTotal').textContent = formatCurrency(total);


        /* Fulfillment Information */
        let totalRequested = 0;
        let totalReserved = 0;
        let totalFulfilled = 0;

        if (order.items) {

            order.items.forEach(function (item) {

                totalRequested += Number(item.quantity || 0);
                totalReserved += Number(item.reserved_quantity || 0);
                totalFulfilled += Number(item.fulfilled_quantity || 0);

            });

        }

        const remaining = Math.max(0, totalRequested - totalFulfilled);

        let fulfillmentText =
            'Requested: ' + totalRequested.toLocaleString() + '<br>' +
            'Reserved/Committed: ' + totalReserved.toLocaleString() + '<br>' +
            'Fulfilled/Released: ' + totalFulfilled.toLocaleString() + '<br>' +
            'Remaining: ' + remaining.toLocaleString();

        if (order.status === 'fulfilled') {
            fulfillmentText += '<br><br><strong>The customer order has been fully fulfilled.</strong>';
        } else if (order.status === 'partially_fulfilled') {
            fulfillmentText += '<br><br><strong>The customer order has been partially fulfilled.</strong>';
        }

        document.getElementById('viewFulfillmentInfo').innerHTML = fulfillmentText;

        openModal('viewOrderModal');
    }


    /* OWNER DECISION MODAL */
    function openOwnerDecisionModal(orderId)
    {
        const order = customerOrders.find(function (order) { return Number(order.id) === Number(orderId); });

        if (!order) return;

        document.getElementById('decisionOrderNumber').textContent = order.order_number;

        const message = document.getElementById('decisionInventoryMessage');

        const select = document.getElementById('ownerDecisionSelect');

        select.value = '';

        if (order.inventory_check_status === 'pending') {

            message.className = 'warning-notice';

            message.innerHTML = '<strong>Inventory check is still pending.</strong><br>The order can only be cancelled until the check is completed.';

            select.innerHTML = `
                <option value="">Select Decision</option>
                <option value="cancelled">Cancel Order</option>
            `;

        } else if (order.inventory_check_status === 'available') {

            message.className = 'info-notice';

            message.innerHTML = '<strong>Stock Available.</strong><br>You may confirm or cancel this order.';

            select.innerHTML = `
                <option value="">Select Decision</option>
                <option value="confirmed">Confirm Order</option>
                <option value="cancelled">Cancel Order</option>
            `;

        } else if (order.inventory_check_status === 'insufficient') {

            message.className = 'warning-notice';

            message.innerHTML = '<strong>Insufficient Stock.</strong><br>You may proceed to purchasing or cancel this order.';

            select.innerHTML = `
                <option value="">Select Decision</option>
                <option value="for_purchasing">Proceed to Purchasing</option>
                <option value="cancelled">Cancel Order</option>
            `;

        }

        document.getElementById('ownerDecisionForm').action = "{{ url('/owner/customer-orders') }}/" + order.id + "/decision";

        updateDecisionDescription();

        openModal('ownerDecisionModal');
    }


    /* DECISION DESCRIPTION */
    function updateDecisionDescription()
    {
        const decision = document.getElementById('ownerDecisionSelect').value;

        const description = document.getElementById('decisionDescription');

        if (decision === 'confirmed') {

            description.className = 'info-notice';
            description.innerHTML = '<strong>Confirm Order:</strong> the ordered quantity will be reserved. Physical stock is not deducted.';

        } else if (decision === 'for_purchasing') {

            description.className = 'warning-notice';
            description.innerHTML = '<strong>Proceed to Purchasing:</strong> a Purchase Order can now be prepared for the missing quantity.';

        } else if (decision === 'cancelled') {

            description.className = 'alert-error';
            description.innerHTML = '<strong>Cancel Order:</strong> the order will be cancelled and any reservation released.';

        } else {

            description.className = 'warning-notice';
            description.innerHTML = 'Select a decision.';

        }
    }


    /* FILTER */
    function filterOrders()
    {
        const search = document.getElementById('orderSearch').value.toLowerCase().trim();
        const status = document.getElementById('statusFilter').value;
        const inventoryStatus = document.getElementById('inventoryFilter').value;

        document.querySelectorAll('#ordersTable tbody tr').forEach(function (row) {

            const orderNumber = row.dataset.orderNumber || '';
            const customerName = row.dataset.customerName || '';
            const rowStatus = row.dataset.status || '';
            const rowInventoryStatus = row.dataset.inventoryStatus || '';

            const matchesSearch = orderNumber.includes(search) || customerName.includes(search);
            const matchesStatus = status === '' || rowStatus === status;
            const matchesInventory = inventoryStatus === '' || rowInventoryStatus === inventoryStatus;

            row.style.display = matchesSearch && matchesStatus && matchesInventory ? '' : 'none';

        });
    }


    /* FORMAT ORDER STATUS */
    function formatStatus(status)
    {
        switch (status) {

            case 'pending_inventory_check':
                return 'Pending Inventory Check';

            case 'confirmed':
                return 'Confirmed';

            case 'partially_fulfilled':
                return 'Partially Fulfilled';

            case 'fulfilled':
                return 'Fulfilled';

            case 'for_purchasing':
                return 'For Purchasing';

            case 'cancelled':
                return 'Cancelled';

            default:
                return String(status || '')
                    .replace(/_/g, ' ')
                    .replace(/\b\w/g, function (letter) {
                        return letter.toUpperCase();
                    });
        }
    }


    /* FORMAT INVENTORY STATUS */
    function formatInventoryStatus(status)
    {
        switch (status) {

            case 'pending': return 'Pending Check';
            case 'available': return 'Stock Available';
            case 'insufficient': return 'Insufficient Stock';

            default: return '—';
        }
    }


    /* FORMAT OWNER DECISION */
    function formatOwnerDecision(decision)
    {
        switch (decision) {

            case 'confirmed': return 'Confirmed';
            case 'for_purchasing': return 'Proceed to Purchasing';
            case 'cancelled': return 'Cancelled';

            default: return 'No decision yet';
        }
    }


    /* CURRENCY */
    function formatCurrency(value)
    {
        return '₱' + Number(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }


    /* CREATE ORDER VALIDATION */
    document.getElementById('createOrderForm').addEventListener('submit', function (event) {

        const rows = document.querySelectorAll('#orderItemsContainer tr');

        if (rows.length === 0) {
            event.preventDefault();
            alert('Please add at least one product.');
            return;
        }

        let hasEmptyProduct = false;
        let hasInvalidQuantity = false;
        let hasDuplicateProduct = false;

        const selectedProducts = new Set();

        rows.forEach(function (row) {

            const product = row.querySelector('.product-select').value;
            const quantity = Number(row.querySelector('.quantity-input').value);

            if (!product) hasEmptyProduct = true;

            if (!quantity || quantity < 1) hasInvalidQuantity = true;

            if (product && selectedProducts.has(product)) hasDuplicateProduct = true;

            if (product) selectedProducts.add(product);

        });

        if (hasEmptyProduct) {
            event.preventDefault();
            alert('Please select a product for every order item.');
            return;
        }

        if (hasInvalidQuantity) {
            event.preventDefault();
            alert('Every requested quantity must be at least 1.');
            return;
        }

        if (hasDuplicateProduct) {
            event.preventDefault();
            alert('The same product cannot be added more than once. Please combine the quantities into one line.');
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