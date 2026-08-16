'use client';

import { useRouter } from 'next/navigation';

interface TopBarProps {
  title: string;
  showBack?: boolean;
}

export default function TopBar({ title, showBack = false }: TopBarProps) {
  const router = useRouter();

  return (
    <header className="sticky top-0 z-40 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <div className="flex items-center h-14 px-4">
        {showBack && (
          <button
            onClick={() => router.back()}
            className="mr-3 p-2 -ml-2 text-gray-700 dark:text-gray-200"
            aria-label="Go back"
          >
            <span className="text-xl">←</span>
          </button>
        )}
        <h1 className="text-lg font-semibold text-gray-900 dark:text-white">{title}</h1>
      </div>
    </header>
  );
}

