<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Activity Logs - DPAM IMS</title>

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

        /* SUMMARY */
        .summary {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
            margin-bottom: 20px;
        }

        .summary-card {
            display: block;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: transform .15s, box-shadow .15s;
        }

        .summary-card::before {
            content: "";
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--card-color, var(--accent));
        }

        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .09);
        }

        .summary-label { color: var(--muted); font-size: 13px; margin-bottom: 8px; }

        .summary-value { font-size: 30px; font-weight: 700; }

        /* FILTER */
        .filter-panel, .table-panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
        }

        .filter-panel { padding: 18px; margin-bottom: 20px; }

        .filter-form {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr 1fr auto auto;
            gap: 10px;
            align-items: end;
        }

        .field { display: flex; flex-direction: column; gap: 6px; }

        .field label { font-size: 12px; color: var(--muted); font-weight: 600; }

        .field input, .field select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
            font-size: 13px;
            font-family: inherit;
            color: var(--ink);
            transition: border-color .15s, box-shadow .15s;
        }

        .field input:focus, .field select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .button {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            text-align: center;
            transition: background .15s;
        }

        .button-primary { background: var(--accent); color: #fff; }
        .button-primary:hover { background: var(--accent-dark); }
        .button-secondary { background: #e2e8f0; color: #334155; }
        .button-secondary:hover { background: #cbd5e1; }

        /* TABLE */
        .table-panel { overflow: hidden; }

        .table-header { padding: 18px 20px; border-bottom: 1px solid var(--line); }

        .table-header h2 { margin: 0; font-size: 17px; }

        .table-wrapper { overflow-x: auto; }

        table { width: 100%; min-width: 1000px; border-collapse: collapse; }

        th, td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            font-size: 13px;
            vertical-align: top;
        }

        th { color: var(--muted); font-weight: 600; background: #f8fafc; white-space: nowrap; }

        tbody tr:hover td { background: #f8fafc; }

        tr:last-child td { border-bottom: none; }

        .user-name { font-weight: 600; }

        .username { font-size: 11px; color: var(--muted); margin-top: 3px; }

        .role {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 6px;
            background: #eff6ff;
            color: #1e40af;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .action { font-weight: 600; }

        .module { color: #475569; }

        .description { max-width: 320px; line-height: 1.45; }

        .reference { font-family: ui-monospace, Consolas, monospace; font-size: 12px; color: #475569; }

        .date-time { white-space: nowrap; color: var(--muted); font-size: 12px; }

        .empty-state { text-align: center; padding: 50px; color: var(--muted); }

        .pagination-container { padding: 18px 20px; }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .filter-form { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 800px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; }
            .main { padding: 20px; }
            .summary, .filter-form { grid-template-columns: 1fr; }
            .topbar { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>

<body>

<div class="layout">

    {{-- SIDEBAR --}}
    <aside class="sidebar">

        <div class="brand">DPAM IMS</div>

        <div class="subtitle">Industrial Supplies & Services Inc.</div>

        <div class="menu-title">Administration</div>

        <nav class="menu">

            <a href="{{ route('admin.dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                Dashboard
            </a>

            <a href="{{ route('admin.users') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.6-3.5 3.3-5.5 6.5-5.5s5.9 2 6.5 5.5"/><path d="M16 4.5a3.5 3.5 0 010 7M18 14.8c2 .6 3.3 2.3 3.5 5.2"/></svg>
                User Management
            </a>

            <a href="{{ route('admin.activity-logs') }}" class="active">
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
                <h1 class="page-title">Activity Logs</h1>
                <div class="page-description">Monitor system activities performed by users.</div>
            </div>
            <div class="admin-badge">ADMIN</div>
        </div>


        {{-- SUMMARY CARDS (clickable) --}}
        <div class="summary">

            <a href="{{ route('admin.activity-logs') }}" class="summary-card" style="--card-color:#2563eb">
                <div class="summary-label">Total Activity Records</div>
                <div class="summary-value">{{ number_format($totalLogs) }}</div>
            </a>

            <a href="{{ route('admin.activity-logs', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()]) }}" class="summary-card" style="--card-color:#f59e0b">
                <div class="summary-label">Activities Today</div>
                <div class="summary-value">{{ number_format($todayLogs) }}</div>
            </a>

        </div>


        {{-- FILTERS --}}
        <div class="filter-panel">

            <form method="GET" action="{{ route('admin.activity-logs') }}" class="filter-form">

                <div class="field">
                    <label>Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search activity...">
                </div>

                <div class="field">
                    <label>Module</label>
                    <select name="module">
                        <option value="">All Modules</option>
                        @foreach($modules as $item)
                            <option value="{{ $item }}" @selected(request('module') === $item)>
                                {{ $item }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Action</label>
                    <select name="action">
                        <option value="">All Actions</option>
                        @foreach($actions as $item)
                            <option value="{{ $item }}" @selected(request('action') === $item)>
                                {{ $item }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>User</label>
                    <select name="user_id">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected((string) request('user_id') === (string) $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="field">
                    <label>To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>

                <button type="submit" class="button button-primary">Filter</button>

                <a href="{{ route('admin.activity-logs') }}" class="button button-secondary">Clear</a>

            </form>

        </div>


        {{-- ACTIVITY TABLE --}}
        <section class="table-panel">

            <div class="table-header">
                <h2>System Activity</h2>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>Reference</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($logs as $log)

                            <tr>

                                <td>
                                    <div class="user-name">{{ $log->user->name ?? 'System' }}</div>

                                    @if($log->user)
                                        <div class="username">{{ $log->user->username }}</div>
                                    @endif
                                </td>

                                <td>
                                    @if($log->user)
                                        <span class="role">{{ ucfirst($log->user->role) }}</span>
                                    @else
                                        <span class="role">System</span>
                                    @endif
                                </td>

                                <td><div class="action">{{ $log->action }}</div></td>

                                <td><div class="module">{{ $log->module }}</div></td>

                                <td><div class="description">{{ $log->description ?? '-' }}</div></td>

                                <td>
                                    @if($log->reference)
                                        <span class="reference">{{ $log->reference }}</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <div class="date-time">
                                        {{ $log->created_at->format('M d, Y') }}
                                        <br>
                                        {{ $log->created_at->format('h:i A') }}
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="empty-state">No activity logs found.</td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if($logs->hasPages())
                <div class="pagination-container">
                    {{ $logs->links() }}
                </div>
            @endif

        </section>

    </main>

</div>

</body>

</html>