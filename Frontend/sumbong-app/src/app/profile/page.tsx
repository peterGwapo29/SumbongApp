'use client';

import { useEffect, useRef, useState } from 'react';
import { useRouter } from 'next/navigation';
import Layout from '@/components/mobile/Layout';
import Card from '@/components/mobile/Card';
import Button from '@/components/mobile/Button';
import { useAuth } from '@/contexts/AuthContext';
import { requestsApi } from '@/lib/api';
import type { Request } from '@/types';

export default function ProfilePage() {
  const router = useRouter();
  const { user, loading, logout, updateUser, updateAvatar } = useAuth();
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
          <div className="text-center mb-4">
            <button
              type="button"
              onClick={handleAvatarClick}
              className="relative w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3 overflow-hidden focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
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
                <span className="text-3xl text-white">
                  {user.name ? user.name.charAt(0).toUpperCase() : '👤'}
                </span>
              )}
              {isEditing && (
                <span className="absolute bottom-0 inset-x-0 bg-black/40 text-[10px] text-white py-1">
                  {uploadingAvatar ? 'Uploading...' : 'Change'}
                </span>
              )}
            </button>
            <input
              ref={fileInputRef}
              type="file"
              accept="image/*"
              className="hidden"
              onChange={handleAvatarChange}
            />
            <h2 className="text-xl font-semibold text-gray-900">{user.name}</h2>
            <p className="text-sm text-gray-600">{user.email}</p>
            {user.verified && (
              <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                ✓ Verified
              </span>
            )}
          </div>
        </Card>

        {/* User Information */}
        <Card>
          <h3 className="font-semibold text-gray-900 mb-3">Personal Information</h3>
          {isEditing ? (
            <form onSubmit={handleSaveProfile} className="space-y-3">
              <div>
                <label className="text-xs text-gray-600">Full Name</label>
                <input
                  type="text"
                  value={formName}
                  onChange={(e) => setFormName(e.target.value)}
                  placeholder="Enter your full name"
                  className="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label className="text-xs text-gray-600">Mobile Number</label>
                <input
                  type="tel"
                  value={formMobile}
                  onChange={(e) => setFormMobile(e.target.value)}
                  placeholder="Enter your mobile number"
                  className="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label className="text-xs text-gray-600">Address</label>
                <textarea
                  value={formAddress}
                  onChange={(e) => setFormAddress(e.target.value)}
                  rows={2}
                  placeholder="Enter your complete address"
                  className="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
              <div>
                <label className="text-xs text-gray-600">User Type</label>
                <p className="text-sm font-medium text-gray-900 capitalize">
                  {user.user_type ? user.user_type.replace('_', ' ') : 'Resident'}
                </p>
              </div>
              {errorMessage && <p className="text-xs text-red-600">{errorMessage}</p>}
              {saveMessage && !errorMessage && (
                <p className="text-xs text-green-600">{saveMessage}</p>
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
                    className="text-xs text-gray-600 underline"
                  >
                    Cancel
                  </button>
                )}
              </div>
            </form>
          ) : (
            <div className="space-y-3">
              <div>
                <label className="text-xs text-gray-600">Full Name</label>
                <p className="text-sm font-medium text-gray-900">
                  {user.name || 'Not set'}
                </p>
              </div>
              <div>
                <label className="text-xs text-gray-600">Mobile Number</label>
                <p className="text-sm font-medium text-gray-900">
                  {user.mobile || 'Not set'}
                </p>
              </div>
              <div>
                <label className="text-xs text-gray-600">Address</label>
                <p className="text-sm font-medium text-gray-900">
                  {user.address || 'Not set'}
                </p>
              </div>
              <div>
                <label className="text-xs text-gray-600">User Type</label>
                <p className="text-sm font-medium text-gray-900 capitalize">
                  {user.user_type ? user.user_type.replace('_', ' ') : 'Resident'}
                </p>
              </div>
            </div>
          )}
        </Card>

        {/* Account Stats */}
        <Card>
          <h3 className="font-semibold text-gray-900 mb-3">Account Statistics</h3>
          <div className="grid grid-cols-2 gap-4">
            <div className="text-center p-3 bg-gray-50 rounded-lg">
              <p className="text-2xl font-bold text-gray-900">
                {statsLoading ? '–' : stats.total}
              </p>
              <p className="text-xs text-gray-600">Total Requests</p>
            </div>
            <div className="text-center p-3 bg-gray-50 rounded-lg">
              <p className="text-2xl font-bold text-gray-900">
                {statsLoading ? '–' : stats.inProgress}
              </p>
              <p className="text-xs text-gray-600">In Progress</p>
            </div>
            <div className="text-center p-3 bg-gray-50 rounded-lg">
              <p className="text-2xl font-bold text-gray-900">
                {statsLoading ? '–' : stats.resolved}
              </p>
              <p className="text-xs text-gray-600">Resolved</p>
            </div>
            <div className="text-center p-3 bg-gray-50 rounded-lg">
              <p className="text-2xl font-bold text-gray-900">
                {statsLoading ? '–' : stats.closed}
              </p>
              <p className="text-xs text-gray-600">Closed</p>
            </div>
          </div>
        </Card>

        {/* Settings */}
        <Card>
          <h3 className="font-semibold text-gray-900 mb-3">Settings</h3>
          <div className="space-y-2">
            <button
              type="button"
              onClick={() => {
                setIsEditing(true);
                setErrorMessage('');
                setSaveMessage('');
                if (user) {
                  setFormName(user.name || '');
                  setFormMobile(user.mobile || '');
                  setFormAddress(user.address || '');
                }
              }}
              className="w-full text-left px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
            >
              <span className="font-medium text-gray-900">
                {isEditing ? 'Editing Profile' : 'Edit Profile'}
              </span>
            </button>
            <button className="w-full text-left px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
              <span className="font-medium text-gray-900">Notification Settings</span>
            </button>
            <button className="w-full text-left px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
              <span className="font-medium text-gray-900">Privacy & Security</span>
            </button>
            <button className="w-full text-left px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
              <span className="font-medium text-gray-900">Help & Support</span>
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

