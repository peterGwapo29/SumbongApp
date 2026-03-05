'use client';

import Layout from '@/components/mobile/Layout';
import Card from '@/components/mobile/Card';
import { mockNotifications } from '@/lib/mockData';

export default function NotificationsPage() {
  const notifications = mockNotifications;

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

  return (
    <Layout title="Notifications">
      <div className="p-4">
        {notifications.length > 0 ? (
          <div className="space-y-3">
            {notifications.map((notification) => (
              <Card key={notification.id}>
                <div className="flex items-start">
                  <span className="text-2xl mr-3">{getNotificationIcon(notification.type)}</span>
                  <div className="flex-1">
                    <div className="flex items-start justify-between mb-1">
                      <h3 className="font-semibold text-gray-900">{notification.title}</h3>
                      {!notification.deliveries?.[0]?.read && (
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
            ))}
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

