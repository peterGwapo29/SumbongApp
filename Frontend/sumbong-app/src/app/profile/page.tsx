'use client';

import { useEffect, useRef, useState } from 'react';
import { useRouter } from 'next/navigation';
import Layout from '@/components/mobile/Layout';
import Card from '@/components/mobile/Card';
import Button from '@/components/mobile/Button';
import { useAuth } from '@/contexts/AuthContext';
import { useTheme } from '@/contexts/ThemeContext';
import { requestsApi } from '@/lib/api';
import type { Request } from '@/types';

const SunIcon = ({ className = "w-4 h-4" }: { className?: string }) => (
  <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
  </svg>
);

const MoonIcon = ({ className = "w-4 h-4" }: { className?: string }) => (
  <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
  </svg>
);

const PencilIcon = ({ className = "w-4 h-4" }: { className?: string }) => (
  <svg className={className} fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
    <path strokeLinecap="round" strokeLinejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
  </svg>
);

export default function ProfilePage() {
  const router = useRouter();
  const { user, loading, logout, updateUser, updateAvatar } = useAuth();
  const { theme, setTheme } = useTheme();
  const fileInputRef = useRef<HTMLInputElement | null>(null);
  const [formName, setFormName] = useState('');
  const [formMobile, setFormMobile] = useState('');
  const [formAddress, setFormAddress] = useState('');
  const [saving, setSaving] = useState(false);
  const [saveMessage, setSaveMessage] = useState('');
  const [errorMessage, setErrorMessage] = useState('');
  const [uploadingAvatar, setUploadingAvatar] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [pendingAvatarFile, setPendingAvatarFile] = useState<File | null>(null);
  const [avatarPreviewUrl, setAvatarPreviewUrl] = useState<string | null>(null);
  const [avatarError, setAvatarError] = useState(false);
  const [statsLoading, setStatsLoading] = useState(true);
  const [stats, setStats] = useState({
    total: 0,
    inProgress: 0,
    resolved: 0,
    closed: 0,
  });

  useEffect(() => {
    if (!loading && !user) {
      router.push('/login');
    }
  }, [loading, user, router]);

  useEffect(() => {
    if (user) {
      setFormName(user.name || '');
      setFormMobile(user.mobile || '');
      setFormAddress(user.address || '');
      setAvatarError(false);
    }
  }, [user]);

  useEffect(() => {
    const loadStats = async () => {
      if (!user) {
        return;
      }
      setStatsLoading(true);
      try {
        const response = await requestsApi.getAll();
        const items: Request[] = Array.isArray(response) ? response : response?.data || [];
        if (!Array.isArray(items)) {
          setStats({
            total: 0,
            inProgress: 0,
            resolved: 0,
            closed: 0,
          });
          return;
        }

        const total = items.length;
        const inProgress = items.filter(
          (r) => r.status === 'in_progress' || r.status === 'assigned'
        ).length;
        const resolved = items.filter((r) => r.status === 'resolved').length;
        const closed = items.filter((r) => r.status === 'closed').length;

        setStats({
          total,
          inProgress,
          resolved,
          closed,
        });
      } catch {
        setStats({
          total: 0,
          inProgress: 0,
          resolved: 0,
          closed: 0,
        });
      } finally {
        setStatsLoading(false);
      }
    };

    void loadStats();
  }, [user]);

  useEffect(() => {
    return () => {
      if (avatarPreviewUrl) {
        URL.revokeObjectURL(avatarPreviewUrl);
      }
    };
  }, [avatarPreviewUrl]);

  const handleLogout = async () => {
    await logout();
    router.push('/login');
  };

  const handleSaveProfile = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!user) return;

    setSaving(true);
    setSaveMessage('');
    setErrorMessage('');

    try {
      await updateUser({
        name: formName,
        mobile: formMobile,
        address: formAddress,
      });

      if (pendingAvatarFile) {
        setUploadingAvatar(true);
        await updateAvatar(pendingAvatarFile);
        setPendingAvatarFile(null);
        if (avatarPreviewUrl) {
          URL.revokeObjectURL(avatarPreviewUrl);
          setAvatarPreviewUrl(null);
        }
      }

      setSaveMessage('Profile updated');
      setIsEditing(false);
    } catch (error: any) {
      setErrorMessage(error.message || 'Failed to update profile.');
    } finally {
      setSaving(false);
      setUploadingAvatar(false);
      if (!errorMessage) {
        setTimeout(() => setSaveMessage(''), 2500);
      }
    }
  };

  const handleAvatarClick = () => {
    if (!isEditing) {
      return;
    }
    fileInputRef.current?.click();
  };

  const handleAvatarChange = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (!file) return;

    if (avatarPreviewUrl) {
      URL.revokeObjectURL(avatarPreviewUrl);
    }

    const previewUrl = URL.createObjectURL(file);
    setPendingAvatarFile(file);
    setAvatarPreviewUrl(previewUrl);
    setAvatarError(false);
    event.target.value = '';
  };

  if (loading || !user) {
    return (
      <Layout title="Profile">
        <div className="p-4">
          <Card>
            <p className="text-center text-gray-600">Loading profile...</p>
          </Card>
        </div>
      </Layout>
    );
  }

  return (
    <Layout title="Profile">
      <div className="p-4 space-y-4">
        {/* Profile Header */}
        <Card>
          <div className="flex items-center justify-between mb-3">
            <span className="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account Info</span>
            <button
              type="button"
              onClick={() => {
                setIsEditing(!isEditing);
                setErrorMessage('');
                setSaveMessage('');
                if (user) {
                  setFormName(user.name || '');
                  setFormMobile(user.mobile || '');
                  setFormAddress(user.address || '');
                }
              }}
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/60 border border-blue-200 dark:border-blue-800 transition-all cursor-pointer shadow-xs"
            >
              <PencilIcon className="w-3.5 h-3.5" />
              <span>{isEditing ? 'Cancel Editing' : 'Edit Profile'}</span>
            </button>
          </div>

          <div className="text-center mb-2">
            <div className="relative inline-block mx-auto mb-3">
              <button
                type="button"
                onClick={handleAvatarClick}
                className="relative w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center mx-auto overflow-hidden focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-md group cursor-pointer"
                title="Click to change profile picture"
              >
                {avatarPreviewUrl ? (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img
                    src={avatarPreviewUrl}
                    alt={user.name}
                    className="w-full h-full object-cover"
                  />
                ) : user.avatar_url && !avatarError ? (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img
                    src={user.avatar_url}
                    alt={user.name}
                    className="w-full h-full object-cover"
                    onError={() => setAvatarError(true)}
                  />
                ) : (
                  <span className="text-4xl text-white">
                    {user.name ? user.name.charAt(0).toUpperCase() : '👤'}
                  </span>
                )}
                <div className="absolute inset-0 bg-black/40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                  <PencilIcon className="w-5 h-5 text-white mb-0.5" />
                  <span className="text-[10px] text-white font-medium">
                    {uploadingAvatar ? 'Uploading...' : 'Change Photo'}
                  </span>
                </div>
              </button>
            </div>
            <input
              ref={fileInputRef}
              type="file"
              accept="image/*"
              className="hidden"
              onChange={handleAvatarChange}
            />
            <h2 className="text-xl font-bold text-gray-900 dark:text-white">{user.name}</h2>
            <p className="text-sm text-gray-600 dark:text-gray-400">{user.email}</p>
            {user.verified && (
              <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/60 text-green-800 dark:text-green-200 mt-2">
                ✓ Verified
              </span>
            )}
          </div>
        </Card>

        {/* User Information */}
        <Card>
          <h3 className="font-semibold text-gray-900 dark:text-white mb-3">Personal Information</h3>
          {isEditing ? (
            <form onSubmit={handleSaveProfile} className="space-y-3">
              <div>
                <label className="text-xs text-gray-600 dark:text-gray-400 font-medium">Full Name</label>
                <input
                  type="text"
                  value={formName}
                  onChange={(e) => setFormName(e.target.value)}
                  placeholder="Enter your full name"
                  className="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label className="text-xs text-gray-600 dark:text-gray-400 font-medium">Mobile Number</label>
                <input
                  type="tel"
                  value={formMobile}
                  onChange={(e) => setFormMobile(e.target.value)}
                  placeholder="Enter your mobile number"
                  className="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label className="text-xs text-gray-600 dark:text-gray-400 font-medium">Address</label>
                <textarea
                  value={formAddress}
                  onChange={(e) => setFormAddress(e.target.value)}
                  rows={2}
                  placeholder="Enter your complete address"
                  className="mt-1 w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg text-sm text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label className="text-xs text-gray-600 dark:text-gray-400 font-medium">User Type</label>
                <p className="text-sm font-medium text-gray-900 dark:text-white capitalize">
                  {user.user_type ? user.user_type.replace('_', ' ') : 'Resident'}
                </p>
              </div>
              {errorMessage && <p className="text-xs text-red-600 dark:text-red-400">{errorMessage}</p>}
              {saveMessage && !errorMessage && (
                <p className="text-xs text-green-600 dark:text-green-400">{saveMessage}</p>
              )}
              <div className="flex items-center gap-2 pt-1">
                <Button type="submit" fullWidth disabled={saving}>
                  {saving ? 'Saving...' : 'Save Changes'}
                </Button>
                {isEditing && (
                  <button
                    type="button"
                    onClick={() => {
                      setIsEditing(false);
                      setErrorMessage('');
                      setSaveMessage('');
                      if (user) {
                        setFormName(user.name || '');
                        setFormMobile(user.mobile || '');
                        setFormAddress(user.address || '');
                      }
                    }}
                    className="text-xs text-gray-600 dark:text-gray-400 underline"
                  >
                    Cancel
                  </button>
                )}
              </div>
            </form>
          ) : (
            <div className="space-y-3">
              <div>
                <label className="text-xs text-gray-600 dark:text-gray-400 font-medium">Full Name</label>
                <p className="text-sm font-medium text-gray-900 dark:text-white">
                  {user.name || 'Not set'}
                </p>
              </div>
              <div>
                <label className="text-xs text-gray-600 dark:text-gray-400 font-medium">Mobile Number</label>
                <p className="text-sm font-medium text-gray-900 dark:text-white">
                  {user.mobile || 'Not set'}
                </p>
              </div>
              <div>
                <label className="text-xs text-gray-600 dark:text-gray-400 font-medium">Address</label>
                <p className="text-sm font-medium text-gray-900 dark:text-white">
                  {user.address || 'Not set'}
                </p>
              </div>
              <div>
                <label className="text-xs text-gray-600 dark:text-gray-400 font-medium">User Type</label>
                <p className="text-sm font-medium text-gray-900 dark:text-white capitalize">
                  {user.user_type ? user.user_type.replace('_', ' ') : 'Resident'}
                </p>
              </div>
            </div>
          )}
        </Card>

        {/* Account Stats */}
        <Card>
          <h3 className="font-semibold text-gray-900 dark:text-white mb-3">Account Statistics</h3>
          <div className="grid grid-cols-2 gap-4">
            <div className="text-center p-3 bg-gray-50 dark:bg-gray-700/60 rounded-lg">
              <p className="text-2xl font-bold text-gray-900 dark:text-white">
                {statsLoading ? '–' : stats.total}
              </p>
              <p className="text-xs text-gray-600 dark:text-gray-400">Total Requests</p>
            </div>
            <div className="text-center p-3 bg-gray-50 dark:bg-gray-700/60 rounded-lg">
              <p className="text-2xl font-bold text-gray-900 dark:text-white">
                {statsLoading ? '–' : stats.inProgress}
              </p>
              <p className="text-xs text-gray-600 dark:text-gray-400">In Progress</p>
            </div>
            <div className="text-center p-3 bg-gray-50 dark:bg-gray-700/60 rounded-lg">
              <p className="text-2xl font-bold text-gray-900 dark:text-white">
                {statsLoading ? '–' : stats.resolved}
              </p>
              <p className="text-xs text-gray-600 dark:text-gray-400">Resolved</p>
            </div>
            <div className="text-center p-3 bg-gray-50 dark:bg-gray-700/60 rounded-lg">
              <p className="text-2xl font-bold text-gray-900 dark:text-white">
                {statsLoading ? '–' : stats.closed}
              </p>
              <p className="text-xs text-gray-600 dark:text-gray-400">Closed</p>
            </div>
          </div>
        </Card>

        {/* Settings */}
        <Card>
          <h3 className="font-semibold text-gray-900 dark:text-white mb-3">Settings</h3>
          <div className="space-y-2">
            {/* Icon-Only Theme Toggle */}
            <div className="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800/80 rounded-lg border border-gray-100 dark:border-gray-700">
              <div className="flex items-center space-x-2.5">
                <span className="text-gray-700 dark:text-gray-200">
                  {theme === 'dark' ? <MoonIcon className="w-5 h-5" /> : <SunIcon className="w-5 h-5" />}
                </span>
                <span className="font-medium text-gray-900 dark:text-white text-sm">Theme Mode</span>
              </div>
              <div className="flex bg-gray-200 dark:bg-gray-700 p-1 rounded-lg gap-1">
                <button
                  type="button"
                  onClick={() => setTheme('light')}
                  aria-label="Light Mode"
                  title="Light Mode"
                  className={`p-1.5 rounded-md transition-all cursor-pointer ${
                    theme === 'light'
                      ? 'bg-white text-blue-600 shadow-xs'
                      : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                  }`}
                >
                  <SunIcon className="w-4 h-4" />
                </button>
                <button
                  type="button"
                  onClick={() => setTheme('dark')}
                  aria-label="Dark Mode"
                  title="Dark Mode"
                  className={`p-1.5 rounded-md transition-all cursor-pointer ${
                    theme === 'dark'
                      ? 'bg-blue-600 text-white shadow-xs'
                      : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'
                  }`}
                >
                  <MoonIcon className="w-4 h-4" />
                </button>
              </div>
            </div>

            <button className="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-800/80 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
              <span className="font-medium text-gray-900 dark:text-white text-sm">Notification Settings</span>
            </button>
            <button className="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-800/80 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
              <span className="font-medium text-gray-900 dark:text-white text-sm">Privacy & Security</span>
            </button>
            <button className="w-full text-left px-4 py-3 bg-gray-50 dark:bg-gray-800/80 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
              <span className="font-medium text-gray-900 dark:text-white text-sm">Help & Support</span>
            </button>
          </div>
        </Card>

        {/* Logout */}
        <Button variant="danger" fullWidth onClick={handleLogout}>
          Logout
        </Button>
      </div>
    </Layout>
  );
}

