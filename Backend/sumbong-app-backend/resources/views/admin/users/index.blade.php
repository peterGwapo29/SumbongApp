<x-admin-layout>
    <x-slot name="header">Manage Users</x-slot>

    <style>
        /* ── Toolbar ── */
        .page-toolbar {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 20px; gap: 12px; flex-wrap: wrap;
        }
        .result-count { font-size: 13px; color: #6B7280; }
        .result-count strong { color: #111827; font-weight: 700; }

        /* ── Filter bar ── */
        .filter-bar {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 18px;
            display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px; }
        .filter-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.7px; color: #9CA3AF; }

        .filter-input {
            width: 100%; font-size: 13.5px; color: #111827;
            background: #F9FAFB; border: 1px solid #E5E7EB;
            border-radius: 8px; padding: 8px 12px;
            font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .filter-input::placeholder { color: #9CA3AF; }
        .filter-input:focus {
            outline: none; border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            background: #fff;
        }
        .filter-select {
            width: 100%; font-size: 13.5px; color: #111827;
            background: #F9FAFB; border: 1px solid #E5E7EB;
            border-radius: 8px; padding: 8px 12px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239CA3AF' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center;
            padding-right: 30px; cursor: pointer; font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .filter-select:focus {
            outline: none; border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background-color: #fff;
        }
        .filter-actions { display: flex; gap: 8px; align-items: flex-end; }
        .btn-filter {
            padding: 8px 18px; background: #111827; color: #fff;
            border: none; border-radius: 8px;
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; font-family: inherit; transition: background 0.15s;
        }
        .btn-filter:hover { background: #1F2937; }
        .btn-clear {
            padding: 8px 16px; background: #fff; color: #374151;
            border: 1px solid #E5E7EB; border-radius: 8px;
            font-size: 13.5px; font-weight: 500;
            text-decoration: none; cursor: pointer;
            transition: background 0.15s, border-color 0.15s; font-family: inherit;
        }
        .btn-clear:hover { background: #F9FAFB; border-color: #D1D5DB; }

        /* Active filter chips */
        .active-filters { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-bottom: 16px; }
        .active-filters-label { font-size: 12px; color: #9CA3AF; }
        .filter-chip {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; background: #EFF6FF; color: #1D4ED8;
            border-radius: 20px; font-size: 12px; font-weight: 500;
        }

        /* ── Table card ── */
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
        .data-table tbody td { padding: 12px 16px; font-size: 13.5px; color: #374151; white-space: nowrap; }

        /* User cell with avatar */
        .user-cell { display: flex; align-items: center; gap: 10px; }
        .user-mini-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .user-cell-name { font-weight: 600; color: #111827; font-size: 13.5px; }
        .user-cell-id { font-size: 11px; color: #9CA3AF; }

        .row-email { color: #6B7280; font-size: 13px; }
        .row-date  { color: #9CA3AF; font-size: 12.5px; }
        .row-id    { font-size: 12px; font-weight: 700; color: #9CA3AF; }

        /* Role badge */
        .role-badge {
            display: inline-flex; align-items: center;
            padding: 3px 9px; border-radius: 6px;
            font-size: 11.5px; font-weight: 600;
        }
        .role-admin { background: #FEF3C7; color: #92400E; }
        .role-user  { background: #F3F4F6; color: #4B5563; }

        /* Type badge */
        .type-badge {
            display: inline-flex; align-items: center;
            padding: 3px 9px; border-radius: 6px;
            font-size: 11.5px; font-weight: 500;
            background: #F0F9FF; color: #0369A1;
        }

        /* Verified badge */
        .badge-verified     { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 600; background: #ECFDF5; color: #065F46; }
        .badge-unverified   { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 600; background: #F3F4F6; color: #6B7280; }
        .badge-verified::before, .badge-unverified::before {
            content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; opacity: 0.6;
        }

        /* Action button */
        .action-btn {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 5px 12px; border-radius: 6px;
            font-size: 12px; font-weight: 500; text-decoration: none;
            transition: background 0.12s, color 0.12s;
            color: #2563EB; background: #EFF6FF;
        }
        .action-btn:hover { background: #DBEAFE; color: #1D4ED8; }
        .action-btn svg { width: 13px; height: 13px; }

        /* Empty state */
        .empty-state { text-align: center; padding: 56px 24px; }
        .empty-icon { width: 48px; height: 48px; background: #F3F4F6; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; color: #9CA3AF; }
        .empty-icon svg { width: 22px; height: 22px; }
        .empty-title { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 4px; }
        .empty-sub   { font-size: 13px; color: #9CA3AF; }

        .pagination-wrap { padding: 14px 20px; border-top: 1px solid #F3F4F6; }
    </style>

    {{-- Toolbar --}}
    <div class="page-toolbar">
        @if($users->count() > 0)
            <span class="result-count">
                Showing <strong>{{ $users->firstItem() }}–{{ $users->lastItem() }}</strong>
                of <strong>{{ $users->total() }}</strong> users
            </span>
        @else
            <span></span>
        @endif
        <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex; align-items:center; gap:8px;">
            @foreach(request()->except(['per_page', 'page']) as $key => $value)
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

    {{-- Active filter chips --}}
    @if(request('search') || request('user_type') || request('verified') !== null && request('verified') !== '')
        <div class="active-filters">
            <span class="active-filters-label">Filtered by:</span>
            @if(request('search'))
                <span class="filter-chip">Search: "{{ request('search') }}"</span>
            @endif
            @if(request('user_type'))
                <span class="filter-chip">Type: {{ ucfirst(str_replace('_',' ', request('user_type'))) }}</span>
            @endif
            @if(request('verified') !== null && request('verified') !== '')
                <span class="filter-chip">{{ request('verified') == '1' ? 'Verified only' : 'Unverified only' }}</span>
            @endif
        </div>
    @endif

    {{-- Filter bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.users.index') }}"
              style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; width:100%;">
            <div class="filter-group" style="flex: 2; min-width: 200px;">
                <label class="filter-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Name or email…" class="filter-input">
            </div>
            <div class="filter-group">
                <label class="filter-label">User Type</label>
                <select name="user_type" class="filter-select">
                    <option value="">All types</option>
                    <option value="resident"     {{ request('user_type') == 'resident'     ? 'selected' : '' }}>Resident</option>
                    <option value="non_resident" {{ request('user_type') == 'non_resident' ? 'selected' : '' }}>Non-Resident</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Verified</label>
                <select name="verified" class="filter-select">
                    <option value="">All</option>
                    <option value="1" {{ request('verified') == '1' ? 'selected' : '' }}>Verified</option>
                    <option value="0" {{ request('verified') == '0' ? 'selected' : '' }}>Not Verified</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">Apply</button>
                <a href="{{ route('admin.users.index') }}" class="btn-clear">Clear</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-card">
        @if($users->count() > 0)
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Type</th>
                            <th>Verified</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-mini-avatar">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="user-cell-name">{{ $user->name }}</div>
                                            <div class="user-cell-id">#{{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="row-email">{{ $user->email }}</td>
                                <td>
                                    @if($user->role?->name === 'admin')
                                        <span class="role-badge role-admin">{{ $user->role->name }}</span>
                                    @else
                                        <span class="role-badge role-user">{{ $user->role->name ?? 'N/A' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="type-badge">{{ ucfirst(str_replace('_', ' ', $user->user_type)) }}</span>
                                </td>
                                <td>
                                    @if($user->verified)
                                        <span class="badge-verified">Verified</span>
                                    @else
                                        <span class="badge-unverified">Not Verified</span>
                                    @endif
                                </td>
                                <td class="row-date">{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="action-btn">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">
                {{ $users->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="empty-title">No users found</div>
                <div class="empty-sub">Try adjusting your search or filters.</div>
            </div>
        @endif
    </div>

</x-admin-layout>