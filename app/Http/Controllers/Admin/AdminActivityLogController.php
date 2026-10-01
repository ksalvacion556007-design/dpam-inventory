<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    /**
     * Display activity logs.
     */
    public function index(Request $request)
    {
        $search = $request->input('q');
        $module = $request->input('module');
        $action = $request->input('action');
        $userId = $request->input('user_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $logs = ActivityLog::query()
            ->with('user')

            /*
            |--------------------------------------------------------------------------
            | SEARCH
            |--------------------------------------------------------------------------
            */

            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {

                            $userQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");

                        });

                });

            })

            /*
            |--------------------------------------------------------------------------
            | MODULE FILTER
            |--------------------------------------------------------------------------
            */

            ->when($module, function ($query) use ($module) {

                $query->where('module', $module);

            })

            /*
            |--------------------------------------------------------------------------
            | ACTION FILTER
            |--------------------------------------------------------------------------
            */

            ->when($action, function ($query) use ($action) {

                $query->where('action', $action);

            })

            /*
            |--------------------------------------------------------------------------
            | USER FILTER
            |--------------------------------------------------------------------------
            */

            ->when($userId, function ($query) use ($userId) {

                $query->where('user_id', $userId);

            })

            /*
            |--------------------------------------------------------------------------
            | DATE FILTER
            |--------------------------------------------------------------------------
            */

            ->when($dateFrom, function ($query) use ($dateFrom) {

                $query->whereDate('created_at', '>=', $dateFrom);

            })

            ->when($dateTo, function ($query) use ($dateTo) {

                $query->whereDate('created_at', '<=', $dateTo);

            })

            ->latest('created_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | FILTER OPTIONS
        |--------------------------------------------------------------------------
        */

        $users = \App\Models\User::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'username',
                'role',
            ]);


        $modules = ActivityLog::query()
            ->select('module')
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');


        $actions = ActivityLog::query()
            ->select('action')
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalLogs = ActivityLog::count();

        $todayLogs = ActivityLog::whereDate(
            'created_at',
            today()
        )->count();


        return view('admin.admin-activity-logs', [

            'logs' => $logs,

            'users' => $users,

            'modules' => $modules,

            'actions' => $actions,

            'totalLogs' => $totalLogs,

            'todayLogs' => $todayLogs,

        ]);
    }
}