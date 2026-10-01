<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Show login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Process login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Check username, password, and account status
        $credentials['status'] = 'active';

        if (Auth::attempt($credentials)) {

            // Prevent session fixation
            $request->session()->regenerate();

            // Redirect according to user role
            return match (Auth::user()->role) {

                'admin' => redirect()->route('admin.dashboard'),

                'owner' => redirect()->route('owner.dashboard'),

                'secretary' => redirect()->route('secretary.dashboard'),

                'cashier' => redirect()->route('cashier.dashboard'),

                default => redirect()->route('dashboard'),
            };
        }

        return back()
            ->withErrors([
                'username' => 'The username or password is incorrect.',
            ])
            ->onlyInput('username');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}