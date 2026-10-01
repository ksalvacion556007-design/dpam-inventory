<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | USER COUNTS
        |--------------------------------------------------------------------------
        */

        $totalUsers = User::count();

        $activeUsers = User::where('status', 'active')->count();

        $inactiveUsers = User::where('status', 'inactive')->count();


        /*
        |--------------------------------------------------------------------------
        | USERS BY ROLE
        |--------------------------------------------------------------------------
        */

        $ownerCount = User::where('role', 'owner')->count();

        $secretaryCount = User::where('role', 'secretary')->count();

        $cashierCount = User::where('role', 'cashier')->count();

        $adminCount = User::where('role', 'admin')->count();


        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOGS
        |--------------------------------------------------------------------------
        |
        | Activity Logs will be connected once the Activity Log
        | module/table is created.
        |
        | For now, the dashboard safely displays zero.
        |
        */

        $recentActivities = collect();

        $activitiesToday = 0;


        /*
        |--------------------------------------------------------------------------
        | RETURN DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('admin.admin-dashboard', [

            'totalUsers' => $totalUsers,

            'activeUsers' => $activeUsers,

            'inactiveUsers' => $inactiveUsers,

            'ownerCount' => $ownerCount,

            'secretaryCount' => $secretaryCount,

            'cashierCount' => $cashierCount,

            'adminCount' => $adminCount,

            'recentActivities' => $recentActivities,

            'activitiesToday' => $activitiesToday,

        ]);
    }
}