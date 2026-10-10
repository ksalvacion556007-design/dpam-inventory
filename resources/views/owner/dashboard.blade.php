<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Owner Dashboard - DPAM IMS</title>

    <style>

        :root {
            --bg: #f1f5f9;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;

            --sidebar: #0b1220;

            --accent: #2563eb;
            --accent-dark: #1d4ed8;

            /* Softer dashboard blue */
            --welcome-blue: #315f9f;
            --welcome-blue-dark: #294f86;

            --danger: #dc2626;
            --danger-bg: #fee2e2;

            --success: #059669;
            --success-bg: #dcfce7;

            --warning: #d97706;
            --warning-bg: #fef3c7;

            --purple: #7c3aed;
            --purple-bg: #ede9fe;

            --cyan: #0891b2;
            --cyan-bg: #cffafe;
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
                Roboto,
                Arial,
                sans-serif;

            background: var(--bg);
            color: var(--ink);
        }


        body.modal-open {
            overflow: hidden;
        }


        a {
            color: inherit;
            text-decoration: none;
        }


        button,
        input,
        select {
            font-family: inherit;
        }


        button {
            border: 0;
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

            z-index: 100;
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


        .topbar {
            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 16px;

            margin-bottom: 24px;

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
           WELCOME PANEL
        ========================================================= */

        .welcome-panel {
            background:
                linear-gradient(
                    135deg,
                    var(--welcome-blue-dark),
                    var(--welcome-blue)
                );

            color: #fff;

            border-radius: 14px;

            padding: 22px 24px;

            margin-bottom: 22px;

            box-shadow:
                0 7px 20px rgba(41, 79, 134, .12);
        }


        .welcome-panel h2 {
            margin: 0;

            font-size: 20px;

            font-weight: 700;
        }


        .welcome-panel p {
            margin: 6px 0 0;

            font-size: 13px;

            color: rgba(255, 255, 255, .88);

            line-height: 1.5;
        }


        /* =========================================================
           ATTENTION BANNER
        ========================================================= */

        .attention-banner {
            background: #fff;

            border: 1px solid var(--line);

            border-radius: 14px;

            padding: 18px 20px;

            margin-bottom: 22px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            flex-wrap: wrap;
        }


        .attention-left {
            display: flex;

            align-items: center;

            gap: 14px;
        }


        .attention-icon {
            width: 42px;
            height: 42px;

            border-radius: 10px;

            background: var(--warning-bg);

            color: var(--warning);

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

            font-weight: 700;

            flex-shrink: 0;
        }


        .attention-title {
            font-size: 14px;

            font-weight: 700;

            margin-bottom: 3px;
        }


        .attention-text {
            color: var(--muted);

            font-size: 12px;
        }


        /* =========================================================
           SUMMARY CARDS
        ========================================================= */

        .summary-grid {
            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 14px;

            margin-bottom: 24px;
        }


        .summary-card {
            width: 100%;

            background: #fff;

            border: 1px solid var(--line);

            border-radius: 14px;

            padding: 18px;

            position: relative;

            overflow: hidden;

            cursor: pointer;

            text-align: left;

            color: var(--ink);

            transition:
                transform .15s,
                box-shadow .15s,
                border-color .15s;
        }


        .summary-card:hover {
            transform: translateY(-2px);

            border-color: #bfdbfe;

            box-shadow:
                0 8px 20px rgba(15, 23, 42, .07);
        }


        .summary-card:focus {
            outline: 3px solid rgba(37, 99, 235, .18);

            outline-offset: 2px;
        }


        .summary-card::before {
            content: "";

            position: absolute;

            left: 0;
            top: 0;
            bottom: 0;

            width: 4px;

            background: var(--card-color);
        }


        .summary-top {
            display: flex;

            align-items: center;

            justify-content: space-between;
        }


        .summary-label {
            color: var(--muted);

            font-size: 12px;

            font-weight: 600;
        }


        .summary-icon {
            width: 36px;
            height: 36px;

            border-radius: 9px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 15px;

            font-weight: 700;
        }


        .summary-icon.blue {
            background: #dbeafe;
            color: #1e40af;
        }


        .summary-icon.green {
            background: var(--success-bg);
            color: #166534;
        }


        .summary-icon.orange {
            background: var(--warning-bg);
            color: #92400e;
        }


        .summary-icon.red {
            background: var(--danger-bg);
            color: #991b1b;
        }


        .summary-value {
            font-size: 28px;

            font-weight: 700;

            line-height: 1;

            margin-top: 14px;
        }


        .summary-link {
            display: block;

            margin-top: 10px;

            font-size: 11px;

            color: var(--card-color);

            font-weight: 600;
        }


        /* =========================================================
           SECTION
        ========================================================= */

        .section {
            margin-bottom: 22px;
        }


        .section-heading {
            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            margin-bottom: 12px;

            flex-wrap: wrap;
        }


        .section-heading h2 {
            margin: 0;

            font-size: 17px;
        }


        .section-heading p {
            margin: 3px 0 0;

            color: var(--muted);

            font-size: 12px;
        }


        /* =========================================================
           ACTION CARDS
        ========================================================= */

        .action-grid {
            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 14px;
        }


        .action-card {
            background: #fff;

            border: 1px solid var(--line);

            border-radius: 12px;

            padding: 18px;

            min-height: 150px;

            display: flex;

            flex-direction: column;

            justify-content: space-between;

            transition:
                transform .15s,
                box-shadow .15s;
        }


        .action-card:hover {
            transform: translateY(-2px);

            box-shadow:
                0 8px 20px rgba(15, 23, 42, .07);
        }


        .action-top {
            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 10px;
        }


        .action-icon {
            width: 38px;
            height: 38px;

            border-radius: 9px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 16px;

            font-weight: 700;
        }


        .action-icon.red {
            background: var(--danger-bg);
            color: #991b1b;
        }


        .action-icon.orange {
            background: var(--warning-bg);
            color: #92400e;
        }


        .action-icon.blue {
            background: #dbeafe;
            color: #1e40af;
        }


        .action-count {
            font-size: 24px;

            font-weight: 700;

            margin-top: 12px;
        }


        .action-title {
            font-size: 14px;

            font-weight: 700;

            margin-top: 6px;
        }


        .action-description {
            color: var(--muted);

            font-size: 11px;

            margin-top: 4px;

            line-height: 1.5;
        }


        .action-button {
            margin-top: 15px;

            width: 100%;

            padding: 9px 12px;

            border-radius: 7px;

            background: #f8fafc;

            color: var(--accent);

            font-size: 11px;

            font-weight: 700;

            cursor: pointer;

            transition:
                background .15s,
                color .15s;
        }


        .action-button:hover {
            background: var(--accent);

            color: #fff;
        }


        /* =========================================================
           CLICKABLE PANELS
        ========================================================= */

        .clickable-panel {
            cursor: pointer;

            transition:
                border-color .15s,
                box-shadow .15s,
                transform .15s;
        }


        .clickable-panel:hover {
            border-color: #bfdbfe;

            box-shadow:
                0 8px 20px rgba(15, 23, 42, .07);

            transform: translateY(-1px);
        }


        .clickable-panel:focus {
            outline: 3px solid rgba(37, 99, 235, .15);

            outline-offset: 2px;
        }


        .panel-click-hint {
            color: var(--accent);

            font-size: 10px;

            font-weight: 700;

            white-space: nowrap;
        }


        /* =========================================================
           CONTENT GRID
        ========================================================= */

        .content-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 1fr)
                minmax(0, 1fr);

            gap: 18px;

            margin-bottom: 22px;
        }


        .panel {
            background: #fff;

            border: 1px solid var(--line);

            border-radius: 14px;

            overflow: hidden;
        }


        .panel-header {
            padding: 17px 20px;

            border-bottom: 1px solid var(--line);

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 10px;

            flex-wrap: wrap;
        }


        .panel-header h2 {
            margin: 0;

            font-size: 16px;
        }


        .panel-header p {
            margin: 4px 0 0;

            color: var(--muted);

            font-size: 11px;
        }


        .panel-body {
            padding: 20px;
        }


        /* =========================================================
           STOCK SNAPSHOT
        ========================================================= */

        .stock-snapshot {
            display: flex;

            align-items: center;

            gap: 30px;
        }


        .donut {
            width: 145px;
            height: 145px;

            flex: 0 0 145px;

            border-radius: 50%;

            position: relative;
        }


        .donut::after {
            content: "";

            position: absolute;

            width: 86px;
            height: 86px;

            left: 50%;
            top: 50%;

            transform:
                translate(-50%, -50%);

            border-radius: 50%;

            background: #fff;
        }


        .donut-center {
            position: absolute;

            z-index: 2;

            left: 50%;
            top: 50%;

            transform:
                translate(-50%, -50%);

            text-align: center;
        }


        .donut-center strong {
            display: block;

            font-size: 23px;
        }


        .donut-center span {
            display: block;

            color: var(--muted);

            font-size: 9px;

            margin-top: 2px;
        }


        .stock-legend {
            flex: 1;
        }


        .legend-item {
            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 10px 0;

            border-bottom: 1px solid #f1f5f9;
        }


        .legend-item:last-child {
            border-bottom: none;
        }


        .legend-left {
            display: flex;

            align-items: center;

            gap: 8px;

            color: #475569;

            font-size: 12px;
        }


        .legend-dot {
            width: 9px;
            height: 9px;

            border-radius: 50%;
        }


        .legend-value {
            font-weight: 700;

            font-size: 12px;
        }


        /* =========================================================
           PURCHASE ORDER STATUS
        ========================================================= */

        .po-status-list {
            display: flex;

            flex-direction: column;

            gap: 14px;
        }


        .po-status-row {
            display: grid;

            grid-template-columns:
                115px 1fr 35px;

            align-items: center;

            gap: 10px;
        }


        .po-status-label {
            color: #475569;

            font-size: 11px;

            text-transform: capitalize;
        }


        .po-bar {
            height: 8px;

            background: #f1f5f9;

            border-radius: 99px;

            overflow: hidden;
        }


        .po-bar-fill {
            height: 100%;

            background: var(--accent);

            border-radius: 99px;
        }


        .po-count {
            text-align: right;

            font-size: 11px;

            font-weight: 700;
        }


        /* =========================================================
           EMPTY STATE
        ========================================================= */

        .empty-state {
            padding: 35px 20px;

            text-align: center;

            color: var(--muted);

            font-size: 12px;
        }


        .empty-icon {
            font-size: 27px;

            margin-bottom: 8px;

            opacity: .6;
        }


        .empty-title {
            color: var(--ink);

            font-weight: 700;

            margin-bottom: 4px;
        }


        /* =========================================================
           MODAL
        ========================================================= */

        .modal {
            position: fixed;

            inset: 0;

            z-index: 500;

            background:
                rgba(15, 23, 42, .55);

            display: none;

            align-items: center;

            justify-content: center;

            padding: 20px;
        }


        .modal.show {
            display: flex;
        }


        .modal-box {
            width: min(1000px, 100%);

            max-height: 88vh;

            background: #fff;

            border-radius: 14px;

            box-shadow:
                0 25px 70px rgba(15, 23, 42, .25);

            display: flex;

            flex-direction: column;

            overflow: hidden;

            animation:
                modalIn .16s ease-out;
        }


        @keyframes modalIn {

            from {
                opacity: 0;

                transform:
                    translateY(8px)
                    scale(.99);
            }

            to {
                opacity: 1;

                transform:
                    translateY(0)
                    scale(1);
            }

        }


        .modal-header {
            padding: 18px 20px;

            border-bottom: 1px solid var(--line);

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;
        }


        .modal-title {
            margin: 0;

            font-size: 17px;

            font-weight: 700;
        }


        .modal-description {
            margin: 4px 0 0;

            color: var(--muted);

            font-size: 11px;

            line-height: 1.5;
        }


        .modal-close {
            width: 34px;
            height: 34px;

            border-radius: 8px;

            background: #f1f5f9;

            color: #475569;

            cursor: pointer;

            font-size: 20px;

            display: flex;

            align-items: center;

            justify-content: center;

            flex-shrink: 0;
        }


        .modal-close:hover {
            background: #e2e8f0;
        }


        .modal-body {
            padding: 20px;

            overflow-y: auto;

            min-height: 0;
        }


        .modal-footer {
            padding: 14px 20px;

            border-top: 1px solid var(--line);

            display: flex;

            align-items: center;

            justify-content: flex-end;

            gap: 8px;

            flex-wrap: wrap;
        }


        .modal-button {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 9px 14px;

            border-radius: 8px;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            text-decoration: none;
        }


        .modal-button.primary {
            background: var(--accent);

            color: #fff;
        }


        .modal-button.primary:hover {
            background: var(--accent-dark);
        }


        .modal-button.secondary {
            background: #f1f5f9;

            color: #475569;
        }


        .modal-button.secondary:hover {
            background: #e2e8f0;
        }


        /* =========================================================
           MODAL SUMMARY
        ========================================================= */

        .modal-summary-grid {
            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 12px;

            margin-bottom: 18px;
        }


        .modal-summary {
            border: 1px solid var(--line);

            border-radius: 10px;

            padding: 14px;

            background: #f8fafc;
        }


        .modal-summary-label {
            color: var(--muted);

            font-size: 10px;

            font-weight: 600;

            text-transform: uppercase;

            letter-spacing: .04em;
        }


        .modal-summary-value {
            margin-top: 6px;

            font-size: 22px;

            font-weight: 700;
        }


        /* =========================================================
           MODAL TABLE
        ========================================================= */

        .modal-table-wrapper {
            overflow-x: auto;

            border: 1px solid var(--line);

            border-radius: 10px;
        }


        .modal-table {
            width: 100%;

            border-collapse: collapse;

            min-width: 650px;
        }


        .modal-table th {
            background: #f8fafc;

            color: var(--muted);

            font-size: 10px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .03em;

            padding: 11px 12px;

            text-align: left;

            border-bottom: 1px solid var(--line);
        }


        .modal-table td {
            padding: 12px;

            font-size: 12px;

            border-bottom: 1px solid var(--line);

            vertical-align: middle;
        }


        .modal-table tbody tr:last-child td {
            border-bottom: none;
        }


        .modal-table tbody tr:hover td {
            background: #fafbfd;
        }


        .product-name {
            font-size: 12px;

            font-weight: 700;

            color: var(--ink);
        }


        .product-brand {
            color: var(--muted);

            font-size: 10px;

            margin-top: 2px;
        }


        .stock-number {
            font-weight: 700;
        }


        .stock-number.low {
            color: var(--warning);
        }


        .stock-number.out {
            color: var(--danger);
        }


        /* =========================================================
           PRODUCT LIST
        ========================================================= */

        .product-list {
            display: grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 10px;
        }


        .product-list-item {
            border: 1px solid var(--line);

            border-radius: 10px;

            padding: 13px;

            background: #fff;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 12px;
        }


        .product-list-main {
            min-width: 0;
        }


        .product-list-name {
            font-size: 12px;

            font-weight: 700;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;
        }


        .product-list-meta {
            color: var(--muted);

            font-size: 10px;

            margin-top: 3px;
        }


        .product-stock {
            text-align: right;

            flex-shrink: 0;
        }


        .product-stock-number {
            font-size: 15px;

            font-weight: 700;
        }


        .product-stock-label {
            color: var(--muted);

            font-size: 9px;

            text-transform: uppercase;
        }


        /* =========================================================
           STATUS BADGES
        ========================================================= */

        .status-badge {
            display: inline-flex;

            align-items: center;

            padding: 4px 8px;

            border-radius: 999px;

            font-size: 9px;

            font-weight: 700;

            text-transform: uppercase;

            white-space: nowrap;
        }


        .status-success {
            background: var(--success-bg);

            color: #166534;
        }


        .status-low {
            background: var(--warning-bg);

            color: #92400e;
        }


        .status-out {
            background: var(--danger-bg);

            color: #991b1b;
        }


        .status-blue {
            background: #dbeafe;

            color: #1e40af;
        }


        .status-purple {
            background: var(--purple-bg);

            color: #6d28d9;
        }


        .status-gray {
            background: #e5e7eb;

            color: #374151;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1200px) {

            .summary-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

        }


        @media (max-width: 1000px) {

            .content-grid {
                grid-template-columns: 1fr;
            }


            .product-list {
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

                padding: 18px 14px;
            }


            .logout-form {
                margin-top: 10px;

                padding-top: 10px;
            }


            .main {
                margin-left: 0;

                padding: 20px;
            }


            .action-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }


            .stock-snapshot {
                flex-direction: column;

                align-items: stretch;
            }


            .donut {
                margin: 0 auto;
            }

        }


        @media (max-width: 600px) {

            .summary-grid,
            .action-grid {
                grid-template-columns: 1fr;
            }


            .topbar {
                align-items: flex-start;

                flex-direction: column;
            }


            .attention-banner {
                align-items: flex-start;
            }


            .modal {
                padding: 10px;
            }


            .modal-box {
                max-height: 94vh;

                border-radius: 11px;
            }


            .modal-summary-grid {
                grid-template-columns: 1fr;
            }


            .modal-header,
            .modal-body,
            .modal-footer {
                padding-left: 14px;

                padding-right: 14px;
            }


            .po-status-row {
                grid-template-columns:
                    95px 1fr 30px;
            }

        }


        /* =========================================================
           PRINT
        ========================================================= */

        @media print {

            .sidebar,
            .modal {
                display: none !important;
            }


            .main {
                margin-left: 0;

                padding: 0;
            }


            body {
                background: #fff;
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
            <a href="{{ route('owner.dashboard') }}"
               class="active">

                <svg viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                    <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                    <rect x="3" y="16" width="7" height="5" rx="1.5"/>

                </svg>

                Dashboard

            </a>


            {{-- Sales & Inventory --}}
            <a href="{{ route('owner.sales-inventory') }}">

                <svg viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <path d="M12 3l9 5-9 5-9-5z"/>
                    <path d="M3 13l9 5 9-5"/>

                </svg>

                Sales & Inventory

            </a>


            {{-- Suppliers --}}
            <a href="{{ route('owner.suppliers') }}">

                <svg viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <rect x="2" y="7" width="12" height="9" rx="1"/>
                    <path d="M14 10h4l3 3v3h-7"/>
                    <circle cx="7" cy="18" r="1.8"/>
                    <circle cx="17" cy="18" r="1.8"/>

                </svg>

                Suppliers

            </a>


            {{-- Purchase Orders --}}
            <a href="{{ route('owner.purchase-orders') }}">

                <svg viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <path d="M3 4h2l2.5 10h10L20 7H7"/>
                    <circle cx="9" cy="19" r="1.5"/>
                    <circle cx="17" cy="19" r="1.5"/>

                </svg>

                Purchase Orders

            </a>


            {{-- Reports --}}
            <a href="{{ route('owner.reports') }}">

                <svg viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <path d="M4 20V10"/>
                    <path d="M10 20V4"/>
                    <path d="M16 20v-8"/>
                    <path d="M22 20H2"/>

                </svg>

                Reports

            </a>


            {{-- Archive --}}
            <a href="{{ route('owner.archive') }}">

                <svg viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <rect x="3" y="4" width="18" height="5" rx="1"/>
                    <path d="M5 9v10h14V9"/>
                    <path d="M10 13h4"/>

                </svg>

                Archive

            </a>

        </nav>


        {{-- Logout --}}
        <form action="{{ route('logout') }}"
              method="POST"
              class="logout-form">

            @csrf

            <button type="submit"
                    class="logout-button">

                <svg viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.8"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <path d="M9 4H5v16h4"/>
                    <path d="M16 8l4 4-4 4"/>
                    <path d="M20 12H9"/>

                </svg>

                Logout

            </button>

        </form>

    </aside>



    {{-- =========================================================
         MAIN
    ========================================================== --}}

    <main class="main">


        {{-- TOPBAR --}}
        <div class="topbar">

            <div>

                <h1 class="page-title">
                    Owner Dashboard
                </h1>

                <div class="page-description">

                    Welcome,
                    {{ Auth::user()->name }}

                    — monitor the items that need your attention.

                </div>

            </div>


            <div class="role-badge">
                OWNER
            </div>

        </div>



        {{-- =====================================================
             DASHBOARD VALUES
        ====================================================== --}}

        @php

            $lowItems =
                collect($lowStockItems ?? []);

            $outItems =
                collect($outOfStockItems ?? []);

            $productsCollection =
                collect($products ?? []);

            $poStatuses =
                collect($poStatusCounts ?? []);

            $totalProducts =
                (int) (
                    $totalProducts
                    ?? $productsCollection->count()
                    ?? 0
                );

            $totalUnitsInStock =
                (int) (
                    $totalUnitsInStock
                    ?? $totalInventory
                    ?? 0
                );

            $lowStockCount =
                (int) (
                    $lowStockCount
                    ?? $lowItems->count()
                );

            $outOfStockCount =
                (int) (
                    $outOfStockCount
                    ?? $outItems->count()
                );

            $inStockCount =
                (int) (
                    $inStockCount
                    ?? max(
                        0,
                        $totalProducts
                        - $lowStockCount
                        - $outOfStockCount
                    )
                );

            $openPurchaseOrderCount =
                (int) (
                    $openPurchaseOrderCount
                    ?? $pendingPurchaseOrders
                    ?? 0
                );

            $openPOs =
                collect($openPurchaseOrders ?? []);

            /*
             * Fallback: if controller has not yet supplied
             * open purchase orders, use recent purchase orders
             * only for display compatibility.
             */
            if (
                $openPOs->isEmpty()
                && isset($recentPurchaseOrders)
            ) {
                $openPOs =
                    collect($recentPurchaseOrders);
            }

            $stockProductTotal =
                max(
                    $totalProducts,
                    1
                );

            $inStockPercent =
                ($inStockCount / $stockProductTotal) * 100;

            $lowStockPercent =
                ($lowStockCount / $stockProductTotal) * 100;

            $outOfStockPercent =
                ($outOfStockCount / $stockProductTotal) * 100;

            $lowStockStart =
                $inStockPercent;

            $outOfStockStart =
                $inStockPercent +
                $lowStockPercent;

            $donutStyle =
                "background: conic-gradient(
                    #059669 0% {$lowStockStart}%,
                    #d97706 {$lowStockStart}% {$outOfStockStart}%,
                    #dc2626 {$outOfStockStart}% 100%
                );";

            $attentionCount =
                $lowStockCount
                + $outOfStockCount
                + $openPurchaseOrderCount;

        @endphp



        {{-- =====================================================
             WELCOME PANEL
        ====================================================== --}}

        <section class="welcome-panel">

            <h2>
                Welcome, {{ Auth::user()->name }}.
            </h2>

            <p>
                This dashboard gives you a quick view of current inventory
                conditions and purchasing actions. Detailed history is available
                in Reports.
            </p>

        </section>



        {{-- =====================================================
             ATTENTION BANNER
        ====================================================== --}}

        @if($attentionCount > 0)

            <div class="attention-banner">

                <div class="attention-left">

                    <div class="attention-icon">
                        !
                    </div>

                    <div>

                        <div class="attention-title">

                            {{ number_format($attentionCount) }}
                            action{{ $attentionCount === 1 ? '' : 's' }}
                            may require your attention.

                        </div>

                        <div class="attention-text">

                            Review low stock, out-of-stock products,
                            and open purchase orders.

                        </div>

                    </div>

                </div>

            </div>

        @else

            <div class="attention-banner">

                <div class="attention-left">

                    <div class="attention-icon"
                         style="
                            background:#dcfce7;
                            color:#166534;
                         ">

                        ✓

                    </div>

                    <div>

                        <div class="attention-title">
                            Everything looks good.
                        </div>

                        <div class="attention-text">

                            There are currently no stock or purchase
                            order alerts requiring attention.

                        </div>

                    </div>

                </div>

            </div>

        @endif



        {{-- =====================================================
             SUMMARY CARDS
        ====================================================== --}}

        <section class="summary-grid">


            {{-- ACTIVE PRODUCTS --}}
            <button type="button"
                    class="summary-card"
                    style="--card-color:#2563eb;"
                    data-modal-open="productsModal">

                <div class="summary-top">

                    <span class="summary-label">
                        Active Products
                    </span>

                    <span class="summary-icon blue">
                        ▦
                    </span>

                </div>

                <div class="summary-value">
                    {{ number_format($totalProducts) }}
                </div>

                <span class="summary-link">
                    View product list →
                </span>

            </button>


            {{-- TOTAL UNITS --}}
            <button type="button"
                    class="summary-card"
                    style="--card-color:#059669;"
                    data-modal-open="stockModal">

                <div class="summary-top">

                    <span class="summary-label">
                        Total Units in Stock
                    </span>

                    <span class="summary-icon green">
                        ◈
                    </span>

                </div>

                <div class="summary-value">
                    {{ number_format($totalUnitsInStock) }}
                </div>

                <span class="summary-link">
                    View stock list →
                </span>

            </button>


            {{-- LOW STOCK --}}
            <button type="button"
                    class="summary-card"
                    style="--card-color:#d97706;"
                    data-modal-open="lowStockModal">

                <div class="summary-top">

                    <span class="summary-label">
                        Low Stock
                    </span>

                    <span class="summary-icon orange">
                        !
                    </span>

                </div>

                <div class="summary-value">
                    {{ number_format($lowStockCount) }}
                </div>

                <span class="summary-link">
                    View low-stock list →
                </span>

            </button>


            {{-- OUT OF STOCK --}}
            <button type="button"
                    class="summary-card"
                    style="--card-color:#dc2626;"
                    data-modal-open="outOfStockModal">

                <div class="summary-top">

                    <span class="summary-label">
                        Out of Stock
                    </span>

                    <span class="summary-icon red">
                        ×
                    </span>

                </div>

                <div class="summary-value">
                    {{ number_format($outOfStockCount) }}
                </div>

                <span class="summary-link">
                    View out-of-stock list →
                </span>

            </button>

        </section>



        {{-- =====================================================
             ACTION REQUIRED
        ====================================================== --}}

        <section class="section">

            <div class="section-heading">

                <div>

                    <h2>
                        Action Required
                    </h2>

                    <p>
                        Focus on the items that may need an Owner decision.
                    </p>

                </div>

            </div>


            <div class="action-grid">


                {{-- LOW STOCK --}}
                <div class="action-card">

                    <div>

                        <div class="action-top">

                            <div class="action-icon orange">
                                !
                            </div>

                        </div>

                        <div class="action-count"
                             style="color:var(--warning);">

                            {{ number_format($lowStockCount) }}

                        </div>

                        <div class="action-title">
                            Low Stock
                        </div>

                        <div class="action-description">

                            Products at or below their
                            reorder level.

                        </div>

                    </div>


                    <button type="button"
                            class="action-button"
                            data-modal-open="lowStockModal">

                        Review Low Stock

                    </button>

                </div>



                {{-- OUT OF STOCK --}}
                <div class="action-card">

                    <div>

                        <div class="action-top">

                            <div class="action-icon red">
                                ×
                            </div>

                        </div>

                        <div class="action-count"
                             style="color:var(--danger);">

                            {{ number_format($outOfStockCount) }}

                        </div>

                        <div class="action-title">
                            Out of Stock
                        </div>

                        <div class="action-description">

                            Products with zero
                            current stock.

                        </div>

                    </div>


                    <button type="button"
                            class="action-button"
                            style="color:var(--danger);"
                            data-modal-open="outOfStockModal">

                        Review Out of Stock

                    </button>

                </div>



                {{-- PURCHASE ORDERS --}}
                <div class="action-card">

                    <div>

                        <div class="action-top">

                            <div class="action-icon blue">
                                PO
                            </div>

                        </div>

                        <div class="action-count"
                             style="color:var(--accent);">

                            {{ number_format($openPurchaseOrderCount) }}

                        </div>

                        <div class="action-title">
                            Open Purchase Orders
                        </div>

                        <div class="action-description">

                            Purchase orders that are
                            still being processed.

                        </div>

                    </div>


                    <button type="button"
                            class="action-button"
                            data-modal-open="purchaseOrdersModal">

                        Review Purchase Orders

                    </button>

                </div>

            </div>

        </section>



        {{-- =====================================================
             CLICKABLE SNAPSHOTS
        ====================================================== --}}

        <div class="content-grid">


            {{-- =================================================
                 INVENTORY SNAPSHOT
            ================================================== --}}

            <section class="panel clickable-panel"
                     role="button"
                     tabindex="0"
                     data-modal-open="inventorySnapshotModal"
                     aria-label="Open Inventory Snapshot details">

                <div class="panel-header">

                    <div>

                        <h2>
                            Inventory Snapshot
                        </h2>

                        <p>
                            Current status of active products.
                        </p>

                    </div>

                    <span class="panel-click-hint">
                        View lists →
                    </span>

                </div>


                <div class="panel-body">

                    <div class="stock-snapshot">


                        <div class="donut"
                             style="{{ $donutStyle }}">

                            <div class="donut-center">

                                <strong>
                                    {{ number_format($totalProducts) }}
                                </strong>

                                <span>
                                    PRODUCTS
                                </span>

                            </div>

                        </div>


                        <div class="stock-legend">


                            <div class="legend-item">

                                <div class="legend-left">

                                    <span class="legend-dot"
                                          style="background:#059669;">
                                    </span>

                                    In Stock

                                </div>

                                <span class="legend-value">
                                    {{ number_format($inStockCount) }}
                                </span>

                            </div>


                            <div class="legend-item">

                                <div class="legend-left">

                                    <span class="legend-dot"
                                          style="background:#d97706;">
                                    </span>

                                    Low Stock

                                </div>

                                <span class="legend-value">
                                    {{ number_format($lowStockCount) }}
                                </span>

                            </div>


                            <div class="legend-item">

                                <div class="legend-left">

                                    <span class="legend-dot"
                                          style="background:#dc2626;">
                                    </span>

                                    Out of Stock

                                </div>

                                <span class="legend-value">
                                    {{ number_format($outOfStockCount) }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </section>



            {{-- =================================================
                 PURCHASE ORDER STATUS
            ================================================== --}}

            <section class="panel clickable-panel"
                     role="button"
                     tabindex="0"
                     data-modal-open="purchaseOrderStatusModal"
                     aria-label="Open Purchase Order Status details">

                <div class="panel-header">

                    <div>

                        <h2>
                            Purchase Order Status
                        </h2>

                        <p>
                            Current purchase order distribution.
                        </p>

                    </div>

                    <span class="panel-click-hint">
                        View lists →
                    </span>

                </div>


                <div class="panel-body">

                    @php

                        $poTotal =
                            max(
                                $poStatuses->sum(),
                                1
                            );

                    @endphp


                    @if($poStatuses->count())

                        <div class="po-status-list">

                            @foreach($poStatuses as $status => $count)

                                @php

                                    $percentage =
                                        ($count / $poTotal) * 100;

                                @endphp

                                <div class="po-status-row">

                                    <span class="po-status-label">

                                        {{ str_replace(
                                            '_',
                                            ' ',
                                            $status
                                        ) }}

                                    </span>


                                    <div class="po-bar">

                                        <div class="po-bar-fill"
                                             style="
                                                width:
                                                {{ $percentage }}%;
                                             ">
                                        </div>

                                    </div>


                                    <span class="po-count">
                                        {{ $count }}
                                    </span>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="empty-state">

                            <div class="empty-icon">
                                —
                            </div>

                            <div class="empty-title">
                                No purchase orders
                            </div>

                            There are currently no purchase orders.

                        </div>

                    @endif

                </div>

            </section>

        </div>


    </main>

</div>



{{-- =============================================================
     ACTIVE PRODUCTS MODAL
============================================================= --}}

<div class="modal"
     id="productsModal"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="productsModalTitle">

    <div class="modal-box">

        <div class="modal-header">

            <div>

                <h2 class="modal-title"
                    id="productsModalTitle">

                    Active Products

                </h2>

                <p class="modal-description">

                    All active products currently registered in DPAM IMS.

                </p>

            </div>


            <button type="button"
                    class="modal-close"
                    data-modal-close
                    aria-label="Close modal">

                ×

            </button>

        </div>


        <div class="modal-body">

            <div class="modal-summary-grid">

                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Active Products
                    </div>

                    <div class="modal-summary-value">
                        {{ number_format($totalProducts) }}
                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        In Stock
                    </div>

                    <div class="modal-summary-value"
                         style="color:var(--success);">

                        {{ number_format($inStockCount) }}

                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Stock Alerts
                    </div>

                    <div class="modal-summary-value"
                         style="color:var(--warning);">

                        {{ number_format(
                            $lowStockCount + $outOfStockCount
                        ) }}

                    </div>

                </div>

            </div>


            @if($productsCollection->count())

                <div class="product-list">

                    @foreach($productsCollection as $product)

                        @php

                            $productStock =
                                (int) (
                                    $product->current_stock
                                    ?? $product->inventory?->current_stock
                                    ?? 0
                                );

                            $productReorder =
                                (int) (
                                    $product->reorder_level
                                    ?? 0
                                );

                            if ($productStock <= 0) {

                                $productStatus =
                                    'OUT OF STOCK';

                                $productStatusClass =
                                    'status-out';

                                $productStockClass =
                                    'out';

                            } elseif (
                                $productStock <= $productReorder
                            ) {

                                $productStatus =
                                    'LOW STOCK';

                                $productStatusClass =
                                    'status-low';

                                $productStockClass =
                                    'low';

                            } else {

                                $productStatus =
                                    'IN STOCK';

                                $productStatusClass =
                                    'status-success';

                                $productStockClass =
                                    '';

                            }

                        @endphp


                        <div class="product-list-item">

                            <div class="product-list-main">

                                <div class="product-list-name">

                                    {{ $product->product_name }}

                                </div>

                                <div class="product-list-meta">

                                    {{ $product->brand ?? 'No brand' }}

                                    ·

                                    Reorder:
                                    {{ number_format($productReorder) }}

                                    {{ $product->unit ?? '' }}

                                </div>

                            </div>


                            <div class="product-stock">

                                <div class="product-stock-number {{ $productStockClass }}">

                                    {{ number_format($productStock) }}

                                </div>

                                <div class="product-stock-label">

                                    {{ $product->unit ?? 'units' }}

                                </div>

                            </div>


                            <span class="status-badge {{ $productStatusClass }}">

                                {{ $productStatus }}

                            </span>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="empty-state">

                    <div class="empty-icon">
                        ▦
                    </div>

                    <div class="empty-title">
                        No product list available
                    </div>

                    Product details can be viewed in Sales & Inventory.

                </div>

            @endif

        </div>


        <div class="modal-footer">

            <button type="button"
                    class="modal-button secondary"
                    data-modal-close>

                Close

            </button>


            <a href="{{ route('owner.sales-inventory') }}"
               class="modal-button primary">

                Open Sales & Inventory

            </a>

        </div>

    </div>

</div>



{{-- =============================================================
     TOTAL STOCK MODAL
============================================================= --}}

<div class="modal"
     id="stockModal"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="stockModalTitle">

    <div class="modal-box">

        <div class="modal-header">

            <div>

                <h2 class="modal-title"
                    id="stockModalTitle">

                    Total Units in Stock

                </h2>

                <p class="modal-description">

                    Current stock quantities for active products.

                </p>

            </div>


            <button type="button"
                    class="modal-close"
                    data-modal-close
                    aria-label="Close modal">

                ×

            </button>

        </div>


        <div class="modal-body">

            <div class="modal-summary-grid">

                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Total Units
                    </div>

                    <div class="modal-summary-value">
                        {{ number_format($totalUnitsInStock) }}
                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Active Products
                    </div>

                    <div class="modal-summary-value">
                        {{ number_format($totalProducts) }}
                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Products Needing Attention
                    </div>

                    <div class="modal-summary-value"
                         style="color:var(--danger);">

                        {{ number_format(
                            $lowStockCount + $outOfStockCount
                        ) }}

                    </div>

                </div>

            </div>


            @if($productsCollection->count())

                <div class="modal-table-wrapper">

                    <table class="modal-table">

                        <thead>

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Brand
                                </th>

                                <th>
                                    Current Stock
                                </th>

                                <th>
                                    Reorder Level
                                </th>

                                <th>
                                    Unit
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($productsCollection as $product)

                                @php

                                    $productStock =
                                        (int) (
                                            $product->current_stock
                                            ?? $product->inventory?->current_stock
                                            ?? 0
                                        );

                                    $productReorder =
                                        (int) (
                                            $product->reorder_level
                                            ?? 0
                                        );

                                    if ($productStock <= 0) {

                                        $statusText =
                                            'OUT OF STOCK';

                                        $statusClass =
                                            'status-out';

                                        $numberClass =
                                            'out';

                                    } elseif (
                                        $productStock <= $productReorder
                                    ) {

                                        $statusText =
                                            'LOW STOCK';

                                        $statusClass =
                                            'status-low';

                                        $numberClass =
                                            'low';

                                    } else {

                                        $statusText =
                                            'IN STOCK';

                                        $statusClass =
                                            'status-success';

                                        $numberClass =
                                            '';

                                    }

                                @endphp


                                <tr>

                                    <td>

                                        <div class="product-name">
                                            {{ $product->product_name }}
                                        </div>

                                    </td>


                                    <td>
                                        {{ $product->brand ?? '—' }}
                                    </td>


                                    <td>

                                        <span class="stock-number {{ $numberClass }}">

                                            {{ number_format($productStock) }}

                                        </span>

                                    </td>


                                    <td>
                                        {{ number_format($productReorder) }}
                                    </td>


                                    <td>
                                        {{ $product->unit ?? '—' }}
                                    </td>


                                    <td>

                                        <span class="status-badge {{ $statusClass }}">

                                            {{ $statusText }}

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <div class="empty-title">
                        No product stock list available
                    </div>

                </div>

            @endif

        </div>


        <div class="modal-footer">

            <button type="button"
                    class="modal-button secondary"
                    data-modal-close>

                Close

            </button>


            <a href="{{ route('owner.sales-inventory') }}"
               class="modal-button primary">

                Open Sales & Inventory

            </a>

        </div>

    </div>

</div>



{{-- =============================================================
     LOW STOCK MODAL
============================================================= --}}

<div class="modal"
     id="lowStockModal"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="lowStockModalTitle">

    <div class="modal-box">

        <div class="modal-header">

            <div>

                <h2 class="modal-title"
                    id="lowStockModalTitle">

                    Low Stock Items

                </h2>

                <p class="modal-description">

                    Products at or below their configured reorder level.

                </p>

            </div>


            <button type="button"
                    class="modal-close"
                    data-modal-close
                    aria-label="Close modal">

                ×

            </button>

        </div>


        <div class="modal-body">

            <div class="modal-summary-grid">

                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Low Stock Products
                    </div>

                    <div class="modal-summary-value"
                         style="color:var(--warning);">

                        {{ number_format($lowStockCount) }}

                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Action
                    </div>

                    <div class="modal-summary-value"
                         style="font-size:15px;">

                        Review

                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Recommended
                    </div>

                    <div class="modal-summary-value"
                         style="font-size:15px;">

                        Replenish

                    </div>

                </div>

            </div>


            @if($lowItems->count())

                <div class="modal-table-wrapper">

                    <table class="modal-table">

                        <thead>

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Brand
                                </th>

                                <th>
                                    Current Stock
                                </th>

                                <th>
                                    Reorder Level
                                </th>

                                <th>
                                    Unit
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($lowItems as $item)

                                <tr>

                                    <td>

                                        <div class="product-name">
                                            {{ $item->product_name }}
                                        </div>

                                    </td>


                                    <td>
                                        {{ $item->brand ?? '—' }}
                                    </td>


                                    <td>

                                        <span class="stock-number low">

                                            {{ number_format(
                                                (int) (
                                                    $item->current_stock
                                                    ?? $item->inventory?->current_stock
                                                    ?? 0
                                                )
                                            ) }}

                                        </span>

                                    </td>


                                    <td>

                                        {{ number_format(
                                            (int) (
                                                $item->reorder_level
                                                ?? 0
                                            )
                                        ) }}

                                    </td>


                                    <td>
                                        {{ $item->unit ?? '—' }}
                                    </td>


                                    <td>

                                        <span class="status-badge status-low">
                                            LOW STOCK
                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <div class="empty-icon">
                        ✓
                    </div>

                    <div class="empty-title">
                        No low-stock items
                    </div>

                    All active products are currently above their reorder level.

                </div>

            @endif

        </div>


        <div class="modal-footer">

            <button type="button"
                    class="modal-button secondary"
                    data-modal-close>

                Close

            </button>


            <a href="{{ route('owner.purchase-orders') }}"
               class="modal-button primary">

                Open Purchase Orders

            </a>

        </div>

    </div>

</div>



{{-- =============================================================
     OUT OF STOCK MODAL
============================================================= --}}

<div class="modal"
     id="outOfStockModal"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="outOfStockModalTitle">

    <div class="modal-box">

        <div class="modal-header">

            <div>

                <h2 class="modal-title"
                    id="outOfStockModalTitle">

                    Out of Stock

                </h2>

                <p class="modal-description">

                    Products that currently have zero stock.

                </p>

            </div>


            <button type="button"
                    class="modal-close"
                    data-modal-close
                    aria-label="Close modal">

                ×

            </button>

        </div>


        <div class="modal-body">

            <div class="modal-summary-grid">

                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Out of Stock Products
                    </div>

                    <div class="modal-summary-value"
                         style="color:var(--danger);">

                        {{ number_format($outOfStockCount) }}

                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Current Units
                    </div>

                    <div class="modal-summary-value">
                        0
                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Recommended
                    </div>

                    <div class="modal-summary-value"
                         style="font-size:15px;">

                        Replenish

                    </div>

                </div>

            </div>


            @if($outItems->count())

                <div class="modal-table-wrapper">

                    <table class="modal-table">

                        <thead>

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Brand
                                </th>

                                <th>
                                    Reorder Level
                                </th>

                                <th>
                                    Unit
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($outItems as $item)

                                <tr>

                                    <td>

                                        <div class="product-name">
                                            {{ $item->product_name }}
                                        </div>

                                    </td>


                                    <td>
                                        {{ $item->brand ?? '—' }}
                                    </td>


                                    <td>

                                        {{ number_format(
                                            (int) (
                                                $item->reorder_level
                                                ?? 0
                                            )
                                        ) }}

                                    </td>


                                    <td>
                                        {{ $item->unit ?? '—' }}
                                    </td>


                                    <td>

                                        <span class="status-badge status-out">
                                            OUT OF STOCK
                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <div class="empty-icon">
                        ✓
                    </div>

                    <div class="empty-title">
                        No out-of-stock products
                    </div>

                    All active products currently have stock.

                </div>

            @endif

        </div>


        <div class="modal-footer">

            <button type="button"
                    class="modal-button secondary"
                    data-modal-close>

                Close

            </button>


            <a href="{{ route('owner.purchase-orders') }}"
               class="modal-button primary">

                Open Purchase Orders

            </a>

        </div>

    </div>

</div>



{{-- =============================================================
     PURCHASE ORDERS MODAL
============================================================= --}}

<div class="modal"
     id="purchaseOrdersModal"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="purchaseOrdersModalTitle">

    <div class="modal-box">

        <div class="modal-header">

            <div>

                <h2 class="modal-title"
                    id="purchaseOrdersModalTitle">

                    Open Purchase Orders

                </h2>

                <p class="modal-description">

                    Purchase orders that are still being processed
                    or require follow-up.

                </p>

            </div>


            <button type="button"
                    class="modal-close"
                    data-modal-close
                    aria-label="Close modal">

                ×

            </button>

        </div>


        <div class="modal-body">

            <div class="modal-summary-grid">

                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Open Purchase Orders
                    </div>

                    <div class="modal-summary-value"
                         style="color:var(--accent);">

                        {{ number_format($openPurchaseOrderCount) }}

                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Action
                    </div>

                    <div class="modal-summary-value"
                         style="font-size:15px;">

                        Follow Up

                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Module
                    </div>

                    <div class="modal-summary-value"
                         style="font-size:15px;">

                        Purchasing

                    </div>

                </div>

            </div>


            @if($openPOs->count())

                <div class="modal-table-wrapper">

                    <table class="modal-table">

                        <thead>

                            <tr>

                                <th>
                                    Purchase Order
                                </th>

                                <th>
                                    Supplier
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($openPOs as $po)

                                @php

                                    $poStatus =
                                        strtolower(
                                            (string) (
                                                $po->status ?? ''
                                            )
                                        );

                                    $poStatusClass =
                                        match ($poStatus) {

                                            'approved',
                                            'confirmed'
                                                => 'status-blue',

                                            'purchasing',
                                            'partial',
                                            'partially_fulfilled'
                                                => 'status-purple',

                                            'cancelled',
                                            'void'
                                                => 'status-out',

                                            'received',
                                            'delivered',
                                            'completed'
                                                => 'status-success',

                                            default
                                                => 'status-gray',

                                        };

                                @endphp


                                <tr>

                                    <td>

                                        <div class="product-name">

                                            {{ $po->po_number
                                                ?? $po->reference
                                                ?? ('PO-' . $po->id) }}

                                        </div>

                                    </td>


                                    <td>

                                        {{ $po->supplier->supplier_name
                                            ?? '—' }}

                                    </td>


                                    <td>

                                        {{ $po->created_at
                                            ? $po->created_at->format('M d, Y')
                                            : '—' }}

                                    </td>


                                    <td>

                                        <span class="status-badge {{ $poStatusClass }}">

                                            {{ ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $poStatus
                                                )
                                            ) }}

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <div class="empty-icon">
                        ✓
                    </div>

                    <div class="empty-title">
                        No open purchase orders
                    </div>

                    There are currently no purchase orders requiring attention.

                </div>

            @endif

        </div>


        <div class="modal-footer">

            <button type="button"
                    class="modal-button secondary"
                    data-modal-close>

                Close

            </button>


            <a href="{{ route('owner.purchase-orders') }}"
               class="modal-button primary">

                Open Purchase Orders

            </a>

        </div>

    </div>

</div>



{{-- =============================================================
     INVENTORY SNAPSHOT MODAL
============================================================= --}}

<div class="modal"
     id="inventorySnapshotModal"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="inventorySnapshotModalTitle">

    <div class="modal-box">

        <div class="modal-header">

            <div>

                <h2 class="modal-title"
                    id="inventorySnapshotModalTitle">

                    Inventory Snapshot

                </h2>

                <p class="modal-description">

                    Complete current stock-status list for active products.

                </p>

            </div>


            <button type="button"
                    class="modal-close"
                    data-modal-close
                    aria-label="Close modal">

                ×

            </button>

        </div>


        <div class="modal-body">

            <div class="modal-summary-grid">

                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Total Products
                    </div>

                    <div class="modal-summary-value">
                        {{ number_format($totalProducts) }}
                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        In Stock
                    </div>

                    <div class="modal-summary-value"
                         style="color:var(--success);">

                        {{ number_format($inStockCount) }}

                    </div>

                </div>


                <div class="modal-summary">

                    <div class="modal-summary-label">
                        Alerts
                    </div>

                    <div class="modal-summary-value"
                         style="color:var(--danger);">

                        {{ number_format(
                            $lowStockCount + $outOfStockCount
                        ) }}

                    </div>

                </div>

            </div>


            @if($productsCollection->count())

                <div class="modal-table-wrapper">

                    <table class="modal-table">

                        <thead>

                            <tr>

                                <th>
                                    Product
                                </th>

                                <th>
                                    Brand
                                </th>

                                <th>
                                    Current Stock
                                </th>

                                <th>
                                    Reorder Level
                                </th>

                                <th>
                                    Unit
                                </th>

                                <th>
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($productsCollection as $product)

                                @php

                                    $stock =
                                        (int) (
                                            $product->current_stock
                                            ?? $product->inventory?->current_stock
                                            ?? 0
                                        );

                                    $reorder =
                                        (int) (
                                            $product->reorder_level
                                            ?? 0
                                        );

                                    if ($stock <= 0) {

                                        $status =
                                            'OUT OF STOCK';

                                        $statusClass =
                                            'status-out';

                                        $stockClass =
                                            'out';

                                    } elseif ($stock <= $reorder) {

                                        $status =
                                            'LOW STOCK';

                                        $statusClass =
                                            'status-low';

                                        $stockClass =
                                            'low';

                                    } else {

                                        $status =
                                            'IN STOCK';

                                        $statusClass =
                                            'status-success';

                                        $stockClass =
                                            '';

                                    }

                                @endphp


                                <tr>

                                    <td>

                                        <div class="product-name">
                                            {{ $product->product_name }}
                                        </div>

                                    </td>


                                    <td>
                                        {{ $product->brand ?? '—' }}
                                    </td>


                                    <td>

                                        <span class="stock-number {{ $stockClass }}">

                                            {{ number_format($stock) }}

                                        </span>

                                    </td>


                                    <td>
                                        {{ number_format($reorder) }}
                                    </td>


                                    <td>
                                        {{ $product->unit ?? '—' }}
                                    </td>


                                    <td>

                                        <span class="status-badge {{ $statusClass }}">

                                            {{ $status }}

                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty-state">

                    <div class="empty-title">
                        No inventory list available
                    </div>

                </div>

            @endif

        </div>


        <div class="modal-footer">

            <button type="button"
                    class="modal-button secondary"
                    data-modal-close>

                Close

            </button>


            <a href="{{ route('owner.sales-inventory') }}"
               class="modal-button primary">

                Open Sales & Inventory

            </a>

        </div>

    </div>

</div>



{{-- =============================================================
     PURCHASE ORDER STATUS MODAL
============================================================= --}}

<div class="modal"
     id="purchaseOrderStatusModal"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-labelledby="purchaseOrderStatusModalTitle">

    <div class="modal-box">

        <div class="modal-header">

            <div>

                <h2 class="modal-title"
                    id="purchaseOrderStatusModalTitle">

                    Purchase Order Status

                </h2>

                <p class="modal-description">

                    Purchase order status breakdown and available purchase-order records.

                </p>

            </div>


            <button type="button"
                    class="modal-close"
                    data-modal-close
                    aria-label="Close modal">

                ×

            </button>

        </div>


        <div class="modal-body">

            @if($poStatuses->count())

                @php

                    $poTotal =
                        max(
                            $poStatuses->sum(),
                            1
                        );

                @endphp


                <div class="modal-summary-grid">

                    <div class="modal-summary">

                        <div class="modal-summary-label">
                            Total Purchase Orders
                        </div>

                        <div class="modal-summary-value">
                            {{ number_format($poStatuses->sum()) }}
                        </div>

                    </div>


                    <div class="modal-summary">

                        <div class="modal-summary-label">
                            Open
                        </div>

                        <div class="modal-summary-value"
                             style="color:var(--accent);">

                            {{ number_format($openPurchaseOrderCount) }}

                        </div>

                    </div>


                    <div class="modal-summary">

                        <div class="modal-summary-label">
                            Status Types
                        </div>

                        <div class="modal-summary-value">

                            {{ number_format($poStatuses->count()) }}

                        </div>

                    </div>

                </div>


                <div class="modal-table-wrapper">

                    <table class="modal-table">

                        <thead>

                            <tr>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Purchase Orders
                                </th>

                                <th>
                                    Share
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($poStatuses as $status => $count)

                                @php

                                    $percentage =
                                        ($count / $poTotal) * 100;

                                    $normalizedStatus =
                                        strtolower(
                                            (string) $status
                                        );

                                    $statusClass =
                                        match ($normalizedStatus) {

                                            'received',
                                            'delivered',
                                            'completed'
                                                => 'status-success',

                                            'cancelled',
                                            'void'
                                                => 'status-out',

                                            'approved',
                                            'confirmed'
                                                => 'status-blue',

                                            'purchasing',
                                            'partial',
                                            'partially_fulfilled'
                                                => 'status-purple',

                                            default
                                                => 'status-gray',

                                        };

                                @endphp


                                <tr>

                                    <td>

                                        <span class="status-badge {{ $statusClass }}">

                                            {{ ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $status
                                                )
                                            ) }}

                                        </span>

                                    </td>


                                    <td>

                                        <strong>
                                            {{ number_format($count) }}
                                        </strong>

                                    </td>


                                    <td>

                                        {{ number_format(
                                            $percentage,
                                            1
                                        ) }}%

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


            @else

                <div class="empty-state">

                    <div class="empty-icon">
                        —
                    </div>

                    <div class="empty-title">
                        No purchase orders
                    </div>

                    There are currently no purchase order records.

                </div>

            @endif


            @if($openPOs->count())

                <div style="margin-top:20px;">

                    <div style="
                        font-size:13px;
                        font-weight:700;
                        margin-bottom:10px;
                    ">

                        Purchase Order List

                    </div>


                    <div class="modal-table-wrapper">

                        <table class="modal-table">

                            <thead>

                                <tr>

                                    <th>
                                        Purchase Order
                                    </th>

                                    <th>
                                        Supplier
                                    </th>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($openPOs as $po)

                                    @php

                                        $poStatus =
                                            strtolower(
                                                (string) (
                                                    $po->status ?? ''
                                                )
                                            );

                                        $poStatusClass =
                                            match ($poStatus) {

                                                'approved',
                                                'confirmed'
                                                    => 'status-blue',

                                                'purchasing',
                                                'partial',
                                                'partially_fulfilled'
                                                    => 'status-purple',

                                                'cancelled',
                                                'void'
                                                    => 'status-out',

                                                'received',
                                                'delivered',
                                                'completed'
                                                    => 'status-success',

                                                default
                                                    => 'status-gray',

                                            };

                                    @endphp


                                    <tr>

                                        <td>

                                            <div class="product-name">

                                                {{ $po->po_number
                                                    ?? $po->reference
                                                    ?? ('PO-' . $po->id) }}

                                            </div>

                                        </td>


                                        <td>

                                            {{ $po->supplier->supplier_name
                                                ?? '—' }}

                                        </td>


                                        <td>

                                            {{ $po->created_at
                                                ? $po->created_at->format('M d, Y')
                                                : '—' }}

                                        </td>


                                        <td>

                                            <span class="status-badge {{ $poStatusClass }}">

                                                {{ ucfirst(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $poStatus
                                                    )
                                                ) }}

                                            </span>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            @endif

        </div>


        <div class="modal-footer">

            <button type="button"
                    class="modal-button secondary"
                    data-modal-close>

                Close

            </button>


            <a href="{{ route('owner.purchase-orders') }}"
               class="modal-button primary">

                Open Purchase Orders

            </a>

        </div>

    </div>

</div>



{{-- =============================================================
     JAVASCRIPT
============================================================= --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const openButtons =
            document.querySelectorAll(
                '[data-modal-open]'
            );


        const closeButtons =
            document.querySelectorAll(
                '[data-modal-close]'
            );


        const modals =
            document.querySelectorAll(
                '.modal'
            );


        /*
        |--------------------------------------------------------------------------
        | OPEN MODAL
        |--------------------------------------------------------------------------
        */

        function openModal(modalId) {

            const modal =
                document.getElementById(
                    modalId
                );


            if (!modal) {
                return;
            }


            /*
             * Close any other modal first.
             */
            modals.forEach(
                function (item) {

                    item.classList.remove(
                        'show'
                    );

                    item.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                }
            );


            modal.classList.add(
                'show'
            );


            modal.setAttribute(
                'aria-hidden',
                'false'
            );


            document.body.classList.add(
                'modal-open'
            );


            const closeButton =
                modal.querySelector(
                    '[data-modal-close]'
                );


            if (closeButton) {

                setTimeout(
                    function () {

                        closeButton.focus();

                    },
                    50
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE MODAL
        |--------------------------------------------------------------------------
        */

        function closeModal(modal) {

            if (!modal) {
                return;
            }


            modal.classList.remove(
                'show'
            );


            modal.setAttribute(
                'aria-hidden',
                'true'
            );


            const anyOpenModal =
                document.querySelector(
                    '.modal.show'
                );


            if (!anyOpenModal) {

                document.body.classList.remove(
                    'modal-open'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | OPEN BUTTONS
        |--------------------------------------------------------------------------
        */

        openButtons.forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        openModal(
                            button.dataset.modalOpen
                        );

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CLOSE BUTTONS
        |--------------------------------------------------------------------------
        */

        closeButtons.forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        const modal =
                            button.closest(
                                '.modal'
                            );


                        closeModal(
                            modal
                        );

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CLICK OUTSIDE MODAL
        |--------------------------------------------------------------------------
        */

        modals.forEach(
            function (modal) {

                modal.addEventListener(
                    'click',
                    function (event) {

                        if (
                            event.target === modal
                        ) {

                            closeModal(
                                modal
                            );

                        }

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | KEYBOARD SUPPORT FOR CLICKABLE PANELS
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll(
                '.clickable-panel'
            )
            .forEach(
                function (panel) {

                    panel.addEventListener(
                        'keydown',
                        function (event) {

                            if (
                                event.key === 'Enter'
                                ||
                                event.key === ' '
                            ) {

                                event.preventDefault();


                                openModal(
                                    panel.dataset.modalOpen
                                );

                            }

                        }
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | ESCAPE
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key !== 'Escape'
                ) {
                    return;
                }


                const openModalElement =
                    document.querySelector(
                        '.modal.show'
                    );


                if (openModalElement) {

                    closeModal(
                        openModalElement
                    );

                }

            }
        );

    }
);

</script>


</body>

</html>