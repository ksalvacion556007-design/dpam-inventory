<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard - DPAM IMS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", system-ui, -apple-system, Roboto, Arial, sans-serif;
            background: var(--bg);
            color: var(--ink);
        }

        a { color: inherit; text-decoration: none; }

        .layout { display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            width: 256px;
            flex-shrink: 0;
            background: var(--sidebar);
            color: #fff;
            padding: 24px 14px;
            display: flex;
            flex-direction: column;
        }

        .brand { font-size: 20px; font-weight: 700; padding: 0 12px; }

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

        .menu a, .logout-button {
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
            transition: background .15s, color .15s;
        }

        .menu a svg, .logout-button svg { width: 18px; height: 18px; flex-shrink: 0; }

        .menu a:hover, .logout-button:hover { background: #1e293b; color: #fff; }

        .menu a.active { background: var(--accent); color: #fff; font-weight: 600; }

        .logout-form { margin-top: auto; padding-top: 24px; }

        /* MAIN */
        .main { flex: 1; min-width: 0; padding: 30px 34px; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 26px;
        }

        .page-title { margin: 0; font-size: 26px; font-weight: 700; }

        .page-description { margin-top: 5px; color: var(--muted); font-size: 14px; }

        .admin-badge {
            background: #dbeafe;
            color: #1e40af;
            padding: 7px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .05em;
        }

        /* KPI CARDS */
        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
            transition: transform .15s, box-shadow .15s, border-color .15s;
        }

        .card::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--card-color, var(--accent));
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .09);
            border-color: #cbd5e1;
        }

        .card-label { font-size: 13px; color: var(--muted); margin-bottom: 10px; }

        .card-value { font-size: 30px; font-weight: 700; }

        .card-link { margin-top: 10px; font-size: 12px; font-weight: 600; color: var(--card-color, var(--accent)); }

        /* PANELS */
        .panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 22px;
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .panel h2 { margin: 0; font-size: 17px; }

        .panel-link { font-size: 13px; font-weight: 600; color: var(--accent); cursor: pointer; }
        .panel-link:hover { text-decoration: underline; }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 22px;
        }

        /* DONUT */
        .donut-wrap { display: flex; flex-direction: column; align-items: center; gap: 16px; }

        .donut { width: 170px; height: 170px; }

        .donut a circle { transition: stroke-width .15s; cursor: pointer; }
        .donut a:hover circle { stroke-width: 26; }

        .donut-total { font-size: 26px; font-weight: 700; fill: var(--ink); }
        .donut-sub { font-size: 11px; fill: var(--muted); }

        .legend { width: 100%; display: flex; flex-direction: column; gap: 6px; }

        .legend a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: background .15s;
        }

        .legend a:hover { background: #f1f5f9; }

        .legend .dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .legend strong { font-weight: 700; }

        /* BARS */
        .bar-list { display: flex; flex-direction: column; gap: 10px; }

        .bar-item {
            display: block;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: background .15s;
        }

        .bar-item:hover { background: #f1f5f9; }

        .bar-top {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 7px;
        }

        .bar-top strong { font-weight: 700; }

        .bar-track { height: 10px; background: #e2e8f0; border-radius: 999px; overflow: hidden; }

        .bar-fill { height: 100%; border-radius: 999px; background: var(--bar-color, var(--accent)); min-width: 3px; }

        /* TABLE */
        .table-wrapper { overflow-x: auto; }

        .activity-table { width: 100%; border-collapse: collapse; min-width: 560px; }

        .activity-table th, .activity-table td {
            padding: 12px 10px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            font-size: 13px;
        }

        .activity-table th {
            color: var(--muted);
            font-weight: 600;
            background: #f8fafc;
            white-space: nowrap;
        }

        .activity-table tr:last-child td { border-bottom: none; }

        .empty-state { color: var(--muted); font-size: 14px; padding: 24px 0; text-align: center; }

        /* DATA POPUP MODAL */
        .data-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1000;
        }

        .data-modal.show { display: flex; }

        .data-modal-box {
            background: #fff;
            width: 100%;
            max-width: 1000px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .3);
            overflow: hidden;
        }

        .data-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 18px 22px;
            border-bottom: 1px solid var(--line);
        }

        .data-modal-header h2 { margin: 0; font-size: 18px; }

        .data-modal-actions { display: flex; align-items: center; gap: 10px; }

        .full-link {
            font-size: 13px;
            font-weight: 600;
            color: var(--accent);
            padding: 7px 12px;
            border-radius: 8px;
            background: #eff6ff;
        }

        .full-link:hover { background: #dbeafe; }

        .close-button {
            border: none;
            background: transparent;
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
            color: var(--muted);
            border-radius: 6px;
            width: 34px;
            height: 34px;
        }

        .close-button:hover { background: #f1f5f9; color: var(--ink); }

        .data-modal-body { padding: 0; overflow: auto; }

        .modal-loading { padding: 50px; text-align: center; color: var(--muted); font-size: 14px; }

        /* Styles for the table loaded inside the modal */
        .data-modal-body .table-header,
        .data-modal-body .section-header { padding: 14px 22px; border-bottom: 1px solid var(--line); }

        .data-modal-body .table-header h2,
        .data-modal-body .section-header h2 { margin: 0; font-size: 15px; }

        .data-modal-body .table-wrapper { overflow-x: auto; }

        .data-modal-body table { width: 100%; border-collapse: collapse; min-width: 620px; }

        .data-modal-body th,
        .data-modal-body td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            font-size: 13px;
            vertical-align: top;
        }

        .data-modal-body th { color: var(--muted); font-weight: 600; background: #f8fafc; white-space: nowrap; }

        .data-modal-body tr:last-child td { border-bottom: none; }

        .data-modal-body .role {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 6px;
            background: #eff6ff;
            color: #1e40af;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .data-modal-body .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .data-modal-body .status-active { background: #dcfce7; color: #166534; }
        .data-modal-body .status-inactive { background: #fee2e2; color: #991b1b; }

        .data-modal-body .user-name { font-weight: 600; }
        .data-modal-body .username { font-size: 11px; color: var(--muted); margin-top: 3px; }
        .data-modal-body .action { font-weight: 600; }
        .data-modal-body .description { max-width: 320px; line-height: 1.45; }
        .data-modal-body .reference { font-family: ui-monospace, Consolas, monospace; font-size: 12px; }
        .data-modal-body .date-time { white-space: nowrap; color: var(--muted); font-size: 12px; }
        .data-modal-body .empty-state { padding: 40px; }

        .data-modal-body .pagination,
        .data-modal-body .pagination-container { padding: 16px 22px; }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .chart-grid { grid-template-columns: 1fr 1fr; }
            .chart-grid .panel:last-child { grid-column: 1 / -1; }
        }

        @media (max-width: 1000px) {
            .cards { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 800px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; }
            .main { padding: 20px; }
            .chart-grid { grid-template-columns: 1fr; }
            .topbar { flex-direction: column; align-items: flex-start; }
        }

        @media (max-width: 560px) {
            .cards { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>

@php
    $roleData = [
        ['label' => 'Owner',     'count' => (int) $ownerCount,     'color' => '#2563eb', 'key' => 'owner'],
        ['label' => 'Secretary', 'count' => (int) $secretaryCount, 'color' => '#0ea5e9', 'key' => 'secretary'],
        ['label' => 'Cashier',   'count' => (int) $cashierCount,   'color' => '#10b981', 'key' => 'cashier'],
        ['label' => 'Admin',     'count' => (int) $adminCount,     'color' => '#f59e0b', 'key' => 'admin'],
    ];

    $roleTotal = max(array_sum(array_column($roleData, 'count')), 0);
    $radius = 60;
    $circumference = 2 * M_PI * $radius;
    $offset = 0;

    $statusMax = max((int) $totalUsers, 1);

    $moduleCounts = $recentActivities->groupBy('module')->map->count()->sortDesc();
    $moduleMax = max($moduleCounts->max() ?? 1, 1);

    $todayParams = ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()];
@endphp

<div class="layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="brand">DPAM IMS</div>

        <div class="subtitle">Industrial Supplies & Services Inc.</div>

        <div class="menu-title">Administration</div>

        <nav class="menu">

            <a href="{{ route('admin.dashboard') }}" class="active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                Dashboard
            </a>

            <a href="{{ route('admin.users') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.6-3.5 3.3-5.5 6.5-5.5s5.9 2 6.5 5.5"/><path d="M16 4.5a3.5 3.5 0 010 7M18 14.8c2 .6 3.3 2.3 3.5 5.2"/></svg>
                User Management
            </a>

            <a href="{{ route('admin.activity-logs') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l4 4v14H6z"/><path d="M9 12h7M9 16h7M9 8h3"/></svg>
                Activity Logs
            </a>

            <a href="{{ route('admin.archive') }}">
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
                <h1 class="page-title">Admin Dashboard</h1>
                <div class="page-description">System administration overview</div>
            </div>
            <div class="admin-badge">ADMIN</div>
        </div>


        {{-- KPI CARDS (open popup) --}}
        <div class="cards">

            <a href="{{ route('admin.users') }}" class="card js-modal" data-title="All Users" style="--card-color:#2563eb">
                <div class="card-label">Total Users</div>
                <div class="card-value">{{ $totalUsers }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="{{ route('admin.users', ['status' => 'active']) }}" class="card js-modal" data-title="Active Users" style="--card-color:#059669">
                <div class="card-label">Active Users</div>
                <div class="card-value">{{ $activeUsers }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="{{ route('admin.users', ['status' => 'inactive']) }}" class="card js-modal" data-title="Inactive Users" style="--card-color:#dc2626">
                <div class="card-label">Inactive Users</div>
                <div class="card-value">{{ $inactiveUsers }}</div>
                <div class="card-link">View details</div>
            </a>

            <a href="{{ route('admin.activity-logs', $todayParams) }}" class="card js-modal" data-title="Activities Today" style="--card-color:#f59e0b">
                <div class="card-label">Activities Today</div>
                <div class="card-value">{{ $activitiesToday }}</div>
                <div class="card-link">View details</div>
            </a>

        </div>


        {{-- GRAPHS (open popup) --}}
        <div class="chart-grid">

            {{-- Users by role --}}
            <section class="panel">

                <div class="panel-head">
                    <h2>Users by Role</h2>
                    <a href="{{ route('admin.users') }}" class="panel-link js-modal" data-title="All Users">Open</a>
                </div>

                <div class="donut-wrap">

                    <svg class="donut" viewBox="0 0 160 160">

                        <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="#e2e8f0" stroke-width="22"/>

                        @if($roleTotal > 0)
                            @foreach($roleData as $slice)
                                @if($slice['count'] > 0)
                                    @php
                                        $length = ($slice['count'] / $roleTotal) * $circumference;
                                    @endphp
                                    <a href="{{ route('admin.users', ['role' => $slice['key']]) }}" class="js-modal" data-title="{{ $slice['label'] }} Users">
                                        <title>{{ $slice['label'] }}: {{ $slice['count'] }}</title>
                                        <circle
                                            cx="80" cy="80" r="{{ $radius }}"
                                            fill="none"
                                            stroke="{{ $slice['color'] }}"
                                            stroke-width="22"
                                            stroke-dasharray="{{ $length }} {{ $circumference - $length }}"
                                            stroke-dashoffset="{{ -$offset }}"
                                            transform="rotate(-90 80 80)"
                                        />
                                    </a>
                                    @php $offset += $length; @endphp
                                @endif
                            @endforeach
                        @endif

                        <text x="80" y="82" text-anchor="middle" class="donut-total">{{ $roleTotal }}</text>
                        <text x="80" y="98" text-anchor="middle" class="donut-sub">Users</text>

                    </svg>

                    <div class="legend">
                        @foreach($roleData as $slice)
                            <a href="{{ route('admin.users', ['role' => $slice['key']]) }}" class="js-modal" data-title="{{ $slice['label'] }} Users">
                                <span>
                                    <span class="dot" style="background:{{ $slice['color'] }}"></span>{{ $slice['label'] }}
                                </span>
                                <strong>{{ $slice['count'] }}</strong>
                            </a>
                        @endforeach
                    </div>

                </div>

            </section>


            {{-- Account status --}}
            <section class="panel">

                <div class="panel-head">
                    <h2>Account Status</h2>
                    <a href="{{ route('admin.users') }}" class="panel-link js-modal" data-title="All Users">Open</a>
                </div>

                <div class="bar-list">

                    <a href="{{ route('admin.users', ['status' => 'active']) }}" class="bar-item js-modal" data-title="Active Users" style="--bar-color:#059669">
                        <div class="bar-top"><span>Active</span><strong>{{ $activeUsers }}</strong></div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ ($activeUsers / $statusMax) * 100 }}%"></div>
                        </div>
                    </a>

                    <a href="{{ route('admin.users', ['status' => 'inactive']) }}" class="bar-item js-modal" data-title="Inactive Users" style="--bar-color:#dc2626">
                        <div class="bar-top"><span>Inactive</span><strong>{{ $inactiveUsers }}</strong></div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ ($inactiveUsers / $statusMax) * 100 }}%"></div>
                        </div>
                    </a>

                    <a href="{{ route('admin.activity-logs', $todayParams) }}" class="bar-item js-modal" data-title="Activities Today" style="--bar-color:#f59e0b">
                        <div class="bar-top"><span>Activities Today</span><strong>{{ $activitiesToday }}</strong></div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ min(100, ($activitiesToday / max($activitiesToday, $statusMax)) * 100) }}%"></div>
                        </div>
                    </a>

                </div>

            </section>


            {{-- Activity by module --}}
            <section class="panel">

                <div class="panel-head">
                    <h2>Recent Activity by Module</h2>
                    <a href="{{ route('admin.activity-logs') }}" class="panel-link js-modal" data-title="Activity Logs">Open</a>
                </div>

                @if($moduleCounts->count() > 0)

                    <div class="bar-list">
                        @foreach($moduleCounts as $moduleName => $count)
                            <a href="{{ route('admin.activity-logs', ['module' => $moduleName]) }}" class="bar-item js-modal" data-title="{{ $moduleName ?: 'Activity' }} Activity">
                                <div class="bar-top"><span>{{ $moduleName ?: '-' }}</span><strong>{{ $count }}</strong></div>
                                <div class="bar-track">
                                    <div class="bar-fill" style="width: {{ ($count / $moduleMax) * 100 }}%"></div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                @else

                    <div class="empty-state">No recent activity.</div>

                @endif

            </section>

        </div>


        {{-- RECENT ACTIVITY TABLE --}}
        <section class="panel">

            <div class="panel-head">
                <h2>Recent System Activity</h2>
                <a href="{{ route('admin.activity-logs') }}" class="panel-link js-modal" data-title="Activity Logs">View all</a>
            </div>

            @if($recentActivities->count() > 0)

                <div class="table-wrapper">
                    <table class="activity-table">

                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Module</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($recentActivities as $activity)
                                <tr>
                                    <td>{{ $activity->user->name ?? 'System' }}</td>
                                    <td>{{ $activity->action ?? '-' }}</td>
                                    <td>{{ $activity->module ?? '-' }}</td>
                                    <td>{{ $activity->created_at?->format('M d, Y h:i A') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            @else

                <div class="empty-state">No recent system activity.</div>

            @endif

        </section>

    </main>

</div>


{{-- DATA POPUP MODAL --}}
<div id="dataModal" class="data-modal">

    <div class="data-modal-box">

        <div class="data-modal-header">

            <h2 id="dataModalTitle">Details</h2>

            <div class="data-modal-actions">
                <a href="#" id="dataModalFull" class="full-link">Open full page</a>
                <button type="button" class="close-button" onclick="closeDataModal()" aria-label="Close">&times;</button>
            </div>

        </div>

        <div id="dataModalBody" class="data-modal-body"></div>

    </div>

</div>


<script>

    const dataModal = document.getElementById('dataModal');
    const dataModalTitle = document.getElementById('dataModalTitle');
    const dataModalBody = document.getElementById('dataModalBody');
    const dataModalFull = document.getElementById('dataModalFull');


    /* Remove the Actions column and any buttons/forms so the popup is view-only */
    function cleanModalContent(panel)
    {
        panel.querySelectorAll('script, form, button').forEach(function(el)
        {
            el.remove();
        });

        panel.querySelectorAll('table').forEach(function(table)
        {
            const headers = Array.from(table.querySelectorAll('thead th'));

            const actionIndex = headers.findIndex(function(th)
            {
                return th.textContent.trim().toLowerCase() === 'actions';
            });

            if (actionIndex === -1) return;

            table.querySelectorAll('tr').forEach(function(row)
            {
                const cell = row.children[actionIndex];

                if (cell) cell.remove();
            });
        });
    }


    async function loadModalData(url, title)
    {
        dataModalTitle.textContent = title || 'Details';

        dataModalFull.href = url;

        dataModalBody.innerHTML = '<div class="modal-loading">Loading...</div>';

        dataModal.classList.add('show');

        try
        {
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Request failed');

            const html = await response.text();

            const doc = new DOMParser().parseFromString(html, 'text/html');

            const panel = doc.querySelector('.table-panel') || doc.querySelector('.section');

            if (!panel) throw new Error('No data');

            cleanModalContent(panel);

            dataModalBody.innerHTML = '';

            dataModalBody.appendChild(document.importNode(panel, true));
        }
        catch (error)
        {
            dataModalBody.innerHTML = '<div class="modal-loading">Unable to load data.</div>';
        }
    }


    function closeDataModal()
    {
        dataModal.classList.remove('show');

        dataModalBody.innerHTML = '';
    }


    /* Open popup for every card, graph and link marked js-modal */
    document.addEventListener('click', function(event)
    {
        const trigger = event.target.closest('.js-modal');

        if (trigger && !dataModal.contains(trigger))
        {
            event.preventDefault();

            loadModalData(
                trigger.getAttribute('href'),
                trigger.getAttribute('data-title')
            );

            return;
        }

        /* Pagination links inside the popup load within the popup */
        const pageLink = event.target.closest('#dataModalBody a[href]');

        if (pageLink)
        {
            event.preventDefault();

            loadModalData(pageLink.href, dataModalTitle.textContent);

            return;
        }

        /* Click outside the box closes the popup */
        if (event.target === dataModal)
        {
            closeDataModal();
        }
    });


    document.addEventListener('keydown', function(event)
    {
        if (event.key === 'Escape' && dataModal.classList.contains('show'))
        {
            closeDataModal();
        }
    });

</script>

</body>

</html>