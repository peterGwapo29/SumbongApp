<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Models\User;
use App\Models\ServiceType;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_requests' => Request::count(),
            'pending_requests' => Request::where('status', 'created')->count(),
            'in_progress_requests' => Request::where('status', 'in_progress')->count(),
            'resolved_requests' => Request::where('status', 'resolved')->count(),
            'total_users' => User::count(),
            'verified_users' => User::where('verified', true)->count(),
            'active_service_types' => ServiceType::where('is_active', true)->count(),
            'requests_by_status' => Request::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status'),
            'requests_by_priority' => Request::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'recent_requests' => Request::with(['user', 'serviceType'])
                ->latest()
                ->limit(10)
                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

