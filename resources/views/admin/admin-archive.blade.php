<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Archive - DPAM IMS</title>

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
            grid-template-columns: repeat(3, 1fr);
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
        .filter-panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 20px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto auto;
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

        /* SECTION */
        .section {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .section-header { padding: 18px 20px; border-bottom: 1px solid var(--line); }

        .section-header h2 { margin: 0; font-size: 17px; }

        .table-wrapper { overflow-x: auto; }

        table { width: 100%; min-width: 800px; border-collapse: collapse; }

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

        .username { color: var(--muted); font-size: 11px; margin-top: 3px; }

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

        .inactive-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: #fee2e2;
            color: #991b1b;
            font-size: 11px;
            font-weight: 700;
        }

        .action { font-weight: 600; }

        .module { color: #475569; }

        .description { max-width: 350px; line-height: 1.45; }

        .reference { font-family: ui-monospace, Consolas, monospace; font-size: 12px; color: #475569; }

        .date-time { white-space: nowrap; color: var(--muted); font-size: 12px; }

        .empty-state { text-align: center; padding: 45px; color: var(--muted); }

        .pagination-container { padding: 18px 20px; }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .filter-form { grid-template-columns: repeat(2, 1fr); }
            .summary { grid-template-columns: 1fr; }
        }

        @media (max-width: 800px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; }
            .main { padding: 20px; }
            .filter-form { grid-template-columns: 1fr; }
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

            <a href="{{ route('admin.activity-logs') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l4 4v14H6z"/><path d="M9 12h7M9 16h7M9 8h3"/></svg>
                Activity Logs
            </a>

            <a href="{{ route('admin.archive') }}" class="active">
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


    {{-- MAIN --}}
    <main class="main">

        <div class="topbar">
            <div>
                <h1 class="page-title">Archive</h1>
                <div class="page-description">Inactive accounts and historical records (read-only).</div>
            </div>
            <div class="admin-badge">ADMIN</div>
        </div>


        {{-- SUMMARY (clickable) --}}
        <div class="summary">

            <a href="{{ route('admin.archive', ['type' => 'users']) }}" class="summary-card" style="--card-color:#dc2626">
                <div class="summary-label">Inactive Users</div>
                <div class="summary-value">{{ number_format($inactiveUsers->total()) }}</div>
            </a>

            <a href="{{ route('admin.archive', ['type' => 'activities']) }}" class="summary-card" style="--card-color:#2563eb">
                <div class="summary-label">Activity Records</div>
                <div class="summary-value">{{ number_format($activityLogs->total()) }}</div>
            </a>

            <a href="{{ route('admin.archive') }}" class="summary-card" style="--card-color:#059669">
                <div class="summary-label">Historical Records</div>
                <div class="summary-value">{{ number_format($historicalCount) }}</div>
            </a>

        </div>


        {{-- FILTER --}}
        <div class="filter-panel">

            <form method="GET" action="{{ route('admin.archive') }}" class="filter-form">

                <div class="field">
                    <label>Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search users or activities...">
                </div>

                <div class="field">
                    <label>Type</label>
                    <select name="type">
                        <option value="">All Records</option>
                        <option value="users" @selected(request('type') === 'users')>Inactive Users</option>
                        <option value="activities" @selected(request('type') === 'activities')>Activity Logs</option>
                    </select>
                </div>

                <div class="field">
                    <label>Year</label>
                    <select name="year">
                        <option value="">All Years</option>
                        @for($year = now()->year; $year >= now()->year - 5; $year--)
                            <option value="{{ $year }}" @selected((string) request('year') === (string) $year)>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>

                <button type="submit" class="button button-primary">Filter</button>

                <a href="{{ route('admin.archive') }}" class="button button-secondary">Clear</a>

            </form>

        </div>


        {{-- INACTIVE USERS --}}
        <section class="section">

            <div class="section-header">
                <h2>Inactive User Accounts</h2>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Date Updated</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($inactiveUsers as $user)

                            <tr>

                                <td><div class="user-name">{{ $user->name }}</div></td>

                                <td>{{ $user->username }}</td>

                                <td>{{ $user->email }}</td>

                                <td><span class="role">{{ ucfirst($user->role) }}</span></td>

                                <td><span class="inactive-badge">Inactive</span></td>

                                <td>
                                    <div class="date-time">
                                        {{ $user->updated_at->format('M d, Y') }}
                                        <br>
                                        {{ $user->updated_at->format('h:i A') }}
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="empty-state">No inactive user accounts found.</td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if($inactiveUsers->hasPages())
                <div class="pagination-container">
                    {{ $inactiveUsers->links() }}
                </div>
            @endif

        </section>


        {{-- ACTIVITY HISTORY --}}
        <section class="section">

            <div class="section-header">
                <h2>Historical Activity</h2>
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

                        @forelse($activityLogs as $log)

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
                                <td colspan="7" class="empty-state">No historical activity records found.</td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if($activityLogs->hasPages())
                <div class="pagination-container">
                    {{ $activityLogs->links() }}
                </div>
            @endif

        </section>

    </main>

</div>

</body>

</html>