'use client';

import { RequestStatus } from '@/types';

interface StatusBadgeProps {
  status: RequestStatus | string | undefined;
}

export default function StatusBadge({ status }: StatusBadgeProps) {
  const statusConfig: Record<string, { label: string; color: string }> = {
    created: { label: 'Created', color: 'bg-gray-100 text-gray-800' },
    assigned: { label: 'Assigned', color: 'bg-blue-100 text-blue-800' },
    in_progress: { label: 'In Progress', color: 'bg-yellow-100 text-yellow-800' },
    resolved: { label: 'Resolved', color: 'bg-green-100 text-green-800' },
    closed: { label: 'Closed', color: 'bg-gray-100 text-gray-800' },
  };

  // Get config with fallback for unknown statuses
  const config = status && statusConfig[status] 
    ? statusConfig[status] 
    : { label: status || 'Unknown', color: 'bg-gray-100 text-gray-800' };

  return (
    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.color}`}>
      {config.label}
    </span>
  );
}

