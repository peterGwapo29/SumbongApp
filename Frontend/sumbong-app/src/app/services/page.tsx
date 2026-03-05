'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import Layout from '@/components/mobile/Layout';
import Card from '@/components/mobile/Card';
import { serviceTypesApi } from '@/lib/api';
import { ServiceType } from '@/types';
import { useLoadingContext } from '@/components/mobile/LoadingProvider';

export default function ServicesPage() {
  const router = useRouter();
  const { startLoading, stopLoading } = useLoadingContext();
  const [serviceTypes, setServiceTypes] = useState<ServiceType[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchServices = async () => {
      try {
        const response = await serviceTypesApi.getAll();
        // Handle response - check if it's wrapped in 'data' property or is direct array
        const data = Array.isArray(response) 
          ? response 
          : (response?.data || []);
        setServiceTypes(Array.isArray(data) 
          ? data.filter((s: ServiceType) => s.is_active !== false)
          : []);
      } catch (error) {
        console.error('Failed to fetch services:', error);
        setServiceTypes([]);
      } finally {
        setLoading(false);
      }
    };

    fetchServices();
  }, []);

  const handleNavigation = (path: string, message?: string) => {
    startLoading(message);
    setTimeout(() => {
      router.push(path);
      setTimeout(() => stopLoading(), 300);
    }, 100);
  };

  // Group services by department
  const servicesByDepartment = serviceTypes.reduce((acc, service) => {
    if (!acc[service.department]) {
      acc[service.department] = [];
    }
    acc[service.department].push(service);
    return acc;
  }, {} as Record<string, ServiceType[]>);

  if (loading) {
    return (
      <Layout title="All Services" showBack>
        <div className="p-4">
          <Card>
            <p className="text-center py-8 text-gray-600">Loading services...</p>
          </Card>
        </div>
      </Layout>
    );
  }

  return (
    <Layout title="All Services" showBack>
      <div className="p-4 space-y-6">
        {Object.keys(servicesByDepartment).length > 0 ? (
          Object.entries(servicesByDepartment).map(([department, services]) => (
            <div key={department}>
              <h2 className="text-lg font-semibold text-gray-900 mb-3">{department}</h2>
              <div className="space-y-2">
                {services.map((service) => (
                  <Card
                    key={service.id}
                    onClick={() => handleNavigation(`/create?service=${service.id}`, 'Opening service...')}
                    loadingMessage="Opening service..."
                  >
                    <div className="flex items-center">
                      <span className="text-3xl mr-4">{service.icon || '📋'}</span>
                      <div className="flex-1">
                        <h3 className="font-semibold text-gray-900 mb-1">{service.name}</h3>
                        <p className="text-sm text-gray-600">{service.description}</p>
                      </div>
                      <span className="text-gray-400">→</span>
                    </div>
                  </Card>
                ))}
              </div>
            </div>
          ))
        ) : (
          <Card>
            <p className="text-center py-8 text-gray-600">No services available</p>
          </Card>
        )}
      </div>
    </Layout>
  );
}

