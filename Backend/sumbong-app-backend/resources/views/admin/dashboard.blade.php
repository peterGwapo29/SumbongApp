<x-admin-layout>
    <x-slot name="header">Admin Dashboard</x-slot>

    <style>
        /* ── Stat Cards ── */
        .stat-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 22px 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: box-shadow 0.2s, transform 0.2s;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: 12px 12px 0 0;
        }
        .stat-card.blue::after   { background: #2563EB; }
        .stat-card.amber::after  { background: #F59E0B; }
        .stat-card.indigo::after { background: #6366F1; }
        .stat-card.green::after  { background: #10B981; }

        .stat-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }
        .stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #9CA3AF;
        }
        .stat-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon svg { width: 18px; height: 18px; }
        .stat-icon.blue   { background: #EFF6FF; color: #2563EB; }
        .stat-icon.amber  { background: #FFFBEB; color: #D97706; }
        .stat-icon.indigo { background: #EEF2FF; color: #6366F1; }
        .stat-icon.green  { background: #ECFDF5; color: #10B981; }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #111827;
            line-height: 1;
            letter-spacing: -1px;
        }
        .stat-sub {
            font-size: 12px;
            color: #6B7280;
        }

        /* ── Section Cards ── */
        .section-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            overflow: hidden;
        }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 22px;
            border-bottom: 1px solid #F3F4F6;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-link {
            font-size: 12.5px;
            font-weight: 500;
            color: #2563EB;
            text-decoration: none;
            display: flex; align-items: center; gap: 4px;
            transition: gap 0.15s;
        }
        .section-link:hover { gap: 8px; }

        /* ── Secondary stat cards ── */
        .mini-stat {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 22px 24px;
        }
        .mini-stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #9CA3AF;
            margin-bottom: 8px;
        }
        .mini-stat-value {
            font-size: 30px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -1px;
            line-height: 1;
            margin-bottom: 4px;
        }
        .mini-stat-sub {
            font-size: 12px;
            color: #6B7280;
        }

        /* ── Quick Actions ── */
        .quick-action {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            color: #374151;
            transition: background 0.15s, color 0.15s;
            border: 1px solid transparent;
        }
        .quick-action:hover {
            background: #F9FAFB;
            border-color: #E5E7EB;
            color: #111827;
        }
        .quick-action svg { width: 15px; height: 15px; color: #9CA3AF; flex-shrink: 0; }
        .quick-action:hover svg { color: #2563EB; }

        /* ── Table ── */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            padding: 11px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #9CA3AF;
            background: #F9FAFB;
            border-bottom: 1px solid #F3F4F6;
            white-space: nowrap;
        }
        .data-table tbody tr {
            border-bottom: 1px solid #F9FAFB;
            transition: background 0.12s;
        }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: #FAFAFA; }
        .data-table tbody td {
            padding: 13px 16px;
            font-size: 13.5px;
            color: #374151;
            white-space: nowrap;
        }
        .row-id {
            font-size: 12px;
            font-weight: 700;
            color: #9CA3AF;
            font-variant-numeric: tabular-nums;
        }
        .row-title {
            font-weight: 600;
            color: #111827;
            text-decoration: none;
            transition: color 0.15s;
        }
        .row-title:hover { color: #2563EB; }

        /* Status badges */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
        }
        .badge::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.6;
        }
        .badge-pending  { background: #FFFBEB; color: #B45309; }
        .badge-progress { background: #EFF6FF; color: #1D4ED8; }
        .badge-resolved { background: #ECFDF5; color: #065F46; }
        .badge-default  { background: #F3F4F6; color: #4B5563; }

        /* Priority badges */
        .pri-urgent { background: #FEF2F2; color: #B91C1C; }
        .pri-high   { background: #FFF7ED; color: #C2410C; }
        .pri-medium { background: #FFFBEB; color: #92400E; }
        .pri-low    { background: #F0FDF4; color: #166534; }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 56px 24px;
        }
        .empty-icon {
            width: 48px; height: 48px;
            background: #F3F4F6;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            color: #9CA3AF;
        }
        .empty-icon svg { width: 22px; height: 22px; }
        .empty-title { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 4px; }
        .empty-sub   { font-size: 13px; color: #9CA3AF; }

        /* Grid helpers */
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
        @media (max-width: 1100px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            .grid-4, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>

    {{-- ── Row 1: Main stat cards ── --}}
    <div class="grid-4">
        <div class="stat-card blue">
            <div class="stat-top">
                <span class="stat-label">Total Requests</span>
                <div class="stat-icon blue">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['total_requests'] }}</div>
            <div class="stat-sub">All time submissions</div>
        </div>

        <div class="stat-card amber">
            <div class="stat-top">
                <span class="stat-label">Pending</span>
                <div class="stat-icon amber">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['pending_requests'] }}</div>
            <div class="stat-sub">Awaiting action</div>
        </div>

        <div class="stat-card indigo">
            <div class="stat-top">
                <span class="stat-label">In Progress</span>
                <div class="stat-icon indigo">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['in_progress_requests'] }}</div>
            <div class="stat-sub">Being processed</div>
        </div>

        <div class="stat-card green">
            <div class="stat-top">
                <span class="stat-label">Resolved</span>
                <div class="stat-icon green">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ $stats['resolved_requests'] }}</div>
            <div class="stat-sub">Successfully closed</div>
        </div>
    </div>

    {{-- ── Row 2: Users, Service Types, Quick Actions ── --}}
    <div class="grid-3">
        <div class="mini-stat">
            <div class="mini-stat-label">Total Users</div>
            <div class="mini-stat-value">{{ $stats['total_users'] }}</div>
            <div class="mini-stat-sub">
                <span style="color: #10B981; font-weight: 600;">{{ $stats['verified_users'] }} verified</span>
                &nbsp;·&nbsp; {{ $stats['total_users'] - $stats['verified_users'] }} unverified
            </div>
        </div>

        <div class="mini-stat">
            <div class="mini-stat-label">Service Types</div>
            <div class="mini-stat-value">{{ $stats['active_service_types'] }}</div>
            <div class="mini-stat-sub">Active categories</div>
        </div>

        <div class="section-card" style="border-radius:12px;">
            <div class="section-header" style="border-bottom: 1px solid #F3F4F6;">
                <span class="section-title">Quick Actions</span>
            </div>
            <div style="padding: 12px 10px; display: flex; flex-direction: column; gap: 2px;">
                <a href="{{ route('admin.requests.index') }}" class="quick-action">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    View All Requests
                </a>
                <a href="{{ route('admin.users.index') }}" class="quick-action">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Manage Users
                </a>
                <a href="{{ route('admin.service-types.index') }}" class="quick-action">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Service Types
                </a>
            </div>
        </div>
    </div>

    {{-- ── Row 3: Recent Requests table ── --}}
    <div class="section-card">
        <div class="section-header">
            <span class="section-title">Recent Requests</span>
            <a href="{{ route('admin.requests.index') }}" class="section-link">
                View all
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if($stats['recent_requests']->count() > 0)
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>User</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['recent_requests'] as $request)
                            <tr>
                                <td><span class="row-id">#{{ $request->id }}</span></td>
                                <td>
                                    <a href="{{ route('admin.requests.show', $request->id) }}" class="row-title">
                                        {{ Str::limit($request->title, 40) }}
                                    </a>
                                </td>
                                <td style="color: #6B7280;">{{ $request->user->name ?? 'N/A' }}</td>
                                <td style="color: #6B7280;">{{ $request->serviceType->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge
                                        @if($request->status === 'created') badge-pending
                                        @elseif($request->status === 'in_progress') badge-progress
                                        @elseif($request->status === 'resolved') badge-resolved
                                        @else badge-default
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge
                                        @if($request->priority === 'urgent') pri-urgent
                                        @elseif($request->priority === 'high') pri-high
                                        @elseif($request->priority === 'medium') pri-medium
                                        @else pri-low
                                        @endif">
                                        {{ ucfirst($request->priority) }}
                                    </span>
                                </td>
                                <td style="color: #9CA3AF; font-size: 12.5px;">{{ $request->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="empty-title">No requests yet</div>
                <div class="empty-sub">Requests submitted by users will appear here.</div>
            </div>
        @endif
    </div>

</x-admin-layout>