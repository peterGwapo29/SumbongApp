'use client';

import { ReactNode, useState } from 'react';
import { useLoadingContext } from './LoadingProvider';

interface CardProps {
  children: ReactNode;
  className?: string;
  onClick?: () => void | Promise<void>;
  showLoading?: boolean;
  loadingMessage?: string;
}

export default function Card({ 
  children, 
  className = '', 
  onClick,
  showLoading = true,
  loadingMessage 
}: CardProps) {
  let loadingContext: ReturnType<typeof useLoadingContext> | null = null;
  try {
    loadingContext = useLoadingContext();
  } catch {
    // Context not available, loading will be disabled
  }

  const [isLoading, setIsLoading] = useState(false);

  const handleClick = async () => {
    if (onClick) {
      if (showLoading && loadingContext) {
        setIsLoading(true);
        loadingContext.startLoading(loadingMessage);
        try {
          await onClick();
        } finally {
          setTimeout(() => {
            setIsLoading(false);
            loadingContext?.stopLoading();
          }, 300);
        }
      } else {
        onClick();
      }
    }
  };

  return (
    <div
      className={`bg-white rounded-lg shadow-sm border border-gray-200 p-4 ${
        onClick ? 'cursor-pointer hover:shadow-md transition-shadow' : ''
      } ${isLoading ? 'opacity-75 pointer-events-none' : ''} ${className}`}
      onClick={handleClick}
    >
      {children}
    </div>
  );
}

