<x-admin-layout>
    <x-slot name="header">Profile</x-slot>

    <style>
        .profile-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 900px) {
            .profile-grid { grid-template-columns: 1fr; }
        }

        /* ── Section card ── */
        .profile-card {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .profile-card:last-child { margin-bottom: 0; }
        .profile-card-header {
            padding: 20px 24px 0;
        }
        .profile-card-title {
            font-size: 15px; font-weight: 700; color: #111827;
            margin-bottom: 4px;
        }
        .profile-card-desc {
            font-size: 13px; color: #6B7280; line-height: 1.5;
        }
        .profile-card-body {
            padding: 20px 24px 24px;
        }

        /* ── User sidebar card ── */
        .user-sidebar {
            background: #fff;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 28px 24px;
            display: flex; flex-direction: column; align-items: center;
            text-align: center;
            position: sticky; top: 80px;
        }
        .user-big-avatar {
            width: 72px; height: 72px; border-radius: 50%;
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; font-weight: 800; color: #fff;
            margin-bottom: 14px;
        }
        .user-sidebar-name {
            font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 3px;
        }
        .user-sidebar-email {
            font-size: 13px; color: #6B7280; margin-bottom: 16px;
            word-break: break-all;
        }
        .user-sidebar-role {
            display: inline-flex; align-items: center;
            padding: 4px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 600;
            background: #FEF3C7; color: #92400E;
            margin-bottom: 20px;
        }
        .sidebar-divider {
            width: 100%; height: 1px; background: #F3F4F6; margin-bottom: 16px;
        }
        .sidebar-meta {
            width: 100%; text-align: left;
        }
        .sidebar-meta-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 8px 0; border-bottom: 1px solid #F9FAFB;
            font-size: 12.5px;
        }
        .sidebar-meta-row:last-child { border-bottom: none; }
        .sidebar-meta-label { color: #9CA3AF; font-weight: 500; }
        .sidebar-meta-value { color: #374151; font-weight: 600; }
        .badge-verified-sm {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 8px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
            background: #ECFDF5; color: #065F46;
        }
        .badge-verified-sm::before {
            content: ''; width: 5px; height: 5px;
            border-radius: 50%; background: currentColor; opacity: 0.7;
        }

        /* ── Form fields ── */
        .form-group { margin-bottom: 18px; }
        .form-group:last-of-type { margin-bottom: 0; }
        .form-label {
            display: block;
            font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.6px;
            color: #6B7280; margin-bottom: 6px;
        }
        .form-input {
            width: 100%; font-size: 14px; color: #111827;
            background: #F9FAFB; border: 1px solid #E5E7EB;
            border-radius: 8px; padding: 10px 14px;
            font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            box-sizing: border-box;
        }
        .form-input:focus {
            outline: none; border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            background: #fff;
        }
        .form-input[disabled] {
            background: #F3F4F6; color: #9CA3AF; cursor: not-allowed;
        }
        .form-error {
            margin-top: 5px; font-size: 12px; color: #DC2626;
        }
        .form-hint {
            margin-top: 5px; font-size: 12px; color: #6B7280;
        }

        /* ── Form footer ── */
        .form-footer {
            display: flex; align-items: center; gap: 12px;
            padding-top: 20px; border-top: 1px solid #F3F4F6;
            margin-top: 20px;
        }
        .btn-save {
            padding: 9px 22px;
            background: #111827; color: #fff;
            border: none; border-radius: 8px;
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; font-family: inherit;
            transition: background 0.15s;
        }
        .btn-save:hover { background: #1F2937; }
        .saved-msg {
            font-size: 13px; color: #10B981; font-weight: 500;
        }

        /* ── Danger zone ── */
        .danger-card {
            background: #fff;
            border: 1px solid #FEE2E2;
            border-radius: 12px; overflow: hidden;
        }
        .danger-card-header { padding: 20px 24px 0; }
        .danger-card-title { font-size: 15px; font-weight: 700; color: #DC2626; margin-bottom: 4px; }
        .danger-card-desc { font-size: 13px; color: #6B7280; line-height: 1.5; }
        .danger-card-body { padding: 20px 24px 24px; }

        .btn-danger {
            padding: 9px 18px;
            background: #fff; color: #DC2626;
            border: 1px solid #FCA5A5; border-radius: 8px;
            font-size: 13.5px; font-weight: 600;
            cursor: pointer; font-family: inherit;
            transition: background 0.15s, border-color 0.15s;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-danger:hover { background: #FEF2F2; border-color: #F87171; }
        .btn-danger svg { width: 15px; height: 15px; }
    </style>

    <div class="profile-grid">

        {{-- ── Left: User sidebar ── --}}
        <aside class="user-sidebar">
            <div class="user-big-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="user-sidebar-name">{{ Auth::user()->name }}</div>
            <div class="user-sidebar-email">{{ Auth::user()->email }}</div>
            <span class="user-sidebar-role">{{ Auth::user()->role?->name ?? 'Admin' }}</span>

            <div class="sidebar-divider"></div>

            <div class="sidebar-meta">
                <div class="sidebar-meta-row">
                    <span class="sidebar-meta-label">Status</span>
                    <span class="badge-verified-sm">Verified</span>
                </div>
                <div class="sidebar-meta-row">
                    <span class="sidebar-meta-label">Member since</span>
                    <span class="sidebar-meta-value">{{ Auth::user()->created_at->format('M Y') }}</span>
                </div>
                <div class="sidebar-meta-row">
                    <span class="sidebar-meta-label">Last updated</span>
                    <span class="sidebar-meta-value">{{ Auth::user()->updated_at->format('M d, Y') }}</span>
                </div>
            </div>
        </aside>

        {{-- ── Right: Forms ── --}}
        <div>

            {{-- Profile Information --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">Profile Information</div>
                    <div class="profile-card-desc">Update your account's name and email address.</div>
                </div>
                <div class="profile-card-body">
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="form-group">
                            <label class="form-label" for="name">Full Name</label>
                            <input id="name" name="name" type="text"
                                   value="{{ old('name', $user->name) }}"
                                   required autofocus autocomplete="name"
                                   class="form-input" />
                            @if ($errors->get('name'))
                                <p class="form-error">{{ $errors->first('name') }}</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <input id="email" name="email" type="email"
                                   value="{{ old('email', $user->email) }}"
                                   required autocomplete="username"
                                   class="form-input" />
                            @if ($errors->get('email'))
                                <p class="form-error">{{ $errors->first('email') }}</p>
                            @endif

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <p class="form-hint">
                                    Your email is unverified.
                                    <button form="send-verification"
                                            class="text-blue-600 underline hover:text-blue-800 font-medium bg-transparent border-none cursor-pointer font-inherit">
                                        Resend verification email
                                    </button>
                                </p>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="form-hint" style="color: #10B981;">Verification link sent!</p>
                                @endif
                            @endif
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn-save">Save Changes</button>
                            @if (session('status') === 'profile-updated')
                                <span class="saved-msg"
                                      x-data="{ show: true }"
                                      x-show="show" x-transition
                                      x-init="setTimeout(() => show = false, 2500)">
                                    ✓ Profile updated
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Update Password --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">Update Password</div>
                    <div class="profile-card-desc">Use a long, random password to keep your account secure.</div>
                </div>
                <div class="profile-card-body">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="form-group">
                            <label class="form-label" for="current_password">Current Password</label>
                            <input id="current_password" name="current_password" type="password"
                                   autocomplete="current-password"
                                   class="form-input" />
                            @if ($errors->updatePassword->get('current_password'))
                                <p class="form-error">{{ $errors->updatePassword->first('current_password') }}</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password">New Password</label>
                            <input id="password" name="password" type="password"
                                   autocomplete="new-password"
                                   class="form-input" />
                            @if ($errors->updatePassword->get('password'))
                                <p class="form-error">{{ $errors->updatePassword->first('password') }}</p>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Confirm New Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                   autocomplete="new-password"
                                   class="form-input" />
                            @if ($errors->updatePassword->get('password_confirmation'))
                                <p class="form-error">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                            @endif
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="btn-save">Update Password</button>
                            @if (session('status') === 'password-updated')
                                <span class="saved-msg"
                                      x-data="{ show: true }"
                                      x-show="show" x-transition
                                      x-init="setTimeout(() => show = false, 2500)">
                                    ✓ Password updated
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="danger-card">
                <div class="danger-card-header">
                    <div class="danger-card-title">Delete Account</div>
                    <div class="danger-card-desc">
                        Once your account is deleted, all data will be permanently removed.
                        Please download any information you wish to keep before proceeding.
                    </div>
                </div>
                <div class="danger-card-body">
                    <button
                        class="btn-danger"
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Account
                    </button>

                    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                        <form method="post" action="{{ route('profile.destroy') }}" style="padding: 28px;">
                            @csrf
                            @method('delete')

                            <h2 style="font-size:16px; font-weight:700; color:#111827; margin-bottom:8px;">
                                Are you sure?
                            </h2>
                            <p style="font-size:13px; color:#6B7280; line-height:1.6; margin-bottom:20px;">
                                This will permanently delete your account and all associated data.
                                Enter your password to confirm.
                            </p>

                            <div class="form-group">
                                <label class="form-label" for="del_password">Password</label>
                                <input id="del_password" name="password" type="password"
                                       placeholder="Your current password"
                                       class="form-input" style="max-width: 320px;" />
                                @if ($errors->userDeletion->get('password'))
                                    <p class="form-error">{{ $errors->userDeletion->first('password') }}</p>
                                @endif
                            </div>

                            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; padding-top:16px; border-top:1px solid #F3F4F6;">
                                <button type="button"
                                        x-on:click="$dispatch('close')"
                                        style="padding:8px 18px; background:#fff; color:#374151; border:1px solid #E5E7EB; border-radius:8px; font-size:13.5px; font-weight:500; cursor:pointer; font-family:inherit;">
                                    Cancel
                                </button>
                                <button type="submit" class="btn-danger">
                                    Delete My Account
                                </button>
                            </div>
                        </form>
                    </x-modal>
                </div>
            </div>

        </div>
    </div>

</x-admin-layout>