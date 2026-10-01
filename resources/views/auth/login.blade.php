<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - DPAM IMS</title>

    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --accent: #2563eb;
            --accent-dark: #1d4ed8;
            --danger: #dc2626;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: "Segoe UI", system-ui, -apple-system, Roboto, Arial, sans-serif;
            color: var(--ink);
            background: linear-gradient(135deg, #0b1220 0%, #16233f 55%, #1e3a8a 100%);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .35);
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--accent);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 18px;
        }

        h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .tagline {
            margin: 4px 0 26px;
            font-size: 13px;
            color: var(--muted);
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .error p {
            margin: 2px 0;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 18px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .req::after {
            content: " *";
            color: var(--danger);
        }

        input {
            width: 100%;
            padding: 11px 13px;
            font-size: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
            color: var(--ink);
            transition: border-color .15s, box-shadow .15s;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
        }

        .pw-wrap {
            position: relative;
        }

        .pw-wrap input {
            padding-right: 44px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 8px;
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

        .toggle-password:hover {
            background: #f1f5f9;
            color: var(--ink);
        }

        .toggle-password svg {
            width: 18px;
            height: 18px;
        }

        .toggle-password .eye-off {
            display: none;
        }

        .toggle-password.showing .eye-on {
            display: none;
        }

        .toggle-password.showing .eye-off {
            display: block;
        }

        .submit {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: var(--accent);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }

        .submit:hover {
            background: var(--accent-dark);
        }
    </style>
</head>

<body>

    <div class="login-card">

        <div class="brand-mark">DP</div>

        <h1>DPAM Inventory Management System</h1>

        <p class="tagline">Sign in to continue</p>

        {{-- Display login errors --}}
        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" autocomplete="off">
            @csrf

            <div class="form-group">
                <label class="req" for="username">Username</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    value=""
                    autocomplete="off"
                    required
                >
            </div>

            <div class="form-group">
                <label class="req" for="password">Password</label>

                <div class="pw-wrap">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword(this)"
                        id="eye"
                        aria-label="Show or hide password"
                    >
                        <svg class="eye-on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.6 10.6 0 0112 5c6.5 0 10 7 10 7a17 17 0 01-3.2 4.2M6.6 6.6A16.6 16.6 0 002 12s3.5 7 10 7a10 10 0 004.4-1"/><path d="M9.9 9.9a3 3 0 004.2 4.2"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="submit">Login</button>

        </form>

    </div>

    <script>
        function togglePassword(button) {
            const input = (button || document.getElementById('eye'))
                .parentElement
                .querySelector('input');

            const btn = button || document.getElementById('eye');

            if (input.type === 'password') {
                input.type = 'text';
                btn.classList.add('showing');
            } else {
                input.type = 'password';
                btn.classList.remove('showing');
            }
        }

        // Make sure fields are empty when the page is restored from cache
        window.addEventListener('pageshow', function () {
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
        });
    </script>

</body>

</html>