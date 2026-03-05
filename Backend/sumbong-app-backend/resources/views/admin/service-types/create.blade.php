<x-admin-layout>
    <x-slot name="header">Create Service Type</x-slot>

    <style>
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 900px) {
            .form-layout { grid-template-columns: 1fr; }
        }

        /* ── Cards ── */
        .form-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .form-card:last-child { margin-bottom: 0; }
        .form-card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #F3F4F6;
            display: flex; align-items: center; gap: 10px;
        }
        .form-card-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: #F3F4F6;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
        }
        .form-card-title { font-size: 14px; font-weight: 700; color: #111827; }
        .form-card-body { padding: 22px; }

        /* ── Fields ── */
        .form-group { margin-bottom: 18px; }
        .form-group:last-child { margin-bottom: 0; }
        .form-label {
            display: block;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.7px;
            color: #6B7280; margin-bottom: 6px;
        }
        .form-label span { color: #EF4444; margin-left: 2px; }
        .form-input {
            width: 100%; font-size: 14px; color: #111827;
            background: #F9FAFB; border: 1px solid #E5E7EB;
            border-radius: 8px; padding: 10px 14px;
            font-family: inherit; box-sizing: border-box;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
        }
        .form-input:focus {
            outline: none; border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            background: #fff;
        }
        textarea.form-input { resize: vertical; min-height: 100px; line-height: 1.5; }
        .form-error { margin-top: 5px; font-size: 12px; color: #DC2626; }
        .form-hint  { margin-top: 5px; font-size: 12px; color: #9CA3AF; }

        /* Emoji input with preview */
        .emoji-wrap { display: flex; gap: 10px; align-items: center; }
        .emoji-preview {
            width: 44px; height: 44px; border-radius: 10px;
            background: #F3F4F6; border: 1px solid #E5E7EB;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
            transition: background 0.15s;
        }

        /* Toggle switch */
        .toggle-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 16px;
            background: #F9FAFB; border: 1px solid #E5E7EB;
            border-radius: 8px;
        }
        .toggle-label-wrap { display: flex; flex-direction: column; gap: 2px; }
        .toggle-label { font-size: 13.5px; font-weight: 600; color: #111827; }
        .toggle-desc  { font-size: 12px; color: #9CA3AF; }
        .toggle-input { position: relative; display: inline-block; width: 40px; height: 22px; flex-shrink: 0; }
        .toggle-input input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; cursor: pointer; inset: 0;
            background: #D1D5DB; border-radius: 22px;
            transition: background 0.2s;
        }
        .toggle-slider::before {
            content: ''; position: absolute;
            width: 16px; height: 16px; border-radius: 50%;
            background: #fff; left: 3px; top: 3px;
            transition: transform 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        }
        .toggle-input input:checked + .toggle-slider { background: #2563EB; }
        .toggle-input input:checked + .toggle-slider::before { transform: translateX(18px); }

        /* ── Sidebar preview card ── */
        .preview-card {
            background: #fff; border: 1px solid #E5E7EB;
            border-radius: 12px; overflow: hidden;
            position: sticky; top: 80px;
        }
        .preview-card-header {
            padding: 14px 18px; border-bottom: 1px solid #F3F4F6;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.7px; color: #9CA3AF;
        }
        .preview-card-body { padding: 20px 18px; }
        .preview-service-item {
            display: flex; align-items: center; gap: 12px;
            padding: 14px; border-radius: 10px;
            background: #F9FAFB; border: 1px solid #E5E7EB;
            margin-bottom: 12px;
        }
        .preview-icon {
            width: 42px; height: 42px; border-radius: 10px;
            background: #fff; border: 1px solid #E5E7EB;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .preview-name { font-size: 14px; font-weight: 700; color: #111827; }
        .preview-dept { font-size: 12px; color: #6B7280; }
        .preview-desc-text { font-size: 12px; color: #9CA3AF; line-height: 1.5; margin-top: 8px; }
        .preview-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
            background: #ECFDF5; color: #065F46; margin-top: 10px;
        }
        .preview-badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; opacity: 0.7; }
        .preview-empty { text-align: center; padding: 20px 0; color: #9CA3AF; font-size: 13px; }

        /* ── Breadcrumb ── */
        .breadcrumb {
            display: flex; align-items: center; gap: 6px;
            margin-bottom: 20px;
            font-size: 13px; color: #9CA3AF;
        }
        .breadcrumb a { color: #6B7280; text-decoration: none; font-weight: 500; transition: color 0.15s; }
        .breadcrumb a:hover { color: #111827; }
        .breadcrumb svg { width: 14px; height: 14px; }

        /* ── Action buttons ── */
        .form-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-primary {
            padding: 10px 22px; background: #111827; color: #fff;
            border: none; border-radius: 8px;
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; font-family: inherit;
            display: inline-flex; align-items: center; gap: 6px;
            transition: background 0.15s;
        }
        .btn-primary:hover { background: #1F2937; }
        .btn-primary svg { width: 15px; height: 15px; }
        .btn-cancel {
            padding: 10px 18px; background: #fff; color: #374151;
            border: 1px solid #E5E7EB; border-radius: 8px;
            font-size: 13.5px; font-weight: 500;
            text-decoration: none; cursor: pointer; font-family: inherit;
            transition: background 0.15s, border-color 0.15s;
        }
        .btn-cancel:hover { background: #F9FAFB; border-color: #D1D5DB; }
    </style>

    {{-- Breadcrumb --}}
    <nav class="breadcrumb">
        <a href="{{ route('admin.service-types.index') }}">Service Types</a>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span>Create New</span>
    </nav>

    <form method="POST" action="{{ route('admin.service-types.store') }}" id="create-form">
        @csrf

        <div class="form-layout">

            {{-- ── Left: Main form ── --}}
            <div>

                {{-- Basic Info --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">📋</div>
                        <span class="form-card-title">Basic Information</span>
                    </div>
                    <div class="form-card-body">
                        <div class="form-group">
                            <label class="form-label" for="name">Name <span>*</span></label>
                            <input type="text" id="name" name="name"
                                   value="{{ old('name') }}" required
                                   placeholder="e.g. Garbage Pickup"
                                   class="form-input"
                                   oninput="updatePreview()" />
                            @error('name')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="department">Department <span>*</span></label>
                            <input type="text" id="department" name="department"
                                   value="{{ old('department') }}" required
                                   placeholder="e.g. Sanitation, Public Works"
                                   class="form-input"
                                   oninput="updatePreview()" />
                            @error('department')<p class="form-error">{{ $message }}</p>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" name="description"
                                      rows="3" placeholder="Brief explanation of what this service covers…"
                                      class="form-input"
                                      oninput="updatePreview()">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Icon & Status --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <div class="form-card-icon">⚙️</div>
                        <span class="form-card-title">Appearance & Status</span>
                    </div>
                    <div class="form-card-body">
                        <div class="form-group">
                            <label class="form-label" for="icon">Icon (Emoji)</label>
                            <div class="emoji-wrap">
                                <div class="emoji-preview" id="emoji-preview">📋</div>
                                <input type="text" id="icon" name="icon"
                                       value="{{ old('icon') }}" maxlength="10"
                                       placeholder="Paste an emoji, e.g. 🗑️"
                                       class="form-input"
                                       oninput="updateEmoji(this.value); updatePreview()" />
                            </div>
                            <p class="form-hint">Paste a single emoji to represent this service type visually.</p>
                        </div>

                        <div class="form-group">
                            <div class="toggle-row">
                                <div class="toggle-label-wrap">
                                    <span class="toggle-label">Active</span>
                                    <span class="toggle-desc">Visible to users when submitting requests</span>
                                </div>
                                <label class="toggle-input">
                                    <input type="checkbox" name="is_active" value="1"
                                           id="is_active"
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           onchange="updatePreview()" />
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Service Type
                    </button>
                    <a href="{{ route('admin.service-types.index') }}" class="btn-cancel">Cancel</a>
                </div>
            </div>

            {{-- ── Right: Live preview ── --}}
            <aside>
                <div class="preview-card">
                    <div class="preview-card-header">Live Preview</div>
                    <div class="preview-card-body">
                        <div class="preview-service-item">
                            <div class="preview-icon" id="prev-icon">📋</div>
                            <div>
                                <div class="preview-name" id="prev-name">Service Name</div>
                                <div class="preview-dept" id="prev-dept">Department</div>
                            </div>
                        </div>
                        <div class="preview-desc-text" id="prev-desc">Description will appear here…</div>
                        <div id="prev-status-wrap">
                            <span class="preview-badge" id="prev-badge">Active</span>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </form>

    <script>
        function updateEmoji(val) {
            const trimmed = val.trim();
            document.getElementById('emoji-preview').textContent = trimmed || '📋';
        }

        function updatePreview() {
            const name  = document.getElementById('name').value.trim();
            const dept  = document.getElementById('department').value.trim();
            const desc  = document.getElementById('description').value.trim();
            const icon  = document.getElementById('icon').value.trim();
            const active = document.getElementById('is_active').checked;

            document.getElementById('prev-name').textContent = name  || 'Service Name';
            document.getElementById('prev-dept').textContent = dept  || 'Department';
            document.getElementById('prev-desc').textContent = desc  || 'Description will appear here…';
            document.getElementById('prev-icon').textContent = icon  || '📋';

            const badge = document.getElementById('prev-badge');
            if (active) {
                badge.textContent = 'Active';
                badge.style.background = '#ECFDF5';
                badge.style.color = '#065F46';
            } else {
                badge.textContent = 'Inactive';
                badge.style.background = '#F3F4F6';
                badge.style.color = '#6B7280';
            }
        }

        // Init preview with old() values if any
        updatePreview();
    </script>

</x-admin-layout>