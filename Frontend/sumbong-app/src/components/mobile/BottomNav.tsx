'use client';

import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { useLoadingContext } from './LoadingProvider';
import { useNotifications } from './NotificationsProvider';

export default function BottomNav() {
  const pathname = usePathname();
  const router = useRouter();
  const { startLoading, stopLoading } = useLoadingContext();
  const { unreadCount } = useNotifications();

  const navItems = [
    { href: '/home', label: 'Home', icon: '🏠' },
    { href: '/requests', label: 'Requests', icon: '📋' },
    { href: '/create', label: 'Create', icon: '➕' },
    { href: '/notifications', label: 'Alerts', icon: '🔔' },
    { href: '/profile', label: 'Profile', icon: '👤' },
  ];

  const handleNavClick = (e: React.MouseEvent<HTMLAnchorElement>, href: string) => {
    if (pathname !== href) {
      e.preventDefault();
      startLoading('Loading...');
      setTimeout(() => {
        router.push(href);
        setTimeout(() => stopLoading(), 300);
      }, 100);
    }
  };

  return (
    <nav className="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
      <div className="flex justify-around items-center h-16">
        {navItems.map((item) => {
          const isActive = pathname === item.href;
          return (
            <Link
              key={item.href}
              href={item.href}
              onClick={(e) => handleNavClick(e, item.href)}
              className={`relative flex flex-col items-center justify-center flex-1 h-full ${
                isActive ? 'text-blue-600' : 'text-gray-500'
              }`}
            >
              <span className="text-2xl mb-1 relative">
                {item.icon}
                {item.href === '/notifications' && unreadCount > 0 && (
                  <span className="absolute -top-1 -right-3 min-w-[18px] h-[18px] rounded-full bg-red-500 text-white text-[10px] font-semibold flex items-center justify-center px-1">
                    {unreadCount > 9 ? '9+' : unreadCount}
                  </span>
                )}
              </span>
              <span className="text-xs font-medium">{item.label}</span>
            </Link>
          );
        })}
      </div>
    </nav>
  );
}

