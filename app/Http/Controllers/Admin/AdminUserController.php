<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * Display the User Management page.
     */
    public function index(Request $request)
    {
        $search = $request->input('q');
        $role = $request->input('role');
        $status = $request->input('status');

        $users = User::query()
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                });

            })
            ->when($role, function ($query) use ($role) {

                $query->where('role', $role);

            })
            ->when($status, function ($query) use ($status) {

                $query->where('status', $status);

            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.admin-user-management', [
            'users' => $users,
        ]);
    }


    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'owner',
                    'secretary',
                    'cashier',
                ]),
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User account created successfully.');
    }


    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id),
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],

            'role' => [
                'required',
                Rule::in([
                    'admin',
                    'owner',
                    'secretary',
                    'cashier',
                ]),
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],

        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users')
            ->with('success', 'User account updated successfully.');
    }


    /**
     * Activate a user account.
     */
    public function activate(User $user)
    {
        $user->update([
            'status' => 'active',
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User account activated successfully.');
    }


    /**
     * Deactivate a user account.
     */
    public function deactivate(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent the currently logged-in Admin from disabling
        | their own account.
        |--------------------------------------------------------------------------
        */

        if (auth()->id() === $user->id) {

            return redirect()
                ->route('admin.users')
                ->with('error', 'You cannot deactivate your own account.');

        }

        $user->update([
            'status' => 'inactive',
        ]);

        return redirect()
            ->route('admin.users')
            ->with('success', 'User account deactivated successfully.');
    }
}