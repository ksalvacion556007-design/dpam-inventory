<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Management - DPAM IMS</title>

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

        /* ALERTS */
        .alert, .errors {
            padding: 13px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error, .errors { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .errors ul { margin: 8px 0 0; padding-left: 20px; }

        /* PANELS */
        .toolbar, .table-panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
        }

        .toolbar { padding: 18px; margin-bottom: 20px; }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 180px 180px auto;
            gap: 10px;
            align-items: end;
        }

        .field { display: flex; flex-direction: column; gap: 6px; }

        .field label, .form-group label {
            font-size: 12px;
            color: var(--muted);
            font-weight: 600;
        }

        .req::after { content: " *"; color: var(--danger); }

        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
            font-size: 14px;
            font-family: inherit;
            color: var(--ink);
            transition: border-color .15s, box-shadow .15s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        /* BUTTONS */
        .button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            text-align: center;
            display: inline-block;
            transition: background .15s;
        }

        .button-primary { background: var(--accent); color: #fff; }
        .button-primary:hover { background: var(--accent-dark); }
        .button-secondary { background: #e2e8f0; color: #334155; }
        .button-secondary:hover { background: #cbd5e1; }
        .button-success { background: var(--success); color: #fff; }
        .button-danger { background: var(--danger); color: #fff; }
        .button-small { padding: 7px 11px; font-size: 12px; }

        /* TABLE */
        .table-panel { overflow: hidden; }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            border-bottom: 1px solid var(--line);
        }

        .table-header h2 { margin: 0; font-size: 17px; }

        .table-wrapper { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }

        th, td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            font-size: 13px;
            white-space: nowrap;
        }

        th { color: var(--muted); font-weight: 600; background: #f8fafc; }

        tbody tr:hover td { background: #f8fafc; }

        tr:last-child td { border-bottom: none; }

        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-active { background: #dcfce7; color: #166534; }
        .status-inactive { background: #fee2e2; color: #991b1b; }

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

        .actions { display: flex; gap: 6px; }

        .pagination { padding: 18px 20px; }

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 1000;
        }

        .modal.show { display: flex; }

        .modal-box {
            background: #fff;
            width: 100%;
            max-width: 620px;
            max-height: 92vh;
            overflow-y: auto;
            border-radius: 16px;
            padding: 26px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--line);
        }

        .modal-header h2 { margin: 0; font-size: 19px; }

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

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .form-group { display: flex; flex-direction: column; gap: 6px; }

        .form-group.full { grid-column: 1 / -1; }

        .form-group label { font-size: 13px; color: #334155; }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 22px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
        }

        /* PASSWORD EYE */
        .pw-wrap { position: relative; }

        .pw-wrap input { padding-right: 44px; }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 6px;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover { background: #f1f5f9; color: var(--ink); }
        .toggle-password svg { width: 18px; height: 18px; }
        .toggle-password .eye-off { display: none; }
        .toggle-password.showing .eye-on { display: none; }
        .toggle-password.showing .eye-off { display: block; }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .filter-form, .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: auto; }
        }

        @media (max-width: 800px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; }
            .main { padding: 20px; }
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

            <a href="{{ route('admin.users') }}" class="active">
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


    {{-- MAIN --}}
    <main class="main">

        <div class="topbar">
            <div>
                <h1 class="page-title">User Management</h1>
                <div class="page-description">Manage system user accounts and access roles.</div>
            </div>
            <div class="admin-badge">ADMIN</div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="errors">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {{-- FILTER TOOLBAR --}}
        <div class="toolbar">

            <form method="GET" action="{{ route('admin.users') }}" class="filter-form">

                <div class="field">
                    <label>Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, username, or email">
                </div>

                <div class="field">
                    <label>Role</label>
                    <select name="role">
                        <option value="">All Roles</option>
                        <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        <option value="owner" @selected(request('role') === 'owner')>Owner</option>
                        <option value="secretary" @selected(request('role') === 'secretary')>Secretary</option>
                        <option value="cashier" @selected(request('role') === 'cashier')>Cashier</option>
                    </select>
                </div>

                <div class="field">
                    <label>Status</label>
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="button button-primary">Search</button>

            </form>

        </div>


        {{-- USER TABLE --}}
        <section class="table-panel">

            <div class="table-header">
                <h2>System Users</h2>

                <button type="button" class="button button-primary" onclick="openCreateModal()">
                    + Add User
                </button>
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
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($users as $user)

                            <tr>

                                <td><strong>{{ $user->name }}</strong></td>

                                <td>{{ $user->username }}</td>

                                <td>{{ $user->email }}</td>

                                <td>
                                    <span class="role">{{ ucfirst($user->role) }}</span>
                                </td>

                                <td>
                                    @if($user->status === 'active')
                                        <span class="status status-active">Active</span>
                                    @else
                                        <span class="status status-inactive">Inactive</span>
                                    @endif
                                </td>

                                <td>

                                    <div class="actions">

                                        {{-- EDIT --}}
                                        <button
                                            type="button"
                                            class="button button-secondary button-small"
                                            onclick="openEditModal(
                                                {{ $user->id }},
                                                @js($user->name),
                                                @js($user->username),
                                                @js($user->email),
                                                @js($user->role),
                                                @js($user->status)
                                            )"
                                        >
                                            Edit
                                        </button>

                                        {{-- ACTIVATE --}}
                                        @if($user->status === 'inactive')

                                            <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="button button-success button-small">
                                                    Activate
                                                </button>
                                            </form>

                                        @else

                                            {{-- DEACTIVATE --}}
                                            @if(auth()->id() !== $user->id)

                                                <form method="POST" action="{{ route('admin.users.deactivate', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="button button-danger button-small">
                                                        Deactivate
                                                    </button>
                                                </form>

                                            @endif

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" style="text-align:center; padding:32px; color:#64748b;">
                                    No users found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            <div class="pagination">
                {{ $users->links() }}
            </div>

        </section>

    </main>

</div>


{{-- CREATE USER MODAL --}}
<div id="createModal" class="modal">

    <div class="modal-box">

        <div class="modal-header">
            <h2>Add User</h2>
            <button type="button" class="close-button" onclick="closeCreateModal()">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" autocomplete="off">

            @csrf

            <div class="form-grid">

                <div class="form-group full">
                    <label class="req">Full Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label class="req">Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label class="req">Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label class="req">Role</label>
                    <select name="role" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                        <option value="secretary">Secretary</option>
                        <option value="cashier">Cashier</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="req">Status</label>
                    <select name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="req">Password</label>
                    <div class="pw-wrap">
                        <input type="password" name="password" autocomplete="new-password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword(this)" aria-label="Show or hide password">
                            <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.6 10.6 0 0112 5c6.5 0 10 7 10 7a17 17 0 01-3.2 4.2M6.6 6.6A16.6 16.6 0 002 12s3.5 7 10 7a10 10 0 004.4-1"/><path d="M9.9 9.9a3 3 0 004.2 4.2"/></svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="req">Confirm Password</label>
                    <div class="pw-wrap">
                        <input type="password" name="password_confirmation" autocomplete="new-password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword(this)" aria-label="Show or hide password">
                            <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.6 10.6 0 0112 5c6.5 0 10 7 10 7a17 17 0 01-3.2 4.2M6.6 6.6A16.6 16.6 0 002 12s3.5 7 10 7a10 10 0 004.4-1"/><path d="M9.9 9.9a3 3 0 004.2 4.2"/></svg>
                        </button>
                    </div>
                </div>

            </div>

            <div class="form-actions">
                <button type="button" class="button button-secondary" onclick="closeCreateModal()">Cancel</button>
                <button type="submit" class="button button-primary">Create User</button>
            </div>

        </form>

    </div>

</div>


{{-- EDIT USER MODAL --}}
<div id="editModal" class="modal">

    <div class="modal-box">

        <div class="modal-header">
            <h2>Edit User</h2>
            <button type="button" class="close-button" onclick="closeEditModal()">&times;</button>
        </div>

        <form id="editUserForm" method="POST" autocomplete="off">

            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group full">
                    <label class="req">Full Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>

                <div class="form-group">
                    <label class="req">Username</label>
                    <input type="text" id="edit_username" name="username" required>
                </div>

                <div class="form-group">
                    <label class="req">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>

                <div class="form-group">
                    <label class="req">Role</label>
                    <select id="edit_role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                        <option value="secretary">Secretary</option>
                        <option value="cashier">Cashier</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="req">Status</label>
                    <select id="edit_status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>New Password</label>
                    <div class="pw-wrap">
                        <input type="password" id="edit_password" name="password" autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword(this)" aria-label="Show or hide password">
                            <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.6 10.6 0 0112 5c6.5 0 10 7 10 7a17 17 0 01-3.2 4.2M6.6 6.6A16.6 16.6 0 002 12s3.5 7 10 7a10 10 0 004.4-1"/><path d="M9.9 9.9a3 3 0 004.2 4.2"/></svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <div class="pw-wrap">
                        <input type="password" id="edit_password_confirmation" name="password_confirmation" autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePassword(this)" aria-label="Show or hide password">
                            <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.6 10.6 0 0112 5c6.5 0 10 7 10 7a17 17 0 01-3.2 4.2M6.6 6.6A16.6 16.6 0 002 12s3.5 7 10 7a10 10 0 004.4-1"/><path d="M9.9 9.9a3 3 0 004.2 4.2"/></svg>
                        </button>
                    </div>
                </div>

            </div>

            <div class="form-actions">
                <button type="button" class="button button-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="button button-primary">Save Changes</button>
            </div>

        </form>

    </div>

</div>


<script>

    /* PASSWORD EYE TOGGLE */
    function togglePassword(button)
    {
        const input = button.parentElement.querySelector('input');

        if (input.type === 'password')
        {
            input.type = 'text';
            button.classList.add('showing');
        }
        else
        {
            input.type = 'password';
            button.classList.remove('showing');
        }
    }

    function resetPasswordToggles(modal)
    {
        modal.querySelectorAll('.pw-wrap').forEach(function(wrap)
        {
            wrap.querySelector('input').type = 'password';
            wrap.querySelector('.toggle-password').classList.remove('showing');
        });
    }


    /* CREATE MODAL */
    function openCreateModal()
    {
        document.getElementById('createModal').classList.add('show');
    }

    function closeCreateModal()
    {
        const modal = document.getElementById('createModal');

        modal.classList.remove('show');

        resetPasswordToggles(modal);
    }


    /* EDIT MODAL */
    function openEditModal(id, name, username, email, role, status)
    {
        document.getElementById('edit_name').value = name;

        document.getElementById('edit_username').value = username;

        document.getElementById('edit_email').value = email;

        document.getElementById('edit_role').value = role;

        document.getElementById('edit_status').value = status;

        document.getElementById('edit_password').value = '';

        document.getElementById('edit_password_confirmation').value = '';

        document.getElementById('editUserForm').action =
            "{{ url('/admin/users') }}/" + id;

        document.getElementById('editModal').classList.add('show');
    }

    function closeEditModal()
    {
        const modal = document.getElementById('editModal');

        modal.classList.remove('show');

        resetPasswordToggles(modal);
    }


    /* CLOSE MODALS WHEN CLICKING OUTSIDE */
    window.addEventListener('click', function(event)
    {
        const createModal = document.getElementById('createModal');

        const editModal = document.getElementById('editModal');

        if (event.target === createModal)
        {
            closeCreateModal();
        }

        if (event.target === editModal)
        {
            closeEditModal();
        }
    });

</script>

</body>

</html>