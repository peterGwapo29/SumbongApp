'use client';

import { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import { notificationsApi } from '@/lib/api';

interface NotificationsContextType {
  unreadCount: number;
  refresh: () => Promise<void>;
}

const NotificationsContext = createContext<NotificationsContextType | undefined>(undefined);

export function NotificationsProvider({ children }: { children: ReactNode }) {
  const [unreadCount, setUnreadCount] = useState(0);

  const loadNotifications = async () => {
    try {
      const response = await notificationsApi.getAll();
      const notifications = Array.isArray(response) ? response : response?.data || [];
      const unread = Array.isArray(notifications)
        ? notifications.filter(
            (n: any) => n.deliveries && n.deliveries.length > 0 && !n.deliveries[0].read
          ).length
        : 0;
      setUnreadCount(unread);
    } catch {
      setUnreadCount(0);
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
    <NotificationsContext.Provider
      value={{
        unreadCount,
        refresh: loadNotifications,
      }}
    >
      {children}
    </NotificationsContext.Provider>
  );
}

export function useNotifications() {
  const context = useContext(NotificationsContext);
  if (!context) {
    throw new Error('useNotifications must be used within a NotificationsProvider');
  }
  return context;
}

