<x-admin-layout>
    <x-slot name="header">Request #{{ $requestModel->id }}</x-slot>

    <style>
        /* ── Layout ── */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            align-items: start;
        }
        @media (max-width: 1024px) { .detail-grid { grid-template-columns: 1fr; } }

        /* ── Breadcrumb + toolbar ── */
        .page-toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #9CA3AF; }
        .breadcrumb a { color: #6B7280; text-decoration: none; font-weight: 500; transition: color 0.15s; }
        .breadcrumb a:hover { color: #111827; }
        .breadcrumb svg { width: 14px; height: 14px; }

        /* ── Cards ── */
        .detail-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; margin-bottom: 16px; }
        .detail-card:last-child { margin-bottom: 0; }
        .detail-card-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #F3F4F6; }
        .detail-card-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: #9CA3AF; }
        .detail-card-body { padding: 20px; }

        /* ── Alert ── */
        .alert-success { display: flex; align-items: center; gap: 10px; background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; border-radius: 10px; padding: 12px 16px; font-size: 13.5px; font-weight: 500; margin-bottom: 18px; }
        .alert-success svg { width: 16px; height: 16px; color: #10B981; flex-shrink: 0; }

        /* ── Request overview ── */
        .req-title { font-size: 20px; font-weight: 800; color: #111827; letter-spacing: -0.3px; margin-bottom: 4px; }
        .req-meta  { font-size: 12.5px; color: #9CA3AF; margin-bottom: 16px; }
        .req-desc  { font-size: 14px; color: #374151; line-height: 1.7; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #F3F4F6; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .info-item {}
        .info-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; color: #9CA3AF; margin-bottom: 4px; }
        .info-value { font-size: 13.5px; font-weight: 600; color: #111827; }
        .info-sub   { font-size: 12px; color: #6B7280; margin-top: 1px; }
        .info-full  { grid-column: 1 / -1; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; opacity: 0.6; }
        .badge-created  { background: #FFFBEB; color: #B45309; }
        .badge-assigned { background: #F5F3FF; color: #6D28D9; }
        .badge-progress { background: #EFF6FF; color: #1D4ED8; }
        .badge-resolved { background: #ECFDF5; color: #065F46; }
        .badge-closed   { background: #F3F4F6; color: #4B5563; }
        .pri-urgent { background: #FEF2F2; color: #B91C1C; }
        .pri-high   { background: #FFF7ED; color: #C2410C; }
        .pri-medium { background: #FFFBEB; color: #92400E; }
        .pri-low    { background: #F0FDF4; color: #166534; }

        /* ── Attachments ── */
        .attachment-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
        .attachment-item { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 12px; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 10px; text-decoration: none; transition: background 0.15s, border-color 0.15s; }
        .attachment-item:hover { background: #EFF6FF; border-color: #BFDBFE; }
        .attachment-icon { width: 36px; height: 36px; border-radius: 8px; background: #E0E7FF; display: flex; align-items: center; justify-content: center; color: #4F46E5; }
        .attachment-icon svg { width: 18px; height: 18px; }
        .attachment-name { font-size: 12px; font-weight: 600; color: #374151; text-align: center; word-break: break-all; }
        .attachment-meta { font-size: 11px; color: #9CA3AF; }

        /* ── Timeline ── */
        .timeline { position: relative; padding-left: 24px; }
        .timeline::before { content: ''; position: absolute; left: 7px; top: 6px; bottom: 6px; width: 2px; background: #F3F4F6; }
        .timeline-item { position: relative; margin-bottom: 20px; }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-dot {
            position: absolute; left: -24px; top: 3px;
            width: 16px; height: 16px; border-radius: 50%;
            background: #fff; border: 2px solid #E5E7EB;
            display: flex; align-items: center; justify-content: center;
        }
        .timeline-dot-inner { width: 6px; height: 6px; border-radius: 50%; }
        .dot-created  { background: #F59E0B; }
        .dot-assigned { background: #8B5CF6; }
        .dot-progress { background: #3B82F6; }
        .dot-resolved { background: #10B981; }
        .dot-closed   { background: #6B7280; }
        .timeline-status { font-size: 13.5px; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .timeline-time   { font-size: 12px; color: #9CA3AF; }
        .timeline-notes  { font-size: 13px; color: #6B7280; margin-top: 5px; padding: 8px 10px; background: #F9FAFB; border-radius: 6px; border-left: 3px solid #E5E7EB; }

        /* ── Feedback ── */
        .feedback-item { padding: 14px; background: #F9FAFB; border: 1px solid #F3F4F6; border-radius: 10px; margin-bottom: 10px; }
        .feedback-item:last-child { margin-bottom: 0; }
        .feedback-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .feedback-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #2563EB, #7C3AED); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0; }
        .feedback-name { font-size: 13.5px; font-weight: 600; color: #111827; }
        .feedback-date { font-size: 11.5px; color: #9CA3AF; }
        .feedback-text { font-size: 13.5px; color: #374151; line-height: 1.6; }
        .stars { display: flex; align-items: center; gap: 3px; margin-top: 8px; }
        .stars svg { width: 14px; height: 14px; }
        .stars span { font-size: 12px; color: #6B7280; margin-left: 4px; }

        /* ── Sidebar cards ── */
        .side-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; margin-bottom: 16px; position: sticky; top: 80px; }
        .side-card:last-child { margin-bottom: 0; }
        .side-card-header { padding: 14px 18px; border-bottom: 1px solid #F3F4F6; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: #9CA3AF; }
        .side-card-body { padding: 16px 18px; }

        /* Form fields */
        .form-label { display: block; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; color: #6B7280; margin-bottom: 5px; }
        .form-select, .form-textarea {
            width: 100%; font-size: 13.5px; color: #111827;
            background: #F9FAFB; border: 1px solid #E5E7EB;
            border-radius: 8px; padding: 9px 12px;
            font-family: inherit; box-sizing: border-box;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%239CA3AF' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; padding-right: 30px; cursor: pointer; }
        .form-select:focus, .form-textarea:focus { outline: none; border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background: #fff; }
        .form-textarea { resize: vertical; min-height: 72px; line-height: 1.5; }
        .form-group { margin-bottom: 12px; }
        .form-group:last-of-type { margin-bottom: 0; }

        /* Buttons */
        .btn-primary { width: 100%; padding: 10px; background: #111827; color: #fff; border: none; border-radius: 8px; font-size: 13.5px; font-weight: 600; cursor: pointer; font-family: inherit; transition: background 0.15s; margin-top: 12px; }
        .btn-primary:hover { background: #1F2937; }
        .btn-secondary { display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 9px; background: #fff; color: #374151; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; text-align: center; cursor: pointer; font-family: inherit; transition: background 0.15s, border-color 0.15s; margin-bottom: 8px; }
        .btn-secondary:hover { background: #F9FAFB; border-color: #D1D5DB; }
        .btn-secondary svg { width: 14px; height: 14px; }
        .btn-danger { display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; padding: 9px; background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; transition: background 0.15s; }
        .btn-danger:hover { background: #FEE2E2; }
        .btn-danger svg { width: 14px; height: 14px; }

        /* Assignment card */
        .assign-item { display: flex; align-items: center; gap: 10px; padding: 10px; background: #F9FAFB; border: 1px solid #F3F4F6; border-radius: 8px; margin-bottom: 8px; }
        .assign-avatar { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #6366F1, #8B5CF6); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #fff; flex-shrink: 0; }
        .assign-name { font-size: 13px; font-weight: 600; color: #111827; }
        .assign-date { font-size: 11px; color: #9CA3AF; }
    </style>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert-success">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="page-toolbar">
        <nav class="breadcrumb">
            <a href="{{ route('admin.requests.index') }}">Requests</a>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span>#{{ $requestModel->id }}</span>
        </nav>
        <a href="{{ route('admin.requests.edit', $requestModel->id) }}" class="btn-secondary" style="width:auto; padding: 8px 16px; margin-bottom:0;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Request
        </a>
    </div>

    <div class="detail-grid">

        {{-- ── Left column ── --}}
        <div>

            {{-- Overview card --}}
            <div class="detail-card">
                <div class="detail-card-header">
                    <span class="detail-card-title">Request Overview</span>
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
                <div class="detail-card-body">
                    <div class="req-title">{{ $requestModel->title }}</div>
                    <div class="req-meta">#{{ $requestModel->id }} &nbsp;·&nbsp; {{ $requestModel->created_at->format('M d, Y · H:i') }}</div>
                    <div class="req-desc">{{ $requestModel->description }}</div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Service Type</div>
                            <div class="info-value">{{ $requestModel->serviceType->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Priority</div>
                            <div class="info-value">
                                <span class="badge
                                    @if($requestModel->priority === 'urgent') pri-urgent
                                    @elseif($requestModel->priority === 'high') pri-high
                                    @elseif($requestModel->priority === 'medium') pri-medium
                                    @else pri-low
                                    @endif">
                                    {{ ucfirst($requestModel->priority) }}
                                </span>
                            </div>
                        </div>
                        <div class="info-item info-full">
                            <div class="info-label">Location</div>
                            <div class="info-value">{{ $requestModel->address }}</div>
                            @if($requestModel->barangay || $requestModel->city)
                                <div class="info-sub">
                                    {{ implode(', ', array_filter([$requestModel->barangay, $requestModel->city])) }}
                                </div>
                            @endif
                        </div>
                        <div class="info-item">
                            <div class="info-label">Requested by</div>
                            <div class="info-value">{{ $requestModel->user->name ?? 'N/A' }}</div>
                            <div class="info-sub">{{ $requestModel->user->email ?? '' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value">{{ $requestModel->updated_at->format('M d, Y') }}</div>
                            <div class="info-sub">{{ $requestModel->updated_at->format('H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attachments --}}
            @if($requestModel->attachments->count() > 0)
                <div class="detail-card">
                    <div class="detail-card-header">
                        <span class="detail-card-title">Attachments ({{ $requestModel->attachments->count() }})</span>
                    </div>
                    <div class="detail-card-body">
                        <div class="attachment-grid">
                            @foreach($requestModel->attachments as $attachment)
                                <a href="{{ asset($attachment->file_url) }}" target="_blank" class="attachment-item">
                                    <div class="attachment-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.415a3 3 0 104.243 4.243l6.586-6.586"/>
                                        </svg>
                                    </div>
                                    <span class="attachment-name">{{ $attachment->file_name }}</span>
                                    <span class="attachment-meta">{{ strtoupper($attachment->file_type) }} · {{ round($attachment->file_size / 1024, 1) }} KB</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Status History --}}
            @if($requestModel->statusHistory->count() > 0)
                <div class="detail-card">
                    <div class="detail-card-header">
                        <span class="detail-card-title">Status History</span>
                        <span style="font-size:12px; color:#9CA3AF;">{{ $requestModel->statusHistory->count() }} events</span>
                    </div>
                    <div class="detail-card-body">
                        <div class="timeline">
                            @foreach($requestModel->statusHistory->sortBy('created_at') as $history)
                                <div class="timeline-item">
                                    <div class="timeline-dot">
                                        <div class="timeline-dot-inner
                                            @if($history->status === 'created')     dot-created
                                            @elseif($history->status === 'assigned') dot-assigned
                                            @elseif($history->status === 'in_progress') dot-progress
                                            @elseif($history->status === 'resolved') dot-resolved
                                            @else dot-closed
                                            @endif">
                                        </div>
                                    </div>
                                    <div class="timeline-status">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</div>
                                    <div class="timeline-time">
                                        {{ $history->created_at->format('M d, Y · H:i') }}
                                        &nbsp;by&nbsp; <strong>{{ $history->changedBy->name ?? 'System' }}</strong>
                                    </div>
                                    @if($history->notes)
                                        <div class="timeline-notes">{{ $history->notes }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Feedback --}}
            @if($requestModel->feedback->count() > 0)
                <div class="detail-card">
                    <div class="detail-card-header">
                        <span class="detail-card-title">Feedback</span>
                    </div>
                    <div class="detail-card-body">
                        @foreach($requestModel->feedback as $feedback)
                            <div class="feedback-item">
                                <div class="feedback-header">
                                    <div class="feedback-avatar">{{ strtoupper(substr($feedback->user->name ?? 'U', 0, 1)) }}</div>
                                    <div>
                                        <div class="feedback-name">{{ $feedback->user->name ?? 'Anonymous' }}</div>
                                        <div class="feedback-date">{{ $feedback->created_at->format('M d, Y · H:i') }}</div>
                                    </div>
                                </div>
                                <div class="feedback-text">{{ $feedback->comment }}</div>
                                @if($feedback->rating)
                                    <div class="stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg fill="{{ $i <= $feedback->rating ? '#FBBF24' : 'none' }}"
                                                 stroke="{{ $i <= $feedback->rating ? '#FBBF24' : '#D1D5DB' }}"
                                                 viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.783.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                        <span>{{ $feedback->rating }}/5</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- ── Right sidebar ── --}}
        <div>

            {{-- Quick Status Update --}}
            <div class="side-card">
                <div class="side-card-header">Quick Status Update</div>
                <div class="side-card-body">
                    <form action="{{ route('admin.requests.status', $requestModel->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" required class="form-select">
                                @foreach(['created','assigned','in_progress','resolved','closed'] as $s)
                                    <option value="{{ $s }}" {{ $requestModel->status === $s ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $s)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" rows="2" class="form-textarea" placeholder="Note about this change…"></textarea>
                        </div>
                        <button type="submit" class="btn-primary">Update Status</button>
                    </form>
                </div>
            </div>

            {{-- Assignment --}}
            <div class="side-card">
                <div class="side-card-header">Assignment</div>
                <div class="side-card-body">
                    @if($requestModel->assignments->count() > 0)
                        <div style="margin-bottom: 14px;">
                            <div style="font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:#9CA3AF; margin-bottom:8px;">Current</div>
                            @foreach($requestModel->assignments as $assignment)
                                <div class="assign-item">
                                    <div class="assign-avatar">{{ strtoupper(substr($assignment->user->name ?? 'U', 0, 1)) }}</div>
                                    <div>
                                        <div class="assign-name">{{ $assignment->user->name ?? 'N/A' }}</div>
                                        <div class="assign-date">Since {{ $assignment->assigned_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <form action="{{ route('admin.requests.assign', $requestModel->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Assign to Staff</label>
                            <select name="user_id" required class="form-select">
                                <option value="">Select staff…</option>
                                @foreach($staff as $sUser)
                                    <option value="{{ $sUser->id }}" {{ $requestModel->assignments->contains('user_id', $sUser->id) ? 'selected' : '' }}>
                                        {{ $sUser->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">Assign Request</button>
                    </form>
                </div>
            </div>

            {{-- Actions --}}
            <div class="side-card">
                <div class="side-card-header">Actions</div>
                <div class="side-card-body">
                    <a href="{{ route('admin.requests.edit', $requestModel->id) }}" class="btn-secondary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Request
                    </a>
                    <form action="{{ route('admin.requests.destroy', $requestModel->id) }}" method="POST"
                          onsubmit="return confirm('Delete request #{{ $requestModel->id }}? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Request
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</x-admin-layout>