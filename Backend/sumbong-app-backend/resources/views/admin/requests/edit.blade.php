<x-admin-layout>
    <x-slot name="header">Edit Request #{{ $requestModel->id }}</x-slot>

    <style>
        .edit-grid {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 20px;
            align-items: start;
        }
        @media (max-width: 1024px) { .edit-grid { grid-template-columns: 1fr; } }

        /* Cards */
        .form-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
        .form-card:last-child { margin-bottom: 0; }
        .form-card-header { display: flex; align-items: center; gap: 10px; padding: 16px 20px; border-bottom: 1px solid #F3F4F6; }
        .form-card-icon { width: 30px; height: 30px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .form-card-title { font-size: 13.5px; font-weight: 700; color: #111827; }
        .form-card-body { padding: 20px; }

        /* Fields */
        .form-row { display: grid; gap: 14px; margin-bottom: 14px; }
        .form-row-2 { grid-template-columns: 1fr 1fr; }
        .form-row-3 { grid-template-columns: 1fr 1fr 1fr; }
        @media (max-width: 700px) { .form-row-2, .form-row-3 { grid-template-columns: 1fr; } }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group:last-child { margin-bottom: 0; }

        .form-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: #6B7280; }
        .form-label span { color: #EF4444; margin-left: 2px; }
        .form-input, .form-select, .form-textarea {
            font-size: 13.5px; color: #111827;
            background: #F9FAFB; border: 1px solid #E5E7EB;
            border-radius: 8px; padding: 9px 12px;
            font-family: inherit; box-sizing: border-box; width: 100%;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        }
        .form-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239CA3AF' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 30px; cursor: pointer; }
        .form-textarea { resize: vertical; min-height: 90px; line-height: 1.6; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fff; }
        .form-error { font-size: 12px; color: #DC2626; }
        .form-hint  { font-size: 12px; color: #9CA3AF; }

        /* Breadcrumb */
        .breadcrumb { display: flex; align-items: center; gap: 6px; margin-bottom: 20px; font-size: 13px; color: #9CA3AF; }
        .breadcrumb a { color: #6B7280; text-decoration: none; font-weight: 500; transition: color 0.15s; }
        .breadcrumb a:hover { color: #111827; }
        .breadcrumb svg { width: 14px; height: 14px; }

        /* Alert */
        .alert-success { display: flex; align-items: center; gap: 10px; background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; border-radius: 10px; padding: 12px 16px; font-size: 13.5px; font-weight: 500; margin-bottom: 18px; }
        .alert-success svg { width: 16px; height: 16px; color: #10B981; flex-shrink: 0; }

        /* Sidebar */
        .side-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; margin-bottom: 16px; position: sticky; top: 80px; }
        .side-card:last-child { margin-bottom: 0; }
        .side-card-header { padding: 14px 18px; border-bottom: 1px solid #F3F4F6; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: #9CA3AF; }
        .side-card-body { padding: 16px 18px; display: flex; flex-direction: column; gap: 10px; }

        /* Meta info */
        .meta-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #F9FAFB; font-size: 13px; }
        .meta-row:last-child { border-bottom: none; }
        .meta-label { color: #9CA3AF; font-weight: 500; font-size: 12px; }
        .meta-value { color: #374151; font-weight: 600; font-size: 12.5px; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 600; }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; opacity: 0.6; }
        .badge-created  { background: #FFFBEB; color: #B45309; }
        .badge-assigned { background: #F5F3FF; color: #6D28D9; }
        .badge-progress { background: #EFF6FF; color: #1D4ED8; }
        .badge-resolved { background: #ECFDF5; color: #065F46; }
        .badge-closed   { background: #F3F4F6; color: #4B5563; }

        /* Action buttons */
        .btn-save { width: 100%; padding: 10px; background: #111827; color: #fff; border: none; border-radius: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer; font-family: inherit; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.15s; }
        .btn-save:hover { background: #1F2937; }
        .btn-save svg { width: 15px; height: 15px; }
        .btn-cancel { display: flex; align-items: center; justify-content: center; width: 100%; padding: 9px; background: #fff; color: #374151; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: background 0.15s; }
        .btn-cancel:hover { background: #F9FAFB; }
    </style>

    @if(session('success'))
        <div class="alert-success">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Breadcrumb --}}
    <nav class="breadcrumb">
        <a href="{{ route('admin.requests.index') }}">Requests</a>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('admin.requests.show', $requestModel->id) }}">#{{ $requestModel->id }}</a>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>Edit</span>
    </nav>

    <form action="{{ route('admin.requests.update', $requestModel->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="edit-grid">

            {{-- ── Left: Form sections ── --}}
            <div>

                {{-- Request Info --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">📋</div>
                        <span class="form-card-title">Request Information</span>
                    </div>
                    <div class="form-card-body">
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label">User <span>*</span></label>
                                <select name="user_id" required class="form-select">
                                    <option value="">Select user…</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id', $requestModel->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Service Type <span>*</span></label>
                                <select name="service_type_id" required class="form-select">
                                    <option value="">Select type…</option>
                                    @foreach($serviceTypes as $serviceType)
                                        <option value="{{ $serviceType->id }}" {{ old('service_type_id', $requestModel->service_type_id) == $serviceType->id ? 'selected' : '' }}>
                                            {{ $serviceType->name }} — {{ $serviceType->department }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_type_id')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 14px;">
                            <label class="form-label">Title <span>*</span></label>
                            <input type="text" name="title" value="{{ old('title', $requestModel->title) }}" required class="form-input" placeholder="Brief description of the issue" />
                            @error('title')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description <span>*</span></label>
                            <textarea name="description" rows="4" required class="form-textarea" placeholder="Detailed information…">{{ old('description', $requestModel->description) }}</textarea>
                            @error('description')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Location --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">📍</div>
                        <span class="form-card-title">Location</span>
                    </div>
                    <div class="form-card-body">
                        <div class="form-group" style="margin-bottom: 14px;">
                            <label class="form-label">Address <span>*</span></label>
                            <input type="text" name="address" value="{{ old('address', $requestModel->address) }}" required class="form-input" placeholder="Street address" />
                            @error('address')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label">Barangay</label>
                                <input type="text" name="barangay" value="{{ old('barangay', $requestModel->barangay) }}" class="form-input" placeholder="Barangay" />
                                @error('barangay')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" name="city" value="{{ old('city', $requestModel->city) }}" class="form-input" placeholder="City" />
                                @error('city')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label">Latitude</label>
                                <input type="number" step="any" name="latitude" value="{{ old('latitude', $requestModel->latitude) }}" class="form-input" placeholder="e.g. 7.1907" />
                                @error('latitude')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Longitude</label>
                                <input type="number" step="any" name="longitude" value="{{ old('longitude', $requestModel->longitude) }}" class="form-input" placeholder="e.g. 125.4553" />
                                @error('longitude')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status & Assignment --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">⚡</div>
                        <span class="form-card-title">Status & Assignment</span>
                    </div>
                    <div class="form-card-body">
                        <div class="form-row form-row-2">
                            <div class="form-group">
                                <label class="form-label">Status <span>*</span></label>
                                <select name="status" required class="form-select">
                                    @foreach(['created','assigned','in_progress','resolved','closed'] as $s)
                                        <option value="{{ $s }}" {{ old('status', $requestModel->status) == $s ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-group">
                                <label class="form-label">Priority <span>*</span></label>
                                <select name="priority" required class="form-select">
                                    @foreach(['low','medium','high','urgent'] as $p)
                                        <option value="{{ $p }}" {{ old('priority', $requestModel->priority) == $p ? 'selected' : '' }}>
                                            {{ ucfirst($p) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('priority')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 14px;">
                            <label class="form-label">Assign to Staff</label>
                            <select name="assignee_id" class="form-select">
                                <option value="">No Assignment</option>
                                @foreach($staff as $staffMember)
                                    <option value="{{ $staffMember->id }}" {{ old('assignee_id', $requestModel->assignments->first()?->user_id) == $staffMember->id ? 'selected' : '' }}>
                                        {{ $staffMember->name }} — {{ $staffMember->role->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assignee_id')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status Change Notes</label>
                            <textarea name="status_notes" rows="2" class="form-textarea" placeholder="Optional note about this update…">{{ old('status_notes') }}</textarea>
                            @error('status_notes')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Right sidebar ── --}}
            <div>
                {{-- Current info --}}
                <div class="side-card">
                    <div class="side-card-header">Current State</div>
                    <div class="side-card-body">
                        <div>
                            <div class="meta-row">
                                <span class="meta-label">Request ID</span>
                                <span class="meta-value">#{{ $requestModel->id }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Status</span>
                                <span class="badge
                                    @if($requestModel->status === 'created')     badge-created
                                    @elseif($requestModel->status === 'assigned') badge-assigned
                                    @elseif($requestModel->status === 'in_progress') badge-progress
                                    @elseif($requestModel->status === 'resolved') badge-resolved
                                    @else badge-closed
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $requestModel->status)) }}
                                </span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Created</span>
                                <span class="meta-value">{{ $requestModel->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Updated</span>
                                <span class="meta-value">{{ $requestModel->updated_at->format('M d, Y') }}</span>
                            </div>
                            <div class="meta-row">
                                <span class="meta-label">Submitted by</span>
                                <span class="meta-value">{{ $requestModel->user->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="side-card">
                    <div class="side-card-header">Save</div>
                    <div class="side-card-body">
                        <button type="submit" class="btn-save">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Request
                        </button>
                        <a href="{{ route('admin.requests.show', $requestModel->id) }}" class="btn-cancel">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>

</x-admin-layout>