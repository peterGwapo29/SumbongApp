<x-admin-layout>
    <x-slot name="header">Requests</x-slot>

    <style>
        /* ── Toolbar ── */
        .page-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
            flex-wrap: wrap;
        }
        .page-toolbar-left {
            display: flex; align-items: center; gap: 10px;
        }
        .result-count {
            font-size: 13px;
            color: #6B7280;
        }
        .result-count strong { color: #111827; font-weight: 700; }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 16px;
            background: #111827;
            color: #fff;
            border: none; border-radius: 9px;
            font-size: 13.5px; font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            font-family: inherit;
        }
        .btn-primary:hover { background: #1F2937; transform: translateY(-1px); }
        .btn-primary svg { width: 15px; height: 15px; }

        /* ── Alert ── */
        .alert-success {
            display: flex; align-items: center; gap: 10px;
            background: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #065F46;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13.5px; font-weight: 500;
            margin-bottom: 18px;
        }
        .alert-success svg { width: 16px; height: 16px; flex-shrink: 0; color: #10B981; }

        /* ── Filter bar ── */
        .filter-bar {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 18px;
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }
        .filter-group {
            display: flex; flex-direction: column; gap: 5px;
            flex: 1; min-width: 140px;
        }
        .filter-label {
            font-size: 11px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.7px;
            color: #9CA3AF;
        }
        .filter-select {
            width: 100%;
            font-size: 13.5px;
            color: #111827;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 8px 12px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239CA3AF' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 30px;
            cursor: pointer;
            font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .filter-select:focus {
            outline: none;
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            background-color: #fff;
        }
        .filter-actions {
            display: flex; gap: 8px; align-items: flex-end;
        }
        .btn-filter {
            padding: 8px 18px;
            background: #111827; color: #fff;
            border: none; border-radius: 8px;
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; font-family: inherit;
            transition: background 0.15s;
        }
        .btn-filter:hover { background: #1F2937; }
        .btn-clear {
            padding: 8px 16px;
            background: #fff; color: #374151;
            border: 1px solid #E5E7EB; border-radius: 8px;
            font-size: 13.5px; font-weight: 500;
            text-decoration: none; cursor: pointer;
            transition: background 0.15s, border-color 0.15s;
            font-family: inherit;
        }
        .btn-clear:hover { background: #F9FAFB; border-color: #D1D5DB; }

        /* Active filter chips */
        .active-filters {
            display: flex; align-items: center; gap: 6px;
            flex-wrap: wrap; margin-bottom: 16px;
        }
        .filter-chip {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px;
            background: #EFF6FF; color: #1D4ED8;
            border-radius: 20px; font-size: 12px; font-weight: 500;
        }
        .active-filters-label { font-size: 12px; color: #9CA3AF; }

        /* ── Table card ── */
        .table-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            overflow: hidden;
        }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            padding: 11px 16px;
            text-align: left;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.7px;
            color: #9CA3AF;
            background: #F9FAFB;
            border-bottom: 1px solid #F3F4F6;
            white-space: nowrap;
        }
        .data-table tbody tr {
            border-bottom: 1px solid #F9FAFB;
            transition: background 0.1s;
        }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: #FAFAFA; }
        .data-table tbody td {
            padding: 13px 16px;
            font-size: 13.5px; color: #374151;
            white-space: nowrap;
        }

        .row-id { font-size: 12px; font-weight: 700; color: #9CA3AF; }
        .row-title {
            font-weight: 600; color: #111827;
            text-decoration: none; transition: color 0.15s;
        }
        .row-title:hover { color: #2563EB; }
        .row-user { color: #6B7280; font-size: 13px; }
        .row-service { color: #6B7280; font-size: 13px; }
        .row-date { color: #9CA3AF; font-size: 12.5px; }

        /* Badges */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 20px;
            font-size: 11.5px; font-weight: 600;
        }
        .badge::before {
            content: ''; width: 5px; height: 5px;
            border-radius: 50%; background: currentColor; opacity: 0.6;
        }
        .badge-created  { background: #FFFBEB; color: #B45309; }
        .badge-assigned { background: #F5F3FF; color: #6D28D9; }
        .badge-progress { background: #EFF6FF; color: #1D4ED8; }
        .badge-resolved { background: #ECFDF5; color: #065F46; }
        .badge-closed   { background: #F3F4F6; color: #4B5563; }

        .pri-urgent { background: #FEF2F2; color: #B91C1C; }
        .pri-high   { background: #FFF7ED; color: #C2410C; }
        .pri-medium { background: #FFFBEB; color: #92400E; }
        .pri-low    { background: #F0FDF4; color: #166534; }

        /* Action buttons */
        .action-btns { display: flex; align-items: center; gap: 4px; }
        .action-btn {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px; font-weight: 500;
            text-decoration: none;
            transition: background 0.12s, color 0.12s;
        }
        .action-btn svg { width: 13px; height: 13px; }
        .action-btn-view {
            color: #2563EB; background: #EFF6FF;
        }
        .action-btn-view:hover { background: #DBEAFE; color: #1D4ED8; }
        .action-btn-edit {
            color: #374151; background: #F3F4F6;
        }
        .action-btn-edit:hover { background: #E5E7EB; color: #111827; }

        /* Empty state */
        .empty-state { text-align: center; padding: 56px 24px; }
        .empty-icon {
            width: 48px; height: 48px;
            background: #F3F4F6; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px; color: #9CA3AF;
        }
        .empty-icon svg { width: 22px; height: 22px; }
        .empty-title { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 4px; }
        .empty-sub   { font-size: 13px; color: #9CA3AF; }

        /* Pagination */
        .pagination-wrap { padding: 14px 20px; border-top: 1px solid #F3F4F6; }
    </style>

    {{-- Toolbar --}}
    <div class="page-toolbar">
        <div class="page-toolbar-left">
            @if($requests->count() > 0)
                <span class="result-count">
                    Showing <strong>{{ $requests->firstItem() }}–{{ $requests->lastItem() }}</strong>
                    of <strong>{{ $requests->total() }}</strong> requests
                </span>
            @endif
        </div>
        <form method="GET" action="{{ route('admin.requests.index') }}" style="display:flex; align-items:center; gap:8px;">
            @foreach(request()->except(['per_page','page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <label class="filter-label" style="margin:0;">Rows per page</label>
            <select name="per_page" class="filter-select" style="width:auto; min-width:80px;" onchange="this.form.submit()">
                @foreach([10,25,50,100] as $size)
                    <option value="{{ $size }}" {{ (int)request('per_page', 10) === $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Success alert --}}
    @if(session('success'))
        <div class="alert-success">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Active filter chips --}}
    @if(request('status') || request('service_type_id') || request('priority'))
        <div class="active-filters">
            <span class="active-filters-label">Filtered by:</span>
            @if(request('status'))
                <span class="filter-chip">Status: {{ ucfirst(str_replace('_',' ',request('status'))) }}</span>
            @endif
            @if(request('service_type_id'))
                <span class="filter-chip">Service: {{ $serviceTypes->find(request('service_type_id'))?->name }}</span>
            @endif
            @if(request('priority'))
                <span class="filter-chip">Priority: {{ ucfirst(request('priority')) }}</span>
            @endif
        </div>
    @endif

    {{-- Filter bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.requests.index') }}"
              style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; width:100%;">
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-select">
                    <option value="">All statuses</option>
                    <option value="created"     {{ request('status') == 'created'     ? 'selected' : '' }}>Created</option>
                    <option value="assigned"    {{ request('status') == 'assigned'    ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved"    {{ request('status') == 'resolved'    ? 'selected' : '' }}>Resolved</option>
                    <option value="closed"      {{ request('status') == 'closed'      ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Service Type</label>
                <select name="service_type_id" class="filter-select">
                    <option value="">All types</option>
                    @foreach($serviceTypes as $serviceType)
                        <option value="{{ $serviceType->id }}" {{ request('service_type_id') == $serviceType->id ? 'selected' : '' }}>
                            {{ $serviceType->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Priority</label>
                <select name="priority" class="filter-select">
                    <option value="">All priorities</option>
                    <option value="low"    {{ request('priority') == 'low'    ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high"   {{ request('priority') == 'high'   ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">Apply</button>
                <a href="{{ route('admin.requests.index') }}" class="btn-clear">Clear</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-card">
        @if($requests->count() > 0)
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td><span class="row-id">#{{ $request->id }}</span></td>
                                <td>
                                    <a href="{{ route('admin.requests.show', $request->id) }}" class="row-title">
                                        {{ Str::limit($request->title, 40) }}
                                    </a>
                                </td>
                                <td class="row-user">{{ $request->user->name ?? 'N/A' }}</td>
                                <td class="row-service">{{ $request->serviceType->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge
                                        @if($request->status === 'created')     badge-created
                                        @elseif($request->status === 'assigned') badge-assigned
                                        @elseif($request->status === 'in_progress') badge-progress
                                        @elseif($request->status === 'resolved') badge-resolved
                                        @else badge-closed
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
                                <td class="row-date">{{ $request->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="action-btns">
                                        <a href="{{ route('admin.requests.show', $request->id) }}" class="action-btn action-btn-view">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </a>
                                        <a href="{{ route('admin.requests.edit', $request->id) }}" class="action-btn action-btn-edit">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">
                {{ $requests->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="empty-title">No requests found</div>
                <div class="empty-sub">Try adjusting your filters.</div>
            </div>
        @endif
    </div>

</x-admin-layout>