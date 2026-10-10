<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - DPAM IMS</title>

    <style>
        :root {
            --bg:#f1f5f9;
            --ink:#0f172a;
            --muted:#64748b;
            --line:#e2e8f0;
            --sidebar:#0b1220;
            --accent:#2563eb;
            --accent-dark:#1d4ed8;
        }

        * {
            box-sizing:border-box;
        }

        body {
            margin:0;
            font-family:"Segoe UI",system-ui,-apple-system,Roboto,Arial,sans-serif;
            background:var(--bg);
            color:var(--ink);
        }

        a {
            color:inherit;
            text-decoration:none;
        }

        .layout {
            display:flex;
            min-height:100vh;
        }

        /* =========================================================
           SIDEBAR
        ========================================================= */

        .sidebar {
            width:256px;
            flex-shrink:0;
            background:var(--sidebar);
            color:#fff;
            padding:24px 14px;
            display:flex;
            flex-direction:column;
            position:fixed;
            top:0;
            left:0;
            bottom:0;
            overflow-y:auto;
        }

        .brand {
            font-size:20px;
            font-weight:700;
            padding:0 12px;
        }

        .subtitle {
            font-size:12px;
            color:#94a3b8;
            line-height:1.5;
            padding:0 12px;
            margin:4px 0 26px;
        }

        .menu-title {
            font-size:11px;
            color:#64748b;
            text-transform:uppercase;
            letter-spacing:.08em;
            margin:6px 12px 8px;
        }

        .menu a,
        .logout-button {
            display:flex;
            align-items:center;
            width:100%;
            padding:11px 12px;
            margin-bottom:4px;
            border-radius:8px;
            color:#cbd5e1;
            background:transparent;
            border:none;
            font-size:14px;
            font-family:inherit;
            cursor:pointer;
            text-align:left;
        }

        .menu a:hover,
        .logout-button:hover {
            background:#1e293b;
            color:#fff;
        }

        .menu a.active {
            background:var(--accent);
            color:#fff;
            font-weight:600;
        }

        .logout-form {
            margin-top:auto;
            padding-top:24px;
        }

        /* =========================================================
           MAIN
        ========================================================= */

        .main {
            flex:1;
            min-width:0;
            margin-left:256px;
            padding:30px 34px;
        }

        .topbar {
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:16px;
            margin-bottom:22px;
            flex-wrap:wrap;
        }

        .page-title {
            margin:0;
            font-size:26px;
            font-weight:700;
        }

        .page-description {
            margin-top:5px;
            color:var(--muted);
            font-size:14px;
        }

        .topbar-actions {
            display:flex;
            align-items:center;
            gap:10px;
        }

        .role-badge {
            background:#dbeafe;
            color:#1e40af;
            padding:7px 14px;
            border-radius:999px;
            font-size:12px;
            font-weight:700;
        }

        /* =========================================================
           BUTTONS
        ========================================================= */

        .btn {
            border:none;
            border-radius:8px;
            padding:10px 16px;
            cursor:pointer;
            font-size:14px;
            font-weight:600;
            font-family:inherit;
            display:inline-block;
            text-align:center;
        }

        .btn-primary {
            background:var(--accent);
            color:#fff;
        }

        .btn-primary:hover {
            background:var(--accent-dark);
        }

        .btn-secondary {
            background:#334155;
            color:#fff;
        }

        .btn-cancel {
            background:#e2e8f0;
            color:#334155;
        }

        .btn-cancel:hover {
            background:#cbd5e1;
        }

        /* =========================================================
           FILTER
        ========================================================= */

        .filter-box {
            background:#fff;
            border:1px solid var(--line);
            border-radius:14px;
            padding:18px;
            margin-bottom:22px;
        }

        .filter-title {
            font-size:15px;
            font-weight:700;
            margin-bottom:4px;
        }

        .filter-description {
            color:var(--muted);
            font-size:12px;
            margin-bottom:15px;
        }

        .filter-grid {
            display:grid;
            grid-template-columns:1fr 1fr 2fr auto auto;
            gap:12px;
            align-items:end;
        }

        .form-group label {
            display:block;
            font-size:13px;
            font-weight:600;
            margin-bottom:7px;
            color:#334155;
        }

        .form-group input[type="date"] {
            width:100%;
            padding:10px 12px;
            border:1px solid #cbd5e1;
            border-radius:8px;
            font-size:14px;
            outline:none;
            font-family:inherit;
            background:#fff;
        }

        .form-group input[type="date"]:focus {
            border-color:var(--accent);
            box-shadow:0 0 0 3px rgba(37,99,235,.08);
        }

        /* =========================================================
           SEARCHABLE PRODUCT PICKER
        ========================================================= */

        .product-picker {
            position:relative;
        }

        .product-search-input {
            width:100%;
            height:40px;
            padding:10px 40px 10px 12px;
            border:1px solid #cbd5e1;
            border-radius:8px;
            font-size:14px;
            outline:none;
            font-family:inherit;
            background:#fff;
            color:#0f172a;
            transition:
                border-color .15s ease,
                box-shadow .15s ease,
                background .15s ease;
        }

        .product-search-input::placeholder {
            color:#94a3b8;
        }

        .product-search-input:focus {
            border-color:var(--accent);
            box-shadow:0 0 0 3px rgba(37,99,235,.08);
        }

        .product-search-input.has-selection {
            background:#eff6ff;
            border-color:#93c5fd;
            color:#1e3a8a;
            font-weight:600;
        }

        .product-picker-arrow {
            position:absolute;
            right:12px;
            top:50%;
            transform:translateY(-50%);
            color:#64748b;
            pointer-events:none;
            font-size:11px;
            transition:transform .15s ease;
        }

        .product-picker.open .product-picker-arrow {
            transform:translateY(-50%) rotate(180deg);
        }

        .product-results {
            position:absolute;
            z-index:1000;
            top:calc(100% + 5px);
            left:0;
            right:0;
            background:#fff;
            border:1px solid #cbd5e1;
            border-radius:10px;
            box-shadow:0 10px 25px rgba(15,23,42,.12);
            max-height:280px;
            overflow-y:auto;
            display:none;
        }

        .product-results.show {
            display:block;
        }

        .product-option {
            width:100%;
            padding:11px 13px;
            border:none;
            background:#fff;
            text-align:left;
            cursor:pointer;
            font-family:inherit;
            border-bottom:1px solid #f1f5f9;
            transition:background .12s ease;
        }

        .product-option:last-child {
            border-bottom:none;
        }

        .product-option:hover,
        .product-option.active {
            background:#eff6ff;
        }

        .product-option-name {
            display:block;
            font-size:13px;
            font-weight:600;
            color:#0f172a;
        }

        .product-option-brand {
            display:block;
            margin-top:2px;
            font-size:11px;
            color:#64748b;
        }

        .product-no-result {
            padding:14px;
            text-align:center;
            color:#64748b;
            font-size:12px;
        }

        /* =========================================================
           REPORT SUMMARY
        ========================================================= */

        .report-summary {
            background:#fff;
            border:1px solid var(--line);
            border-radius:14px;
            padding:16px 18px;
            margin-bottom:22px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:20px;
            flex-wrap:wrap;
        }

        .summary-title {
            font-size:13px;
            font-weight:700;
            color:#334155;
        }

        .summary-period {
            font-size:14px;
            font-weight:600;
            margin-top:3px;
        }

        .summary-items {
            display:flex;
            gap:24px;
            flex-wrap:wrap;
        }

        .summary-item {
            min-width:100px;
        }

        .summary-label {
            font-size:11px;
            color:var(--muted);
            margin-bottom:3px;
        }

        .summary-value {
            font-size:18px;
            font-weight:700;
        }

        /* =========================================================
           PRODUCT STOCK CARD INFO
        ========================================================= */

        .selected-product {
            background:#fff;
            border:1px solid #bfdbfe;
            border-radius:14px;
            padding:18px 20px;
            margin-bottom:22px;
        }

        .selected-product-title {
            font-size:11px;
            font-weight:700;
            color:#2563eb;
            text-transform:uppercase;
            letter-spacing:.05em;
            margin-bottom:5px;
        }

        .selected-product-name {
            font-size:20px;
            font-weight:700;
            margin-bottom:12px;
        }

        .product-details {
            display:grid;
            grid-template-columns:repeat(5,1fr);
            gap:14px;
        }

        .product-detail-label {
            font-size:11px;
            color:var(--muted);
            margin-bottom:3px;
        }

        .product-detail-value {
            font-size:13px;
            font-weight:600;
        }

        /* =========================================================
           PRIMARY REPORT
        ========================================================= */

        .primary-panel {
            border:2px solid #bfdbfe;
        }

        .primary-header {
            background:#eff6ff;
        }

        .primary-label {
            display:inline-block;
            background:#2563eb;
            color:#fff;
            padding:4px 9px;
            border-radius:999px;
            font-size:10px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:.04em;
            margin-bottom:7px;
        }

        /* =========================================================
           REPORT PANELS
        ========================================================= */

        .table-panel {
            background:#fff;
            border:1px solid var(--line);
            border-radius:14px;
            overflow:hidden;
            margin-bottom:22px;
        }

        .section-header {
            padding:17px 22px;
            border-bottom:1px solid var(--line);
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            flex-wrap:wrap;
        }

        .section-header h2 {
            margin:0;
            font-size:17px;
        }

        .section-header p {
            margin:4px 0 0;
            color:var(--muted);
            font-size:12px;
        }

        .table-wrapper {
            overflow-x:auto;
        }

        table {
            width:100%;
            border-collapse:collapse;
        }

        th,
        td {
            padding:11px 14px;
            border-bottom:1px solid var(--line);
            text-align:left;
            font-size:13px;
            vertical-align:middle;
            white-space:nowrap;
        }

        th {
            color:var(--muted);
            font-weight:600;
            background:#f8fafc;
        }

        tbody tr:hover td {
            background:#fafbfd;
        }

        tbody tr:last-child td {
            border-bottom:none;
        }

        tfoot td {
            font-weight:700;
            background:#f8fafc;
            border-top:2px solid var(--line);
        }

        .text-right {
            text-align:right;
        }

        .num {
            font-variant-numeric:tabular-nums;
        }

        .sub {
            color:var(--muted);
            font-size:11.5px;
            margin-top:2px;
        }

        .empty {
            color:var(--muted);
            font-size:14px;
            padding:30px 20px;
            text-align:center;
        }

        .pos {
            color:#166534;
            font-weight:700;
        }

        .neg {
            color:#991b1b;
            font-weight:700;
        }

        .badge {
            display:inline-block;
            padding:4px 10px;
            border-radius:999px;
            font-size:11px;
            font-weight:700;
        }

        .badge-green {
            background:#dcfce7;
            color:#166534;
        }

        .badge-yellow {
            background:#fef3c7;
            color:#92400e;
        }

        .badge-red {
            background:#fee2e2;
            color:#991b1b;
        }

        .badge-blue {
            background:#dbeafe;
            color:#1e40af;
        }

        .badge-gray {
            background:#e5e7eb;
            color:#374151;
        }

        .report-note {
            padding:11px 22px;
            background:#fafbfd;
            border-top:1px solid var(--line);
            color:var(--muted);
            font-size:12px;
        }

        .alert-error {
            padding:12px 15px;
            border-radius:10px;
            margin-bottom:18px;
            font-size:13px;
            background:#fee2e2;
            color:#991b1b;
            border:1px solid #fecaca;
        }

        .print-only {
            display:none;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media(max-width:1100px) {

            .filter-grid {
                grid-template-columns:1fr 1fr;
            }

            .product-details {
                grid-template-columns:repeat(3,1fr);
            }

            .summary-items {
                width:100%;
            }
        }

        @media(max-width:800px) {

            .sidebar {
                position:relative;
                width:100%;
            }

            .main {
                margin-left:0;
                padding:20px;
            }

            .layout {
                flex-direction:column;
            }

            .product-details {
                grid-template-columns:1fr 1fr;
            }
        }

        @media(max-width:560px) {

            .filter-grid {
                grid-template-columns:1fr;
            }

            .product-details {
                grid-template-columns:1fr;
            }

            .summary-items {
                display:grid;
                grid-template-columns:1fr 1fr;
                width:100%;
            }
        }

        /* =========================================================
           PRINT
        ========================================================= */

        @page {
            size:A4 landscape;
            margin:12mm;
        }

        @media print {

            .sidebar,
            .topbar,
            .filter-box,
            .report-summary,
            .no-print,
            .alert-error {
                display:none !important;
            }

            .main {
                margin-left:0;
                padding:0;
            }

            body {
                background:#fff;
            }

            .layout {
                display:block;
            }

            .print-only {
                display:block;
            }

            .table-wrapper {
                overflow:visible;
            }

            .table-panel {
                border:1px solid #ccc;
                margin-bottom:14px;
                break-inside:avoid;
            }

            .primary-panel {
                border:2px solid #333;
            }

            th,
            td {
                padding:6px 8px;
                font-size:10px;
                white-space:normal;
            }

            thead {
                display:table-header-group;
            }

            .selected-product {
                border:1px solid #ccc;
                break-inside:avoid;
            }
        }
    </style>
</head>

<body>

@php

    $peso = fn ($v) =>
        '₱' . number_format((float) $v, 2);

    $periodText = ($dateFrom || $dateTo)
        ? (
            ($dateFrom
                ? \Carbon\Carbon::parse($dateFrom)->format('M d, Y')
                : 'Beginning')
            . ' – ' .
            ($dateTo
                ? \Carbon\Carbon::parse($dateTo)->format('M d, Y')
                : 'Present')
        )
        : 'All Records';

    $statusClass = fn ($status) => match (
        strtolower((string) $status)
    ) {

        'out of stock',
        'cancelled',
        'voided'
            => 'badge-red',

        'low stock',
        'pending',
        'draft',
        'unpaid'
            => 'badge-yellow',

        'approved',
        'received',
        'fulfilled',
        'paid',
        'in stock'
            => 'badge-green',

        'partially received',
        'partially fulfilled'
            => 'badge-blue',

        default
            => 'badge-gray',
    };

    $selectedProduct = $productId
        ? $productOptions->firstWhere('id', $productId)
        : null;

    $selectedInventory = null;

    if ($selectedProduct) {

        $selectedInventory = $inventory->firstWhere(
            'product_id',
            $selectedProduct->id
        );

    }

@endphp


<div class="layout">

    {{-- =========================================================
         SIDEBAR
    ========================================================= --}}

    <aside class="sidebar">

        <div class="brand">
            DPAM IMS
        </div>

        <div class="subtitle">
            Industrial Supplies & Services Inc.
        </div>

        <div class="menu-title">
            Owner
        </div>

        <nav class="menu">

            <a href="{{ route('owner.dashboard') }}">
                Dashboard
            </a>

            <a href="{{ route('owner.sales-inventory') }}">
                Sales & Inventory
            </a>

            <a href="{{ route('owner.suppliers') }}">
                Suppliers
            </a>

            <a href="{{ route('owner.purchase-orders') }}">
                Purchase Orders
            </a>

            <a href="{{ route('owner.reports') }}"
               class="active">
                Reports
            </a>

            <a href="{{ route('owner.archive') }}">
                Archive
            </a>

        </nav>

        <form action="{{ route('logout') }}"
              method="POST"
              class="logout-form">

            @csrf

            <button type="submit"
                    class="logout-button">
                Logout
            </button>

        </form>

    </aside>


    {{-- =========================================================
         MAIN
    ========================================================= --}}

    <main class="main">

        {{-- =====================================================
             HEADER
        ===================================================== --}}

        <div class="topbar">

            <div>

                <h1 class="page-title">
                    Reports
                </h1>

                <div class="page-description">
                    Historical inventory, purchasing, and customer transaction records.
                </div>

            </div>

            <div class="topbar-actions">

                <button type="button"
                        class="btn btn-secondary"
                        onclick="window.print()">

                    Print / Save as PDF

                </button>

                <div class="role-badge">
                    OWNER
                </div>

            </div>

        </div>


        {{-- =====================================================
             ERRORS
        ===================================================== --}}

        @if($errors->any())

            <div class="alert-error">

                <strong>
                    Please check the following:
                </strong>

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- =====================================================
             FILTER
        ===================================================== --}}

        <div class="filter-box">

            <div class="filter-title">
                Stock Card Filters
            </div>

            <div class="filter-description">
                Search for one product and select an optional date range
                to view its continuous inventory history.
            </div>

            <form method="GET"
                  action="{{ route('owner.reports') }}">

                <div class="filter-grid">

                    {{-- DATE FROM --}}

                    <div class="form-group">

                        <label for="date_from">
                            Date From
                        </label>

                        <input type="date"
                               id="date_from"
                               name="date_from"
                               value="{{ $dateFrom }}">

                    </div>


                    {{-- DATE TO --}}

                    <div class="form-group">

                        <label for="date_to">
                            Date To
                        </label>

                        <input type="date"
                               id="date_to"
                               name="date_to"
                               value="{{ $dateTo }}">

                    </div>


                    {{-- SEARCHABLE PRODUCT --}}

                    <div class="form-group">

                        <label for="product_search">
                            Product
                        </label>

                        <div class="product-picker"
                             id="product_picker">

                            {{-- Actual product ID submitted to Laravel --}}
                            <input type="hidden"
                                   name="product_id"
                                   id="product_id"
                                   value="{{ $productId }}">

                            {{-- Search / selected product field --}}
                            <input type="text"
                                   id="product_search"
                                   class="product-search-input {{ $selectedProduct ? 'has-selection' : '' }}"
                                   placeholder="Search product or brand..."
                                   autocomplete="off"
                                   value="{{ $selectedProduct
                                        ? $selectedProduct->product_name .
                                            ($selectedProduct->brand
                                                ? ' — '.$selectedProduct->brand
                                                : '')
                                        : '' }}">

                            <span class="product-picker-arrow">
                                ▼
                            </span>


                            {{-- PRODUCT RESULTS --}}

                            <div id="product_results"
                                 class="product-results">

                                @foreach($productOptions as $opt)

                                    <button type="button"
                                            class="product-option"
                                            data-id="{{ $opt->id }}"
                                            data-name="{{ $opt->product_name }}"
                                            data-brand="{{ $opt->brand ?? '' }}">

                                        <span class="product-option-name">

                                            {{ $opt->product_name }}

                                        </span>

                                        @if($opt->brand)

                                            <span class="product-option-brand">

                                                {{ $opt->brand }}

                                            </span>

                                        @endif

                                    </button>

                                @endforeach

                            </div>

                        </div>

                    </div>


                    {{-- GENERATE --}}

                    <button type="submit"
                            class="btn btn-primary">

                        Generate

                    </button>


                    {{-- CLEAR --}}

                    <a href="{{ route('owner.reports') }}"
                       class="btn btn-cancel">

                        Clear

                    </a>

                </div>

            </form>

        </div>


        {{-- =====================================================
             PRINT HEADER
        ===================================================== --}}

        <div class="print-only"
             style="margin-bottom:14px;">

            <h1 style="margin:0 0 4px;font-size:20px;">
                DPAM Industrial Supplies and Services Inc.
            </h1>

            <h2 style="margin:0 0 4px;font-size:15px;">
                Inventory Management Reports
            </h2>

            <p style="margin:0;font-size:12px;">

                Reporting Period:
                {{ $periodText }}

                · Printed
                {{ now()->format('M d, Y h:i A') }}

            </p>

        </div>


        {{-- =====================================================
             REPORT SUMMARY
        ===================================================== --}}

        <div class="report-summary">

            <div>

                <div class="summary-title">
                    REPORTING PERIOD
                </div>

                <div class="summary-period">
                    {{ $periodText }}
                </div>

            </div>

            <div class="summary-items">

                <div class="summary-item">

                    <div class="summary-label">
                        Stock In
                    </div>

                    <div class="summary-value">
                        {{ number_format($totalStockInQuantity) }}
                    </div>

                </div>

                <div class="summary-item">

                    <div class="summary-label">
                        Stock Out
                    </div>

                    <div class="summary-value">
                        {{ number_format($totalStockOutQuantity) }}
                    </div>

                </div>

                <div class="summary-item">

                    <div class="summary-label">
                        Purchase Orders
                    </div>

                    <div class="summary-value">
                        {{ $purchaseOrders->count() }}
                    </div>

                </div>

                <div class="summary-item">

                    <div class="summary-label">
                        Customer Orders
                    </div>

                    <div class="summary-value">
                        {{ $customerOrders->count() }}
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             1. STOCK CARD REPORT
        ===================================================== --}}

        <div class="table-panel primary-panel">

            <div class="section-header primary-header">

                <div>

                    <div class="primary-label">
                        Primary Inventory Report
                    </div>

                    <h2>
                        Stock Card Report
                    </h2>

                    <p>
                        Continuous inventory movement for one selected product.
                    </p>

                </div>

            </div>


            @if($selectedProduct)

                {{-- SELECTED PRODUCT INFORMATION --}}

                <div class="selected-product">

                    <div class="selected-product-title">
                        Selected Product
                    </div>

                    <div class="selected-product-name">
                        {{ $selectedProduct->product_name }}
                    </div>

                    <div class="product-details">

                        <div>

                            <div class="product-detail-label">
                                Brand
                            </div>

                            <div class="product-detail-value">
                                {{ $selectedProduct->brand ?: '—' }}
                            </div>

                        </div>

                        <div>

                            <div class="product-detail-label">
                                Current Stock
                            </div>

                            <div class="product-detail-value">

                                @if($selectedInventory)

                                    {{ number_format($selectedInventory['current_stock']) }}

                                @else

                                    0

                                @endif

                            </div>

                        </div>

                        <div>

                            <div class="product-detail-label">
                                Reorder Level
                            </div>

                            <div class="product-detail-value">

                                @if($selectedInventory)

                                    {{ number_format($selectedInventory['reorder_level']) }}

                                @else

                                    0

                                @endif

                            </div>

                        </div>

                        <div>

                            <div class="product-detail-label">
                                Unit
                            </div>

                            <div class="product-detail-value">

                                {{ $selectedInventory['unit'] ?? '—' }}

                            </div>

                        </div>

                        <div>

                            <div class="product-detail-label">
                                Status
                            </div>

                            <div class="product-detail-value">

                                @if($selectedInventory)

                                    <span class="badge {{ $statusClass($selectedInventory['status']) }}">

                                        {{ $selectedInventory['status'] }}

                                    </span>

                                @else

                                    —

                                @endif

                            </div>

                        </div>

                    </div>

                </div>


                {{-- STOCK CARD TABLE --}}

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Date
                                </th>

                                <th class="text-right">
                                    Beginning Balance
                                </th>

                                <th class="text-right">
                                    Stock In
                                </th>

                                <th class="text-right">
                                    Stock Out
                                </th>

                                <th class="text-right">
                                    Remaining Balance
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($stockCard as $r)

                                <tr>

                                    <td>

                                        {{ $r['date']
                                            ? $r['date']->format('M d, Y')
                                            : '—' }}

                                    </td>

                                    <td class="text-right num">

                                        {{ number_format($r['begin']) }}

                                    </td>

                                    <td class="text-right num">

                                        @if($r['in'] > 0)

                                            <span class="pos">
                                                +{{ number_format($r['in']) }}
                                            </span>

                                        @else

                                            0

                                        @endif

                                    </td>

                                    <td class="text-right num">

                                        @if($r['out'] > 0)

                                            <span class="neg">
                                                −{{ number_format($r['out']) }}
                                            </span>

                                        @else

                                            0

                                        @endif

                                    </td>

                                    <td class="text-right num">

                                        <strong>
                                            {{ number_format($r['remain']) }}
                                        </strong>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5"
                                        class="empty">

                                        No inventory movements found for this product
                                        during the selected period.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                        @if(count($stockCard))

                            <tfoot>

                                <tr>

                                    <td>
                                        Period Totals
                                    </td>

                                    <td></td>

                                    <td class="text-right num">

                                        {{ number_format($totalStockInQuantity) }}

                                    </td>

                                    <td class="text-right num">

                                        {{ number_format($totalStockOutQuantity) }}

                                    </td>

                                    <td></td>

                                </tr>

                            </tfoot>

                        @endif

                    </table>

                </div>


                <div class="report-note">

                    <strong>
                        Stock Card Rule:
                    </strong>

                    Opening Stock establishes the Beginning Balance
                    and is not counted as Stock In.
                    Supplier deliveries are Stock In.
                    Customer releases are Stock Out.
                    Each following Beginning Balance comes from the
                    previous Remaining Balance.

                </div>

            @else

                <div class="empty"
                     style="padding:55px 20px;">

                    <strong style="display:block;color:#334155;margin-bottom:6px;">

                        Select a Product

                    </strong>

                    Search for and select a product above to generate
                    its continuous stock card.

                    <div style="margin-top:8px;font-size:12px;">

                        Each product has its own continuous stock ledger
                        so Beginning Balance, Stock In, Stock Out,
                        and Remaining Balance remain easy to audit.

                    </div>

                </div>

            @endif

        </div>


        {{-- =====================================================
             2. INVENTORY STATUS REPORT
        ===================================================== --}}

        <div class="table-panel">

            <div class="section-header">

                <div>

                    <h2>
                        Inventory Status Report
                    </h2>

                    <p>
                        Current inventory snapshot for management reference.
                    </p>

                </div>

            </div>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Item
                            </th>

                            <th>
                                Brand
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Unit
                            </th>

                            <th class="text-right">
                                Current Stock
                            </th>

                            <th class="text-right">
                                Reorder Level
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($inventory as $item)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $item['product_name'] }}
                                    </strong>

                                </td>

                                <td>
                                    {{ $item['brand'] ?: '—' }}
                                </td>

                                <td>
                                    {{ $item['category'] ?: '—' }}
                                </td>

                                <td>
                                    {{ $item['unit'] ?: '—' }}
                                </td>

                                <td class="text-right num">

                                    {{ number_format($item['current_stock']) }}

                                </td>

                                <td class="text-right num">

                                    {{ number_format($item['reorder_level']) }}

                                </td>

                                <td>

                                    <span class="badge {{ $statusClass($item['status']) }}">

                                        {{ $item['status'] }}

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="empty">

                                    No inventory records found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                    @if($inventory->count())

                        <tfoot>

                            <tr>

                                <td colspan="4">
                                    Total Current Inventory
                                </td>

                                <td class="text-right num">

                                    {{ number_format($totalInventoryQuantity) }}

                                </td>

                                <td></td>
                                <td></td>

                            </tr>

                        </tfoot>

                    @endif

                </table>

            </div>

        </div>


        {{-- =====================================================
             3. PURCHASE ORDER REPORT
        ===================================================== --}}

        <div class="table-panel">

            <div class="section-header">

                <div>

                    <h2>
                        Purchase Order Report
                    </h2>

                    <p>
                        Procurement history during the selected period.
                    </p>

                </div>

            </div>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Date
                            </th>

                            <th>
                                PO Number
                            </th>

                            <th>
                                Supplier
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-right">
                                Total
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($purchaseOrders as $po)

                            @php

                                $poStatus = strtolower(
                                    $po->status ?? 'unknown'
                                );

                                $poTotal = $po->total_amount
                                    ?? $po->items->sum(
                                        fn ($i) =>
                                            (float)$i->quantity *
                                            (float)$i->unit_cost
                                    );

                            @endphp

                            <tr>

                                <td>

                                    {{ $po->po_date
                                        ? \Carbon\Carbon::parse($po->po_date)->format('M d, Y')
                                        : '—' }}

                                </td>

                                <td>

                                    <strong>
                                        {{ $po->po_number ?? $po->id }}
                                    </strong>

                                </td>

                                <td>

                                    {{ $po->supplier->supplier_name ?? '—' }}

                                </td>

                                <td>

                                    <span class="badge {{ $statusClass($poStatus) }}">

                                        {{ ucfirst(
                                            str_replace('_', ' ', $poStatus)
                                        ) }}

                                    </span>

                                </td>

                                <td class="text-right num">

                                    {{ $peso($poTotal) }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="empty">

                                    No purchase orders found
                                    for the selected period.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="report-note">

                Purchase Order creation or approval does not increase inventory.
                Inventory increases only when the supplier delivery is actually
                recorded as Stock In.

            </div>

        </div>


        {{-- =====================================================
             4. CUSTOMER ORDER REPORT
        ===================================================== --}}

        <div class="table-panel">

            <div class="section-header">

                <div>

                    <h2>
                        Customer Order Report
                    </h2>

                    <p>
                        Customer order and payment history during the selected period.
                    </p>

                </div>

            </div>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Date
                            </th>

                            <th>
                                Order Number
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-right">
                                Total
                            </th>

                            <th class="text-right">
                                Collected
                            </th>

                            <th class="text-right">
                                Balance
                            </th>

                            <th>
                                Payment
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($customerOrders as $order)

                            @php

                                $orderStatus = strtolower(
                                    $order->status ?? 'unknown'
                                );

                                $paymentStatus = strtolower(
                                    $order->payment_status ?? ''
                                );

                            @endphp

                            <tr>

                                <td>

                                    {{ $order->order_date
                                        ? $order->order_date->format('M d, Y')
                                        : '—' }}

                                </td>

                                <td>

                                    <strong>
                                        {{ $order->order_number }}
                                    </strong>

                                </td>

                                <td>

                                    {{ $order->customer_name ?: '—' }}

                                </td>

                                <td>

                                    <span class="badge {{ $statusClass($orderStatus) }}">

                                        {{ ucfirst(
                                            str_replace('_', ' ', $orderStatus)
                                        ) }}

                                    </span>

                                </td>

                                <td class="text-right num">

                                    {{ $peso($order->total_amount) }}

                                </td>

                                <td class="text-right num">

                                    {{ $peso($order->collected_amount) }}

                                </td>

                                <td class="text-right num">

                                    {{ $peso($order->balance_amount) }}

                                </td>

                                <td>

                                    <span class="badge {{ $statusClass($paymentStatus) }}">

                                        {{ $paymentStatus
                                            ? ucfirst($paymentStatus)
                                            : '—' }}

                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="empty">

                                    No customer orders found
                                    for the selected period.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- =====================================================
             FOOTER
        ===================================================== --}}

        <div class="empty"
             style="padding:10px 0 25px;font-size:12px;">

            DPAM Industrial Supplies and Services Inc.
            — Inventory Management System

        </div>

    </main>

</div>


{{-- =============================================================
     SEARCHABLE PRODUCT JAVASCRIPT
============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {

    const picker =
        document.getElementById('product_picker');

    const searchInput =
        document.getElementById('product_search');

    const productIdInput =
        document.getElementById('product_id');

    const results =
        document.getElementById('product_results');

    const options =
        Array.from(
            document.querySelectorAll('.product-option')
        );


    if (
        !picker ||
        !searchInput ||
        !productIdInput ||
        !results
    ) {
        return;
    }


    /* =========================================================
       SHOW RESULTS
    ========================================================= */

    function showResults() {

        results.classList.add('show');

        picker.classList.add('open');

    }


    /* =========================================================
       HIDE RESULTS
    ========================================================= */

    function hideResults() {

        results.classList.remove('show');

        picker.classList.remove('open');

    }


    /* =========================================================
       FILTER PRODUCTS
    ========================================================= */

    function filterProducts() {

        const search =
            searchInput.value
                .trim()
                .toLowerCase();

        let visibleCount = 0;


        options.forEach(function (option) {

            const name =
                (
                    option.dataset.name || ''
                ).toLowerCase();

            const brand =
                (
                    option.dataset.brand || ''
                ).toLowerCase();


            const matches =
                search === '' ||
                name.includes(search) ||
                brand.includes(search);


            option.style.display =
                matches
                    ? 'block'
                    : 'none';


            if (matches) {

                visibleCount++;

            }

        });


        let noResult =
            results.querySelector(
                '.product-no-result'
            );


        if (visibleCount === 0) {

            if (!noResult) {

                noResult =
                    document.createElement('div');

                noResult.className =
                    'product-no-result';

                noResult.textContent =
                    'No matching product found.';

                results.appendChild(
                    noResult
                );

            }

        } else {

            if (noResult) {

                noResult.remove();

            }

        }


        showResults();

    }


    /* =========================================================
       OPEN ON FOCUS
    ========================================================= */

    searchInput.addEventListener(
        'focus',
        function () {

            filterProducts();

        }
    );


    /* =========================================================
       SEARCH WHILE TYPING
    ========================================================= */

    searchInput.addEventListener(
        'input',
        function () {

            /*
             * Since the user is typing a new search,
             * remove the previous product selection.
             */

            productIdInput.value = '';

            searchInput.classList.remove(
                'has-selection'
            );

            filterProducts();

        }
    );


    /* =========================================================
       SELECT PRODUCT
    ========================================================= */

    options.forEach(function (option) {

        option.addEventListener(
            'click',
            function () {

                const id =
                    option.dataset.id;

                const name =
                    option.dataset.name;

                const brand =
                    option.dataset.brand;


                /*
                 * Store actual product ID.
                 */

                productIdInput.value =
                    id;


                /*
                 * Display selected product
                 * directly inside the field.
                 */

                searchInput.value =
                    name +
                    (
                        brand
                            ? ' — ' + brand
                            : ''
                    );


                /*
                 * Give selected field
                 * a subtle blue state.
                 */

                searchInput.classList.add(
                    'has-selection'
                );


                hideResults();

            }
        );

    });


    /* =========================================================
       CLICK OUTSIDE
    ========================================================= */

    document.addEventListener(
        'click',
        function (event) {

            if (
                !event.target.closest(
                    '.product-picker'
                )
            ) {

                hideResults();

            }

        }
    );


    /* =========================================================
       ESCAPE KEY
    ========================================================= */

    searchInput.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {

                hideResults();

                searchInput.blur();

            }

        }
    );


    /* =========================================================
       INITIAL SELECTED STATE
    ========================================================= */

    if (productIdInput.value) {

        searchInput.classList.add(
            'has-selection'
        );

    }

});

</script>

</body>
</html>