'use client';

import { ReactNode, ButtonHTMLAttributes, useState } from 'react';
import { useLoadingContext } from './LoadingProvider';

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  children: ReactNode;
  variant?: 'primary' | 'secondary' | 'outline' | 'danger';
  fullWidth?: boolean;
  showLoading?: boolean;
  loadingMessage?: string;
}

export default function Button({
  children,
  variant = 'primary',
  fullWidth = false,
  className = '',
  onClick,
  showLoading = true,
  loadingMessage,
  ...props
}: ButtonProps) {
  let loadingContext: ReturnType<typeof useLoadingContext> | null = null;
  try {
    loadingContext = useLoadingContext();
  } catch {
    // Context not available, loading will be disabled
  }

  const [isLocalLoading, setIsLocalLoading] = useState(false);

  const baseStyles = 'px-4 py-3 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed';
  
  const variants = {
    primary: 'bg-blue-600 text-white hover:bg-blue-700',
    secondary: 'bg-gray-200 text-gray-900 hover:bg-gray-300',
    outline: 'border-2 border-blue-600 text-blue-600 hover:bg-blue-50',
    danger: 'bg-red-600 text-white hover:bg-red-700',
  };

  const handleClick = async (e: React.MouseEvent<HTMLButtonElement>) => {
    if (onClick) {
      if (showLoading && loadingContext) {
        setIsLocalLoading(true);
        loadingContext.startLoading(loadingMessage);
        try {
          await onClick(e);
        } finally {
          setTimeout(() => {
            setIsLocalLoading(false);
            loadingContext?.stopLoading();
          }, 300);
        }
      } else {
        onClick(e);
      }
    }
  };

  return (
    <button
      className={`${baseStyles} ${variants[variant]} ${fullWidth ? 'w-full' : ''} ${className} ${isLocalLoading ? 'opacity-75' : ''}`}
      onClick={handleClick}
      disabled={props.disabled || isLocalLoading}
      {...props}
    >
      {isLocalLoading ? (
        <span className="flex items-center justify-center">
          <span className="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin mr-2"></span>
          {children}
        </span>
      ) : (
        children
      )}
    </button>
  );
}

