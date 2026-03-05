<x-admin-layout>
    <x-slot name="header">Service Types</x-slot>

    <style>
        .page-toolbar {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 20px; gap: 12px; flex-wrap: wrap;
        }
        .result-count { font-size: 13px; color: #6B7280; }
        .result-count strong { color: #111827; font-weight: 700; }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 16px; background: #111827; color: #fff;
            border: none; border-radius: 9px;
            font-size: 13.5px; font-weight: 600;
            text-decoration: none; cursor: pointer;
            transition: background 0.15s, transform 0.1s; font-family: inherit;
        }
        .btn-primary:hover { background: #1F2937; transform: translateY(-1px); }
        .btn-primary svg { width: 15px; height: 15px; }

        /* Alert */
        .alert-success {
            display: flex; align-items: center; gap: 10px;
            background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46;
            border-radius: 10px; padding: 12px 16px;
            font-size: 13.5px; font-weight: 500; margin-bottom: 18px;
        }
        .alert-success svg { width: 16px; height: 16px; flex-shrink: 0; color: #10B981; }

        /* Table card */
        .table-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            padding: 11px 16px; text-align: left;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.7px;
            color: #9CA3AF; background: #F9FAFB;
            border-bottom: 1px solid #F3F4F6; white-space: nowrap;
        }
        .data-table tbody tr { border-bottom: 1px solid #F9FAFB; transition: background 0.1s; }
        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: #FAFAFA; }
        .data-table tbody td { padding: 14px 16px; font-size: 13.5px; color: #374151; }

        /* Service name cell */
        .service-cell { display: flex; align-items: center; gap: 12px; }
        .service-icon-wrap {
            width: 38px; height: 38px; border-radius: 10px;
            background: #F3F4F6;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
            border: 1px solid #E5E7EB;
        }
        .service-name { font-weight: 600; color: #111827; font-size: 14px; }
        .service-id   { font-size: 11px; color: #9CA3AF; }

        .dept-badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 6px;
            font-size: 12px; font-weight: 500;
            background: #F0F9FF; color: #0369A1;
            white-space: nowrap;
        }

        .desc-text { color: #6B7280; font-size: 13px; max-width: 280px; white-space: normal; line-height: 1.4; }

        .badge-active   { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 600; background: #ECFDF5; color: #065F46; }
        .badge-inactive { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 600; background: #F3F4F6; color: #6B7280; }
        .badge-active::before, .badge-inactive::before {
            content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; opacity: 0.7;
        }

        /* Action buttons */
        .action-btns { display: flex; align-items: center; gap: 4px; }
        .action-btn {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 5px 10px; border-radius: 6px;
            font-size: 12px; font-weight: 500; text-decoration: none;
            transition: background 0.12s, color 0.12s;
            border: none; cursor: pointer; font-family: inherit;
        }
        .action-btn svg { width: 13px; height: 13px; }
        .action-btn-edit  { color: #374151; background: #F3F4F6; }
        .action-btn-edit:hover { background: #E5E7EB; color: #111827; }
        .action-btn-delete { color: #DC2626; background: #FEF2F2; }
        .action-btn-delete:hover { background: #FEE2E2; color: #B91C1C; }

        /* Empty state */
        .empty-state { text-align: center; padding: 56px 24px; }
        .empty-icon { width: 48px; height: 48px; background: #F3F4F6; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; color: #9CA3AF; }
        .empty-icon svg { width: 22px; height: 22px; }
        .empty-title { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 4px; }
        .empty-sub   { font-size: 13px; color: #9CA3AF; margin-bottom: 20px; }

        .pagination-wrap { padding: 14px 20px; border-top: 1px solid #F3F4F6; }
    </style>

    {{-- Toolbar --}}
    <div class="page-toolbar">
        @if($serviceTypes->count() > 0)
            <span class="result-count">
                <strong>{{ $serviceTypes->total() }}</strong> service {{ Str::plural('type', $serviceTypes->total()) }}
            </span>
        @else
            <span></span>
        @endif
        <div style="display:flex; align-items:center; gap:8px;">
            <form method="GET" action="{{ route('admin.service-types.index') }}" style="display:flex; align-items:center; gap:8px; margin-right:8px;">
                @foreach(request()->except(['search','status','per_page','page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search services…"
                    class="filter-input"
                    style="width:180px;"
                >
                <select name="status" class="filter-select" style="width:130px;">
                    <option value="">All status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="btn-filter">Apply</button>
            </form>
            <form method="GET" action="{{ route('admin.service-types.index') }}" style="display:flex; align-items:center; gap:6px;">
                @foreach(request()->except(['per_page','page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <label class="result-count" style="margin:0;">Rows</label>
                <select name="per_page" class="filter-select" style="width:80px;" onchange="this.form.submit()">
                    @foreach([10,25,50,100] as $size)
                        <option value="{{ $size }}" {{ (int)request('per_page', 10) === $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.service-types.create') }}" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create New
            </a>
        </div>
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

    {{-- Table --}}
    <div class="table-card">
        @if($serviceTypes->count() > 0)
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Department</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($serviceTypes as $serviceType)
                            <tr>
                                <td>
                                    <div class="service-cell">
                                        <div class="service-icon-wrap">
                                            {{ $serviceType->icon ?? '📋' }}
                                        </div>
                                        <div>
                                            <div class="service-name">{{ $serviceType->name }}</div>
                                            <div class="service-id">#{{ $serviceType->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="dept-badge">{{ $serviceType->department ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="desc-text">{{ Str::limit($serviceType->description, 60) }}</span>
                                </td>
                                <td>
                                    @if($serviceType->is_active)
                                        <span class="badge-active">Active</span>
                                    @else
                                        <span class="badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="{{ route('admin.service-types.edit', $serviceType->id) }}" class="action-btn action-btn-edit">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.service-types.destroy', $serviceType->id) }}"
                                              style="display:inline;"
                                              onsubmit="return confirm('Delete \'{{ $serviceType->name }}\'? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn action-btn-delete">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">
                {{ $serviceTypes->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="empty-title">No service types yet</div>
                <div class="empty-sub">Create your first service type to get started.</div>
                <a href="{{ route('admin.service-types.create') }}" class="btn-primary" style="display:inline-flex;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create First Service Type
                </a>
            </div>
        @endif
    </div>

</x-admin-layout>