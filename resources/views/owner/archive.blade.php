<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Archive - DPAM IMS</title>

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

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family:
                "Segoe UI",
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Roboto",
                Arial,
                sans-serif;
            background: var(--bg);
            color: var(--ink);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        /* =========================================================
           LAYOUT
        ========================================================= */

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================================================
           SIDEBAR
        ========================================================= */

        .sidebar {
            width: 256px;
            flex-shrink: 0;

            background: var(--sidebar);
            color: #fff;

            padding: 24px 14px;

            display: flex;
            flex-direction: column;

            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;

            overflow-y: auto;
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
            padding: 0 12px;
        }

        .subtitle {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;

            padding: 0 12px;
            margin: 4px 0 26px;
        }

        .menu-title {
            font-size: 11px;
            color: #64748b;

            text-transform: uppercase;
            letter-spacing: .08em;

            margin: 6px 12px 8px;
        }

        .menu {
            display: flex;
            flex-direction: column;
        }

        .menu a,
        .logout-button {
            display: flex;
            align-items: center;
            gap: 11px;

            width: 100%;

            padding: 11px 12px;
            margin-bottom: 4px;

            border-radius: 8px;

            color: #cbd5e1;
            background: transparent;

            border: none;

            font-size: 14px;
            font-family: inherit;

            cursor: pointer;
            text-align: left;

            transition:
                background .15s,
                color .15s;
        }

        .menu a svg,
        .logout-button svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .menu a:hover,
        .logout-button:hover {
            background: #1e293b;
            color: #fff;
        }

        .menu a.active {
            background: var(--accent);
            color: #fff;
            font-weight: 600;
        }

        .logout-form {
            margin-top: auto;
            padding-top: 24px;
        }

        /* =========================================================
           MAIN
        ========================================================= */

        .main {
            flex: 1;
            min-width: 0;

            margin-left: 256px;

            padding: 30px 34px;
        }

        /* =========================================================
           TOPBAR
        ========================================================= */

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 16px;

            margin-bottom: 26px;

            flex-wrap: wrap;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;

            flex-wrap: wrap;
        }

        .page-title {
            margin: 0;

            font-size: 26px;
            font-weight: 700;
        }

        .page-description {
            margin-top: 5px;

            color: var(--muted);
            font-size: 14px;
        }

        .role-badge {
            background: #dbeafe;
            color: #1e40af;

            padding: 7px 14px;

            border-radius: 999px;

            font-size: 12px;
            font-weight: 700;

            letter-spacing: .05em;
        }

        /* =========================================================
           BUTTONS
        ========================================================= */

        .btn {
            border: none;
            border-radius: 8px;

            padding: 10px 16px;

            cursor: pointer;

            font-size: 14px;
            font-weight: 600;

            display: inline-block;

            transition: background .15s;

            text-align: center;
        }

        .btn-secondary {
            background: #334155;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #1e293b;
        }

        /* =========================================================
           SUMMARY CARDS
        ========================================================= */

        .cards {
            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 18px;

            margin-bottom: 22px;
        }

        .card {
            display: block;

            background: #fff;

            border: 1px solid var(--line);

            border-radius: 14px;

            padding: 20px;

            position: relative;

            overflow: hidden;

            cursor: pointer;

            transition:
                transform .15s,
                box-shadow .15s,
                border-color .15s;
        }

        .card::before {
            content: "";

            position: absolute;

            left: 0;
            top: 0;
            bottom: 0;

            width: 4px;

            background: var(--card-color, var(--accent));
        }

        .card:hover {
            transform: translateY(-3px);

            box-shadow:
                0 10px 24px rgba(15, 23, 42, .09);

            border-color: #cbd5e1;
        }

        .card-label {
            font-size: 13px;
            color: var(--muted);

            margin-bottom: 10px;
        }

        .card-value {
            font-size: 30px;
            font-weight: 700;
        }

        .card-link {
            margin-top: 10px;

            font-size: 12px;
            font-weight: 600;

            color: var(--card-color, var(--accent));
        }

        /* =========================================================
           TABS
        ========================================================= */

        .tabs {
            display: flex;

            gap: 6px;

            margin-bottom: 18px;

            flex-wrap: wrap;

            background: #fff;

            border: 1px solid var(--line);

            border-radius: 12px;

            padding: 5px;

            width: fit-content;

            max-width: 100%;
        }

        .tab-button {
            border: none;

            background: transparent;

            color: var(--muted);

            padding: 10px 18px;

            border-radius: 8px;

            cursor: pointer;

            font-size: 14px;
            font-weight: 600;

            transition:
                background .15s,
                color .15s;
        }

        .tab-button:hover {
            background: #f1f5f9;
            color: var(--ink);
        }

        .tab-button.active {
            background: var(--accent);
            color: #fff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* =========================================================
           TABLE PANEL
        ========================================================= */

        .table-panel {
            background: #fff;

            border: 1px solid var(--line);

            border-radius: 14px;

            overflow: hidden;

            margin-bottom: 22px;
        }

        .table-header {
            padding: 16px 22px;

            border-bottom: 1px solid var(--line);

            display: flex;

            justify-content: space-between;
            align-items: center;

            gap: 12px;

            flex-wrap: wrap;
        }

        .table-header h2 {
            margin: 0;

            font-size: 16px;
        }

        .table-header p {
            margin: 4px 0 0;

            font-size: 12px;

            color: var(--muted);
        }

        .table-count {
            font-size: 13px;
            color: var(--muted);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 14px;

            border-bottom: 1px solid var(--line);

            text-align: left;

            font-size: 13px;

            vertical-align: middle;

            white-space: nowrap;
        }

        th {
            color: var(--muted);

            font-weight: 600;

            background: #f8fafc;
        }

        tbody tr:hover td {
            background: #fafbfd;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .empty-state {
            color: var(--muted);

            font-size: 14px;

            padding: 50px 20px;

            text-align: center;
        }

        .empty-icon {
            width: 44px;
            height: 44px;

            margin: 0 auto 12px;

            border-radius: 12px;

            background: #f1f5f9;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;

            color: #64748b;
        }

        .empty-title {
            color: #334155;

            font-weight: 700;

            margin-bottom: 5px;
        }

        .secondary-text {
            color: var(--muted);

            font-size: 12px;

            margin-top: 3px;
        }

        /* =========================================================
           BADGES
        ========================================================= */

        .status-badge {
            display: inline-block;

            padding: 4px 10px;

            border-radius: 999px;

            font-size: 11px;

            font-weight: 700;

            white-space: nowrap;
        }

        .status-archived {
            background: #e5e7eb;
            color: #374151;
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 900px) {

            .cards {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 800px) {

            .layout {
                flex-direction: column;
            }

            .sidebar {
                position: relative;

                width: 100%;

                min-height: auto;
            }

            .main {
                margin-left: 0;

                padding: 20px;
            }

            .topbar {
                flex-direction: column;

                align-items: flex-start;
            }

        }

        @media (max-width: 560px) {

            .cards {
                grid-template-columns: 1fr;
            }

            .tabs {
                width: 100%;
            }

            .tab-button {
                flex: 1;
            }

        }

        /* =========================================================
           PRINT
        ========================================================= */

        @media print {

            .sidebar,
            .topbar-actions,
            .tabs,
            .cards {
                display: none !important;
            }

            .main {
                margin-left: 0;

                padding: 0;
            }

            body {
                background: #fff;
            }

            .table-panel {
                border: 1px solid #ccc;

                box-shadow: none;

                break-inside: avoid;
            }

            .tab-content {
                display: block !important;
            }

            .table-wrapper {
                overflow: visible;
            }

            table {
                min-width: 0 !important;
            }

            th,
            td {
                font-size: 11px;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}

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

            {{-- Dashboard --}}
            <a href="{{ route('owner.dashboard') }}">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                    <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                    <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                </svg>

                Dashboard

            </a>


            {{-- Sales & Inventory --}}
            <a href="{{ route('owner.sales-inventory') }}">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M21 8l-9-5-9 5v8l9 5 9-5z"/>
                    <path d="M3 8l9 5 9-5"/>
                    <path d="M12 13v8"/>
                </svg>

                Sales & Inventory

            </a>


            {{-- Suppliers --}}
            <a href="{{ route('owner.suppliers') }}">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="2" y="7" width="12" height="9" rx="1"/>
                    <path d="M14 10h4l3 3v3h-7"/>
                    <circle cx="7" cy="18" r="1.8"/>
                    <circle cx="17" cy="18" r="1.8"/>
                </svg>

                Suppliers

            </a>


            {{-- Purchase Orders --}}
            <a href="{{ route('owner.purchase-orders') }}">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M3 4h2l2.5 10h10L20 7H7"/>
                    <circle cx="9" cy="19" r="1.5"/>
                    <circle cx="17" cy="19" r="1.5"/>
                </svg>

                Purchase Orders

            </a>


            {{-- Reports --}}
            <a href="{{ route('owner.reports') }}">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 20V10"/>
                    <path d="M10 20V4"/>
                    <path d="M16 20v-8"/>
                    <path d="M22 20H2"/>
                </svg>

                Reports

            </a>


            {{-- Archive --}}
            <a
                href="{{ route('owner.archive') }}"
                class="active"
            >

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="3" y="4" width="18" height="5" rx="1"/>
                    <path d="M5 9v10h14V9"/>
                    <path d="M10 13h4"/>
                </svg>

                Archive

            </a>

        </nav>


        {{-- Logout --}}
        <form
            action="{{ route('logout') }}"
            method="POST"
            class="logout-form"
        >

            @csrf

            <button
                type="submit"
                class="logout-button"
            >

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M9 4H5v16h4"/>
                    <path d="M16 8l4 4-4 4"/>
                    <path d="M20 12H9"/>
                </svg>

                Logout

            </button>

        </form>

    </aside>


    {{-- =========================================================
         MAIN CONTENT
    ========================================================== --}}

    <main class="main">

        {{-- =====================================================
             TOPBAR
        ====================================================== --}}

        <div class="topbar">

            <div>

                <h1 class="page-title">
                    Archive
                </h1>

                <div class="page-description">
                    Archived products and suppliers that are no longer active.
                </div>

            </div>


            <div class="topbar-actions">

                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="window.print()"
                >
                    Print / Save PDF
                </button>

                <div class="role-badge">
                    OWNER
                </div>

            </div>

        </div>


        {{-- =====================================================
             SUMMARY CARDS
        ====================================================== --}}

        <div class="cards">

            {{-- Archived Products --}}
            <div
                class="card"
                style="--card-color:#2563eb"
                role="button"
                tabindex="0"
                onclick="showArchiveTab('products-tab')"
                onkeydown="handleCardKey(event, 'products-tab')"
            >

                <div class="card-label">
                    Archived Products
                </div>

                <div class="card-value">
                    {{ number_format($archivedProductCount) }}
                </div>

                <div class="card-link">
                    View archived products →
                </div>

            </div>


            {{-- Archived Suppliers --}}
            <div
                class="card"
                style="--card-color:#d97706"
                role="button"
                tabindex="0"
                onclick="showArchiveTab('suppliers-tab')"
                onkeydown="handleCardKey(event, 'suppliers-tab')"
            >

                <div class="card-label">
                    Archived Suppliers
                </div>

                <div class="card-value">
                    {{ number_format($archivedSupplierCount) }}
                </div>

                <div class="card-link">
                    View archived suppliers →
                </div>

            </div>

        </div>


        {{-- =====================================================
             TABS
        ====================================================== --}}

        <div class="tabs">

            <button
                type="button"
                class="tab-button active"
                data-tab="products-tab"
            >
                Archived Products
            </button>

            <button
                type="button"
                class="tab-button"
                data-tab="suppliers-tab"
            >
                Archived Suppliers
            </button>

        </div>


        {{-- =====================================================
             ARCHIVED PRODUCTS
        ====================================================== --}}

        <section
            id="products-tab"
            class="tab-content active"
        >

            <div class="table-panel">

                <div class="table-header">

                    <div>

                        <h2>
                            Archived Products
                        </h2>

                        <p>
                            Products that are no longer active in the system.
                        </p>

                    </div>

                    <div class="table-count">
                        {{ number_format($archivedProductCount) }} archived
                    </div>

                </div>


                <div class="table-wrapper">

                    @if ($archivedProducts->count() > 0)

                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        Product Name
                                    </th>

                                    <th>
                                        Category
                                    </th>

                                    <th>
                                        API
                                    </th>

                                    <th>
                                        Base Oil
                                    </th>

                                    <th>
                                        Package Size
                                    </th>

                                    <th>
                                        Unit
                                    </th>

                                    <th>
                                        Unit Price
                                    </th>

                                    <th>
                                        Reorder Level
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach ($archivedProducts as $product)

                                    <tr>

                                        <td>

                                            <strong>
                                                {{ $product->product_name }}
                                            </strong>

                                        </td>


                                        <td>

                                            {{ $product->category?->name
                                                ?? $product->category?->category_name
                                                ?? '—' }}

                                        </td>


                                        <td>
                                            {{ $product->api ?? '—' }}
                                        </td>


                                        <td>
                                            {{ $product->base_oil ?? '—' }}
                                        </td>


                                        <td>
                                            {{ $product->package_size ?? '—' }}
                                        </td>


                                        <td>
                                            {{ $product->unit ?? '—' }}
                                        </td>


                                        <td>

                                            ₱{{ number_format(
                                                (float) ($product->unit_price ?? 0),
                                                2
                                            ) }}

                                        </td>


                                        <td>

                                            {{ number_format(
                                                (int) ($product->reorder_level ?? 0)
                                            ) }}

                                        </td>


                                        <td>

                                            <span class="status-badge status-archived">
                                                Archived
                                            </span>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    @else

                        <div class="empty-state">

                            <div class="empty-icon">
                                ▦
                            </div>

                            <div class="empty-title">
                                No archived products
                            </div>

                            There are currently no archived products.

                        </div>

                    @endif

                </div>

            </div>

        </section>


        {{-- =====================================================
             ARCHIVED SUPPLIERS
        ====================================================== --}}

        <section
            id="suppliers-tab"
            class="tab-content"
        >

            <div class="table-panel">

                <div class="table-header">

                    <div>

                        <h2>
                            Archived Suppliers
                        </h2>

                        <p>
                            Suppliers that are no longer active in the system.
                        </p>

                    </div>

                    <div class="table-count">
                        {{ number_format($archivedSupplierCount) }} archived
                    </div>

                </div>


                <div class="table-wrapper">

                    @if ($archivedSuppliers->count() > 0)

                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        Supplier Name
                                    </th>

                                    <th>
                                        Contact Person
                                    </th>

                                    <th>
                                        Contact Number
                                    </th>

                                    <th>
                                        Email
                                    </th>

                                    <th>
                                        Address
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach ($archivedSuppliers as $supplier)

                                    <tr>

                                        <td>

                                            <strong>
                                                {{ $supplier->supplier_name ?? '—' }}
                                            </strong>

                                        </td>


                                        <td>
                                            {{ $supplier->contact_person ?? '—' }}
                                        </td>


                                        <td>
                                            {{ $supplier->contact_number
                                                ?? $supplier->phone
                                                ?? '—' }}
                                        </td>


                                        <td>
                                            {{ $supplier->email ?? '—' }}
                                        </td>


                                        <td>
                                            {{ $supplier->address ?? '—' }}
                                        </td>


                                        <td>

                                            <span class="status-badge status-archived">
                                                Archived
                                            </span>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    @else

                        <div class="empty-state">

                            <div class="empty-icon">
                                ▤
                            </div>

                            <div class="empty-title">
                                No archived suppliers
                            </div>

                            There are currently no archived suppliers.

                        </div>

                    @endif

                </div>

            </div>

        </section>

    </main>

</div>


<script>

    /* =========================================================
       ARCHIVE TABS
    ========================================================== */

    const tabButtons = document.querySelectorAll('.tab-button');

    const tabContents = document.querySelectorAll('.tab-content');


    function showArchiveTab(targetTab) {

        tabButtons.forEach(function (button) {

            button.classList.toggle(
                'active',
                button.dataset.tab === targetTab
            );

        });


        tabContents.forEach(function (content) {

            content.classList.toggle(
                'active',
                content.id === targetTab
            );

        });

    }


    tabButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            showArchiveTab(this.dataset.tab);

        });

    });


    /* =========================================================
       SUMMARY CARD KEYBOARD SUPPORT
    ========================================================== */

    function handleCardKey(event, targetTab) {

        if (
            event.key === 'Enter' ||
            event.key === ' '
        ) {

            event.preventDefault();

            showArchiveTab(targetTab);

        }

    }

</script>

</body>

</html>