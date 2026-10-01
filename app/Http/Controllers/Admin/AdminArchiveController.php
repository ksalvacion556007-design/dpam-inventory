<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminArchiveController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');
        $type = $request->input('type');
        $year = $request->input('year');

        /*
        |--------------------------------------------------------------------------
        | Inactive User Accounts
        |--------------------------------------------------------------------------
        */

        $inactiveUsers = User::query()
            ->where('status', 'inactive')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($year, function ($query) use ($year) {
                $query->whereYear('updated_at', $year);
            })
            ->orderByDesc('updated_at')
            ->paginate(15, ['*'], 'users_page')
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Historical Activity Logs
        |--------------------------------------------------------------------------
        */

        $activityLogs = ActivityLog::query()
            ->with('user')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        });
                });
            })
            ->when($year, function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            })
            ->latest('created_at')
            ->latest('id')
            ->paginate(20, ['*'], 'activity_page')
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Historical Record Count
        |--------------------------------------------------------------------------
        */

        $historicalCount = ActivityLog::count();

        return view('admin.admin-archive', [
            'inactiveUsers' => $inactiveUsers,
            'activityLogs' => $activityLogs,
            'historicalCount' => $historicalCount,
        ]);
    }
}