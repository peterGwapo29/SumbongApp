'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import Layout from '@/components/mobile/Layout';
import Card from '@/components/mobile/Card';
import Button from '@/components/mobile/Button';
import { mockUser } from '@/lib/mockData';

export default function ProfilePage() {
  const router = useRouter();
  const [user] = useState(mockUser);

  const handleLogout = () => {
    // Simulate logout
    router.push('/login');
  };

  return (
    <Layout title="Profile">
      <div className="p-4 space-y-4">
        {/* Profile Header */}
        <Card>
          <div className="text-center mb-4">
            <div className="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
              <span className="text-3xl text-white">👤</span>
            </div>
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
          <div className="space-y-3">
            <div>
              <label className="text-xs text-gray-600">Mobile Number</label>
              <p className="text-sm font-medium text-gray-900">{user.mobile}</p>
            </div>
            <div>
              <label className="text-xs text-gray-600">Address</label>
              <p className="text-sm font-medium text-gray-900">{user.address}</p>
            </div>
            <div>
              <label className="text-xs text-gray-600">User Type</label>
              <p className="text-sm font-medium text-gray-900 capitalize">
                {user.user_type.replace('_', ' ')}
              </p>
            </div>
          </div>
        </Card>

        {/* Account Stats */}
        <Card>
          <h3 className="font-semibold text-gray-900 mb-3">Account Statistics</h3>
          <div className="grid grid-cols-2 gap-4">
            <div className="text-center p-3 bg-gray-50 rounded-lg">
              <p className="text-2xl font-bold text-gray-900">5</p>
              <p className="text-xs text-gray-600">Total Requests</p>
            </div>
            <div className="text-center p-3 bg-gray-50 rounded-lg">
              <p className="text-2xl font-bold text-gray-900">2</p>
              <p className="text-xs text-gray-600">In Progress</p>
            </div>
            <div className="text-center p-3 bg-gray-50 rounded-lg">
              <p className="text-2xl font-bold text-gray-900">3</p>
              <p className="text-xs text-gray-600">Resolved</p>
            </div>
            <div className="text-center p-3 bg-gray-50 rounded-lg">
              <p className="text-2xl font-bold text-gray-900">4.5</p>
              <p className="text-xs text-gray-600">Avg Rating</p>
            </div>
          </div>
        </Card>

        {/* Settings */}
        <Card>
          <h3 className="font-semibold text-gray-900 mb-3">Settings</h3>
          <div className="space-y-2">
            <button className="w-full text-left px-4 py-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
              <span className="font-medium text-gray-900">Edit Profile</span>
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

