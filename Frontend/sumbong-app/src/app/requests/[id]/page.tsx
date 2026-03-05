'use client';

import { use, useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import Layout from '@/components/mobile/Layout';
import Card from '@/components/mobile/Card';
import StatusBadge from '@/components/mobile/StatusBadge';
import Button from '@/components/mobile/Button';
import { requestsApi } from '@/lib/api';
import { Request } from '@/types';
import { useLoadingContext } from '@/components/mobile/LoadingProvider';

export default function RequestDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const router = useRouter();
  const { startLoading, stopLoading } = useLoadingContext();
  const { id } = use(params);
  const [request, setRequest] = useState<Request | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchRequest = async () => {
      // Validate ID before making API call
      if (!id || id === 'undefined' || id === 'null') {
        setError('Invalid request ID');
        setRequest(null);
        setLoading(false);
        return;
      }

      try {
        const response = await requestsApi.getById(String(id));
        // Handle response - check if it's wrapped in 'data' property or is direct object
        const data = response?.data || response;
        setRequest(data);
      } catch (err: any) {
        setError(err.message || 'Failed to load request');
        setRequest(null);
      } finally {
        setLoading(false);
      }
    };

    fetchRequest();
  }, [id]);

  const handleNavigation = (path: string, message?: string) => {
    startLoading(message);
    setTimeout(() => {
      router.push(path);
      setTimeout(() => stopLoading(), 300);
    }, 100);
  };

  if (loading) {
    return (
      <Layout title="Request Details" showBack>
        <div className="p-4">
          <Card>
            <p className="text-center text-gray-600 py-8">Loading request...</p>
          </Card>
        </div>
      </Layout>
    );
  }

  if (error || !request) {
    return (
      <Layout title="Request Not Found" showBack>
        <div className="p-4">
          <Card>
            <p className="text-center text-gray-600 py-8">{error || 'Request not found'}</p>
            <Button onClick={() => handleNavigation('/requests', 'Loading requests...')} fullWidth showLoading={false}>
              Back to Requests
            </Button>
          </Card>
        </div>
      </Layout>
    );
  }

  return (
    <Layout title="Request Details" showBack>
      <div className="p-4 space-y-4">
        {/* Request Header */}
        <Card>
          <div className="flex items-start justify-between mb-3">
            <h2 className="text-xl font-semibold text-gray-900 flex-1">{request.title}</h2>
            <StatusBadge status={request.status} />
          </div>
          <p className="text-gray-700 mb-4">{request.description}</p>
          
          <div className="space-y-2 text-sm">
            <div className="flex items-center text-gray-600">
              <span className="font-medium mr-2">Service:</span>
              <span>{request.service_type?.name || 'N/A'}</span>
            </div>
            <div className="flex items-center text-gray-600">
              <span className="font-medium mr-2">Priority:</span>
              <span className="capitalize">{request.priority}</span>
            </div>
            <div className="flex items-center text-gray-600">
              <span className="font-medium mr-2">Location:</span>
              <span>
                {typeof request.location === 'object' 
                  ? request.location.address 
                  : request.address || 'No address'}
              </span>
            </div>
            <div className="flex items-center text-gray-600">
              <span className="font-medium mr-2">Created:</span>
              <span>{new Date(request.created_at).toLocaleString()}</span>
            </div>
            <div className="flex items-center text-gray-600">
              <span className="font-medium mr-2">Last Updated:</span>
              <span>{new Date(request.updated_at).toLocaleString()}</span>
            </div>
          </div>
        </Card>

        {/* Status Timeline */}
        {request.status_history && request.status_history.length > 0 && (
          <Card>
            <h3 className="font-semibold text-gray-900 mb-3">Status Timeline</h3>
            <div className="space-y-3">
              {request.status_history.map((history, index) => (
                <div key={history.id} className="flex items-start">
                  <div className="flex flex-col items-center mr-3">
                    <div className={`w-3 h-3 rounded-full ${
                      history.status === 'created' ? 'bg-blue-600' :
                      history.status === 'in_progress' ? 'bg-yellow-600' :
                      history.status === 'resolved' ? 'bg-green-600' :
                      'bg-gray-600'
                    }`}></div>
                    {index < request.status_history!.length - 1 && (
                      <div className="w-0.5 h-full bg-gray-300 mt-1"></div>
                    )}
                  </div>
                  <div className="flex-1">
                    <p className="font-medium text-gray-900 capitalize">
                      {history.status.replace('_', ' ')}
                    </p>
                    <p className="text-sm text-gray-600">
                      {new Date(history.created_at).toLocaleString()}
                    </p>
                    {history.notes && (
                      <p className="text-sm text-gray-500 mt-1">{history.notes}</p>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </Card>
        )}

        {/* Attachments */}
        {request.attachments && request.attachments.length > 0 && (
          <Card>
            <h3 className="font-semibold text-gray-900 mb-3">Attachments</h3>
            <div className="space-y-2">
              {request.attachments.map((attachment) => (
                <div key={attachment.id} className="flex items-center p-2 bg-gray-50 rounded">
                  <span className="text-xl mr-2">📎</span>
                  <div className="flex-1">
                    <p className="text-sm font-medium">{attachment.file_name}</p>
                    <p className="text-xs text-gray-600">{attachment.file_type}</p>
                  </div>
                </div>
              ))}
            </div>
          </Card>
        )}

        {/* Actions */}
        <div className="space-y-2">
          {request.status === 'resolved' && (
            <Button variant="outline" fullWidth>
              Add Feedback
            </Button>
          )}
          <Button variant="secondary" fullWidth onClick={() => handleNavigation('/requests', 'Loading requests...')} showLoading={false}>
            Back to Requests
          </Button>
        </div>
      </div>
    </Layout>
  );
}

