'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import Layout from '@/components/mobile/Layout';
import Card from '@/components/mobile/Card';
import Button from '@/components/mobile/Button';
import { serviceTypesApi, requestsApi, notificationsApi } from '@/lib/api';
import StatusBadge from '@/components/mobile/StatusBadge';
import { useLoadingContext } from '@/components/mobile/LoadingProvider';
import { ServiceType, Request } from '@/types';

export default function HomePage() {
  const router = useRouter();
  const { startLoading, stopLoading } = useLoadingContext();
  const [serviceTypes, setServiceTypes] = useState<ServiceType[]>([]);
  const [recentRequests, setRecentRequests] = useState<Request[]>([]);
  const [unreadNotifications, setUnreadNotifications] = useState(0);
  const [loading, setLoading] = useState(true);

  const fetchData = async () => {
    try {
      const [servicesResponse, requestsResponse, notificationsResponse] = await Promise.all([
        serviceTypesApi.getAll(),
        requestsApi.getAll(),
        notificationsApi.getAll(),
      ]);

      const services = Array.isArray(servicesResponse)
        ? servicesResponse
        : servicesResponse?.data || [];
      setServiceTypes(
        Array.isArray(services)
          ? services.filter((s: ServiceType) => s.is_active !== false).slice(0, 4)
          : []
      );

      const requests = Array.isArray(requestsResponse)
        ? requestsResponse
        : requestsResponse?.data || [];
      setRecentRequests(Array.isArray(requests) ? requests.slice(0, 3) : []);

      const notifications = Array.isArray(notificationsResponse)
        ? notificationsResponse
        : notificationsResponse?.data || [];
      const unread = Array.isArray(notifications)
        ? notifications.filter(
            (n: any) => n.deliveries && n.deliveries.length > 0 && !n.deliveries[0].read
          ).length
        : 0;
      setUnreadNotifications(unread);
    } catch (error) {
      console.error('Failed to fetch data:', error);
      setServiceTypes([]);
      setRecentRequests([]);
      setUnreadNotifications(0);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const load = async () => {
      try {
        await fetchData();
      } catch {
        // handled in fetchData
      }
    };

    void load();

    const interval = setInterval(() => {
      void fetchData();
    }, 30000);

    return () => clearInterval(interval);
  }, []);

  const handleNavigation = (path: string, message?: string) => {
    startLoading(message);
    setTimeout(() => {
      router.push(path);
      setTimeout(() => stopLoading(), 300);
    }, 100);
  };

  return (
    <Layout title="Home">
      <div className="p-4 space-y-4">
        {/* Welcome Section */}
        <Card>
          <h2 className="text-xl font-semibold text-gray-900 mb-2">Welcome back!</h2>
          <p className="text-gray-600">Quick access to city services</p>
        </Card>

        {/* Quick Actions */}
        <div>
          <h3 className="text-lg font-semibold text-gray-900 mb-3">Quick Actions</h3>
          <div className="grid grid-cols-2 gap-3">
            <Button
              onClick={() => handleNavigation('/create', 'Opening create request...')}
              className="h-24 flex flex-col items-center justify-center"
              showLoading={false}
            >
              <span className="text-2xl mb-1">➕</span>
              <span>New Request</span>
            </Button>
            <Button
              variant="secondary"
              onClick={() => handleNavigation('/requests', 'Loading requests...')}
              className="h-24 flex flex-col items-center justify-center"
              showLoading={false}
            >
              <span className="text-2xl mb-1">📋</span>
              <span>My Requests</span>
            </Button>
          </div>
        </div>

        {/* Service Types */}
        <div>
          <h3 className="text-lg font-semibold text-gray-900 mb-3">Available Services</h3>
          {loading ? (
            <Card>
              <p className="text-center py-4 text-gray-600">Loading services...</p>
            </Card>
          ) : serviceTypes.length > 0 ? (
            <div className="space-y-2">
              {serviceTypes.map((service) => (
                <Card
                  key={service.id}
                  onClick={() => handleNavigation(`/create?service=${service.id}`, 'Opening service...')}
                  loadingMessage="Opening service..."
                >
                  <div className="flex items-center">
                    <span className="text-2xl mr-3">{service.icon || '📋'}</span>
                    <div className="flex-1">
                      <h4 className="font-medium text-gray-900">{service.name}</h4>
                      <p className="text-sm text-gray-600">{service.department}</p>
                    </div>
                    <span className="text-gray-400">→</span>
                  </div>
                </Card>
              ))}
            </div>
          ) : (
            <Card>
              <p className="text-center py-4 text-gray-600">No services available</p>
            </Card>
          )}
          <Button
            variant="outline"
            fullWidth
            className="mt-3"
            onClick={() => handleNavigation('/services', 'Loading services...')}
            showLoading={false}
          >
            View All Services
          </Button>
        </div>

        {/* Recent Requests */}
        <div>
          <div className="flex items-center justify-between mb-3">
            <h3 className="text-lg font-semibold text-gray-900">Recent Requests</h3>
            <button
              onClick={() => handleNavigation('/requests', 'Loading requests...')}
              className="text-sm text-blue-600"
            >
              View All
            </button>
          </div>
          {loading ? (
            <Card>
              <p className="text-center py-4 text-gray-600">Loading requests...</p>
            </Card>
          ) : recentRequests.length > 0 ? (
            <div className="space-y-2">
              {recentRequests.map((request) => (
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
                  <div className="flex items-start justify-between">
                    <div className="flex-1">
                      <h4 className="font-medium text-gray-900 mb-1">{request.title}</h4>
                      <p className="text-sm text-gray-600 mb-2">
                        {typeof request.location === 'object' ? request.location.address : request.address || 'No address'}
                      </p>
                      <StatusBadge status={request.status} />
                    </div>
                  </div>
                </Card>
              ))}
            </div>
          ) : (
            <Card>
              <p className="text-gray-600 text-center py-4">No requests yet</p>
            </Card>
          )}
        </div>

        {/* Notifications Alert */}
        {unreadNotifications > 0 && (
          <Card className="bg-blue-50 border-blue-200">
            <div className="flex items-center justify-between">
              <div>
                <h4 className="font-medium text-blue-900">New Notifications</h4>
                <p className="text-sm text-blue-700">{unreadNotifications} unread</p>
              </div>
              <Button
                variant="primary"
                onClick={() => handleNavigation('/notifications', 'Loading notifications...')}
                showLoading={false}
              >
                View
              </Button>
            </div>
          </Card>
        )}
      </div>
    </Layout>
  );
}
