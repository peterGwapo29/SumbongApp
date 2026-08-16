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
    <div className="min-h-screen bg-slate-100 dark:bg-slate-950 flex justify-center items-stretch font-sans antialiased">
      <div className="w-full max-w-md h-screen bg-gray-50 dark:bg-gray-900 flex flex-col relative shadow-2xl border-x border-gray-200 dark:border-gray-800">
        {title && <TopBar title={title} showBack={showBack} />}
        <main className="flex-1 overflow-y-auto pb-20 no-scrollbar">
          {children}
        </main>
        {showBottomNav && <BottomNav />}
      </div>
    </div>
  );
}

