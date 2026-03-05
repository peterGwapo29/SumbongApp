'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import Layout from '@/components/mobile/Layout';
import Card from '@/components/mobile/Card';
import StatusBadge from '@/components/mobile/StatusBadge';
import { requestsApi } from '@/lib/api';
import { RequestStatus, Request } from '@/types';
import { useLoadingContext } from '@/components/mobile/LoadingProvider';

export default function RequestsPage() {
  const router = useRouter();
  const { startLoading, stopLoading } = useLoadingContext();
  const [filter, setFilter] = useState<RequestStatus | 'all'>('all');
  const [requests, setRequests] = useState<Request[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchRequests = async () => {
      try {
        const params = filter !== 'all' ? { status: filter } : undefined;
        const response = await requestsApi.getAll(params);
        // Handle response - check if it's wrapped in 'data' property or is direct array
        const data = Array.isArray(response) 
          ? response 
          : (response?.data || []);
        setRequests(Array.isArray(data) ? data : []);
      } catch (error) {
        console.error('Failed to fetch requests:', error);
        setRequests([]);
      } finally {
        setLoading(false);
      }
    };

    fetchRequests();
  }, [filter]);

  const handleNavigation = (path: string, message?: string) => {
    startLoading(message);
    setTimeout(() => {
      router.push(path);
      setTimeout(() => stopLoading(), 300);
    }, 100);
  };
  
  const filteredRequests = requests;

  const statusFilters: Array<{ value: RequestStatus | 'all'; label: string }> = [
    { value: 'all', label: 'All' },
    { value: 'created', label: 'Created' },
    { value: 'assigned', label: 'Assigned' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'resolved', label: 'Resolved' },
    { value: 'closed', label: 'Closed' },
  ];

  return (
    <Layout title="My Requests">
      <div className="p-4">
        {/* Filter Tabs */}
        <div className="flex gap-2 mb-4 overflow-x-auto pb-2">
          {statusFilters.map((status) => (
            <button
              key={status.value}
              onClick={() => setFilter(status.value)}
              className={`px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap ${
                filter === status.value
                  ? 'bg-blue-600 text-white'
                  : 'bg-white text-gray-700 border border-gray-300'
              }`}
            >
              {status.label}
            </button>
          ))}
        </div>

        {/* Requests List */}
        {loading ? (
          <Card>
            <div className="text-center py-8">
              <p className="text-gray-600">Loading requests...</p>
            </div>
          </Card>
        ) : filteredRequests.length > 0 ? (
          <div className="space-y-3">
            {filteredRequests.map((request) => (
              <Card
                key={request.id}
                onClick={() => {
                  const requestId = String(request.id || '');
                  if (requestId) {
                    handleNavigation(`/requests/${requestId}`, 'Loading request details...');
                  }
                }}
                loadingMessage="Loading request details..."
              >
                <div className="flex items-start justify-between mb-2">
                  <div className="flex-1">
                    <h3 className="font-semibold text-gray-900 mb-1">{request.title}</h3>
                    <p className="text-sm text-gray-600 mb-2 line-clamp-2">{request.description}</p>
                    <div className="flex items-center gap-2 text-xs text-gray-500 mb-2">
                      <span>📍 {
                        typeof request.location === 'object' 
                          ? request.location.address 
                          : request.address || 'No address'
                      }</span>
                    </div>
                    <div className="flex items-center justify-between">
                      <StatusBadge status={request.status} />
                      <span className="text-xs text-gray-500">
                        {new Date(request.created_at).toLocaleDateString()}
                      </span>
                    </div>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        ) : (
          <Card>
            <div className="text-center py-8">
              <p className="text-gray-600 mb-4">No requests found</p>
              <button
                onClick={() => handleNavigation('/create', 'Opening create request...')}
                className="text-blue-600 font-medium"
              >
                Create your first request
              </button>
            </div>
          </Card>
        )}
      </div>
    </Layout>
  );
}

