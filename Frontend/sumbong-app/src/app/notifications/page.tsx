'use client';

import { useEffect, useState } from 'react';
import Layout from '@/components/mobile/Layout';
import Card from '@/components/mobile/Card';
import Button from '@/components/mobile/Button';
import { notificationsApi } from '@/lib/api';

interface NotificationItem {
  id: string;
  title: string;
  message: string;
  type: string;
  created_at: string;
  deliveries?: { read: boolean }[];
}

export default function NotificationsPage() {
  const [notifications, setNotifications] = useState<NotificationItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const getNotificationIcon = (type: string) => {
    switch (type) {
      case 'alert':
        return '📢';
      case 'request_update':
        return '📋';
      case 'assignment':
        return '👤';
      default:
        return '🔔';
    }
  };

  const loadNotifications = async () => {
    try {
      setError('');
      const response = await notificationsApi.getAll();
      const items = Array.isArray(response) ? response : response?.data || [];
      setNotifications(items);
    } catch (err: any) {
      setError(err.message || 'Failed to load notifications.');
    } finally {
      setLoading(false);
    }
  };

  const handleMarkAllAsRead = async () => {
    try {
      await notificationsApi.markAllAsRead();
      await loadNotifications();
    } catch (err: any) {
      setError(err.message || 'Failed to mark notifications as read.');
    }
  };

  const handleMarkAsRead = async (id: string) => {
    try {
      await notificationsApi.markAsRead(id);
      await loadNotifications();
    } catch (err: any) {
      setError(err.message || 'Failed to mark notification as read.');
    }
  };

  useEffect(() => {
    void loadNotifications();
    const interval = setInterval(() => {
      void loadNotifications();
    }, 30000);
    return () => clearInterval(interval);
  }, []);

  return (
    <Layout title="Notifications">
      <div className="p-4 space-y-3">
        {error && (
          <Card>
            <p className="text-sm text-red-600">{error}</p>
          </Card>
        )}

        <div className="flex items-center justify-between">
          <p className="text-sm text-gray-600">
            You will see alerts for new services and updates on your own requests.
          </p>
          {notifications.length > 0 && (
            <Button
              variant="outline"
              onClick={handleMarkAllAsRead}
              showLoading={false}
              className="ml-2 px-3 py-1 text-xs"
            >
              Mark all read
            </Button>
          )}
        </div>

        {loading ? (
          <Card>
            <p className="text-center py-8 text-gray-600">Loading notifications...</p>
          </Card>
        ) : notifications.length > 0 ? (
          <div className="space-y-3">
            {notifications.map((notification) => {
              const isUnread = !notification.deliveries?.[0]?.read;
              return (
                <Card
                  key={notification.id}
                  onClick={() => {
                    if (isUnread) {
                      void handleMarkAsRead(notification.id);
                    }
                  }}
                >
                  <div className="flex items-start">
                    <span className="text-2xl mr-3">{getNotificationIcon(notification.type)}</span>
                    <div className="flex-1">
                      <div className="flex items-start justify-between mb-1">
                        <h3 className="font-semibold text-gray-900">{notification.title}</h3>
                        {isUnread && (
                          <span className="w-2 h-2 bg-blue-600 rounded-full ml-2 mt-1"></span>
                        )}
                      </div>
                      <p className="text-sm text-gray-600 mb-2">{notification.message}</p>
                      <p className="text-xs text-gray-500" suppressHydrationWarning>
                        {new Date(notification.created_at).toLocaleString()}
                      </p>
                    </div>
                  </div>
                </Card>
              );
            })}
          </div>
        ) : (
          <Card>
            <div className="text-center py-8">
              <span className="text-4xl mb-4 block">🔔</span>
              <p className="text-gray-600">No notifications yet</p>
            </div>
          </Card>
        )}
      </div>
    </Layout>
  );
}

