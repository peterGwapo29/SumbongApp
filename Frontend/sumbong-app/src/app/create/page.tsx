'use client';

import { useState, useEffect, Suspense } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import Layout from '@/components/mobile/Layout';
import Card from '@/components/mobile/Card';
import Button from '@/components/mobile/Button';
import { serviceTypesApi, requestsApi, attachmentsApi } from '@/lib/api';
import { ServiceType } from '@/types';

function CreateRequestForm() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const preselectedService = searchParams.get('service');

  const [serviceTypes, setServiceTypes] = useState<ServiceType[]>([]);
  const [formData, setFormData] = useState({
    service_type_id: preselectedService || '',
    title: '',
    description: '',
    address: '',
    barangay: '',
    city: '',
    priority: 'medium' as 'low' | 'medium' | 'high' | 'urgent',
  });
  const [files, setFiles] = useState<File[]>([]);
  const [filePreviews, setFilePreviews] = useState<string[]>([]);
  const [uploadingFiles, setUploadingFiles] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [loadingServices, setLoadingServices] = useState(true);

  useEffect(() => {
    // Fetch service types
    serviceTypesApi.getAll()
      .then((response) => {
        // Handle response - check if it's wrapped in 'data' property or is direct array
        const data = Array.isArray(response) 
          ? response 
          : (response?.data || []);
        setServiceTypes(Array.isArray(data) ? data : []);
        if (preselectedService) {
          setFormData(prev => ({ ...prev, service_type_id: preselectedService }));
        }
      })
      .catch((err) => {
        setError('Failed to load service types. Please refresh the page.');
        console.error(err);
        setServiceTypes([]);
      })
      .finally(() => {
        setLoadingServices(false);
      });
  }, [preselectedService]);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const newFiles = Array.from(e.target.files);
      const currentFileCount = files.length;
      
      setFiles(prev => [...prev, ...newFiles]);
      
      // Create previews for image files
      newFiles.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = (e) => {
            if (e.target?.result) {
              setFilePreviews(prev => {
                const updated = [...prev];
                updated[currentFileCount + index] = e.target!.result as string;
                return updated;
              });
            }
          };
          reader.readAsDataURL(file);
        } else {
          setFilePreviews(prev => {
            const updated = [...prev];
            updated[currentFileCount + index] = '';
            return updated;
          });
        }
      });
    }
    // Reset input to allow selecting the same file again
    e.target.value = '';
  };

  const removeFile = (index: number) => {
    setFiles(prev => prev.filter((_, i) => i !== index));
    setFilePreviews(prev => prev.filter((_, i) => i !== index));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    
    try {
      // Create the request
      const request = await requestsApi.create({
        service_type_id: formData.service_type_id,
        title: formData.title,
        description: formData.description,
        address: formData.address,
        barangay: formData.barangay || undefined,
        city: formData.city || undefined,
        priority: formData.priority,
      });

      // Extract request ID from response (handle both direct and wrapped responses)
      const requestId = request?.id || request?.data?.id || (typeof request === 'object' && 'id' in request ? request.id : null);
      
      // Upload attachments if any
      if (files.length > 0 && requestId) {
        setUploadingFiles(true);
        try {
          const uploadPromises = files.map((file) =>
            attachmentsApi.upload(String(requestId), file).catch((err) => {
              console.error('Failed to upload attachment:', err);
              return null; // Continue even if attachment upload fails
            })
          );
          
          await Promise.all(uploadPromises);
        } catch (uploadError) {
          console.error('Error uploading files:', uploadError);
          // Continue even if upload fails
        } finally {
          setUploadingFiles(false);
        }
      }

      if (requestId) {
        router.push(`/requests/${String(requestId)}`);
      } else {
        router.push('/requests');
      }
    } catch (err: any) {
      setError(err.message || 'Failed to create request. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const selectedService = serviceTypes.find(s => s.id === formData.service_type_id);

  if (loadingServices) {
    return (
      <Layout title="Create Request" showBack>
        <div className="p-4">
          <Card>
            <p className="text-center py-8">Loading services...</p>
          </Card>
        </div>
      </Layout>
    );
  }

  return (
    <Layout title="Create Request" showBack>
      <div className="p-4">
        <form onSubmit={handleSubmit} className="space-y-4">
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
              {error}
            </div>
          )}

          {/* Service Type Selection */}
          <Card>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Service Type *
            </label>
            <select
              value={formData.service_type_id}
              onChange={(e) => setFormData({ ...formData, service_type_id: e.target.value })}
              required
              className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white"
            >
              <option value="">Select a service...</option>
              {serviceTypes.filter(s => s.is_active !== false).map((service) => (
                <option key={service.id} value={service.id}>
                  {service.icon || '📋'} {service.name} - {service.department}
                </option>
              ))}
            </select>
            {selectedService && (
              <p className="text-sm text-gray-600 mt-2">{selectedService.description}</p>
            )}
          </Card>

          {/* Title */}
          <Card>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Title *
            </label>
            <input
              type="text"
              value={formData.title}
              onChange={(e) => setFormData({ ...formData, title: e.target.value })}
              required
              className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white placeholder:text-gray-400"
              placeholder="Brief description of the issue"
            />
          </Card>

          {/* Description */}
          <Card>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Description *
            </label>
            <textarea
              value={formData.description}
              onChange={(e) => setFormData({ ...formData, description: e.target.value })}
              required
              rows={4}
              className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white placeholder:text-gray-400"
              placeholder="Provide detailed information about your request..."
            />
          </Card>

          {/* Location */}
          <Card>
            <h3 className="text-sm font-medium text-gray-700 mb-3">Location *</h3>
            <div className="space-y-3">
              <div>
                <label className="block text-xs text-gray-600 mb-1">Address</label>
                <input
                  type="text"
                  value={formData.address}
                  onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                  required
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white placeholder:text-gray-400"
                  placeholder="Street address"
                />
              </div>
              <div>
                <label className="block text-xs text-gray-600 mb-1">Barangay</label>
                <input
                  type="text"
                  value={formData.barangay}
                  onChange={(e) => setFormData({ ...formData, barangay: e.target.value })}
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white placeholder:text-gray-400"
                  placeholder="Barangay"
                />
              </div>
              <div>
                <label className="block text-xs text-gray-600 mb-1">City</label>
                <input
                  type="text"
                  value={formData.city}
                  onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white placeholder:text-gray-400"
                  placeholder="City"
                />
              </div>
            </div>
          </Card>

          {/* Priority */}
          <Card>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Priority
            </label>
            <select
              value={formData.priority}
              onChange={(e) => setFormData({ ...formData, priority: e.target.value as any })}
              className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white"
            >
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
          </Card>

          {/* Attachments */}
          <Card>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Attachments (Optional)
            </label>
            <div className="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
              <span className="text-2xl mb-2 block">📷</span>
              <p className="text-sm text-gray-600 mb-2">Add photos or documents</p>
              <input
                type="file"
                multiple
                onChange={handleFileChange}
                accept="image/*,video/*,.pdf,.doc,.docx"
                className="hidden"
                id="file-input"
              />
              <span className="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                Choose Files
              </span>
              {files.length > 0 && (
                <div className="mt-4 space-y-3">
                  <p className="text-sm font-medium text-gray-700 mb-2">Selected files ({files.length}):</p>
                  <div className="grid grid-cols-2 gap-3">
                    {files.map((file, index) => (
                      <div key={index} className="relative border border-gray-200 rounded-lg p-2 bg-gray-50">
                        {file.type.startsWith('image/') && filePreviews[index] ? (
                          <div className="relative">
                            <img
                              src={filePreviews[index]}
                              alt={file.name}
                              className="w-full h-24 object-cover rounded"
                            />
                            <button
                              type="button"
                              onClick={() => removeFile(index)}
                              className="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                            >
                              ×
                            </button>
                          </div>
                        ) : (
                          <div className="relative">
                            <div className="w-full h-24 bg-gray-200 rounded flex items-center justify-center">
                              <span className="text-2xl">📄</span>
                            </div>
                            <button
                              type="button"
                              onClick={() => removeFile(index)}
                              className="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                            >
                              ×
                            </button>
                          </div>
                        )}
                        <p className="text-xs text-gray-600 mt-1 truncate" title={file.name}>
                          {file.name}
                        </p>
                        <p className="text-xs text-gray-500">
                          {(file.size / 1024).toFixed(1)} KB
                        </p>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </Card>

          {/* Submit */}
          <div className="pb-4">
            <Button type="submit" fullWidth disabled={loading || uploadingFiles}>
              {uploadingFiles 
                ? 'Uploading files...' 
                : loading 
                  ? 'Submitting...' 
                  : 'Submit Request'}
            </Button>
          </div>
        </form>
      </div>
    </Layout>
  );
}

export default function CreateRequestPage() {
  return (
    <Suspense fallback={
      <Layout title="Create Request" showBack>
        <div className="p-4">
          <Card>
            <p className="text-center py-8">Loading...</p>
          </Card>
        </div>
      </Layout>
    }>
      <CreateRequestForm />
    </Suspense>
  );
}

