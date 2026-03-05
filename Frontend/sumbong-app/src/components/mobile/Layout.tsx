'use client';

import { ReactNode } from 'react';
import BottomNav from './BottomNav';
import TopBar from './TopBar';

interface LayoutProps {
  children: ReactNode;
  showBottomNav?: boolean;
  title?: string;
  showBack?: boolean;
}

export default function Layout({ 
  children, 
  showBottomNav = true, 
  title,
  showBack = false 
}: LayoutProps) {
  return (
    <div className="flex flex-col h-screen bg-gray-50">
      {title && <TopBar title={title} showBack={showBack} />}
      <main className="flex-1 overflow-y-auto pb-20">
        {children}
      </main>
      {showBottomNav && <BottomNav />}
    </div>
  );
}

