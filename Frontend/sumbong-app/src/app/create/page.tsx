'use client';

import { useState, useEffect, useRef, Suspense } from 'react';
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
  const cameraInputRef = useRef<HTMLInputElement>(null);
  const galleryInputRef = useRef<HTMLInputElement>(null);

  const addFiles = (newFiles: File[]) => {
    const imageFiles = newFiles.filter((file) => file.type.startsWith('image/'));
    if (imageFiles.length === 0) return;

    const currentFileCount = files.length;
    setFiles((prev) => [...prev, ...imageFiles]);

    imageFiles.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = (event) => {
        if (event.target?.result) {
          setFilePreviews((prev) => {
            const updated = [...prev];
            updated[currentFileCount + index] = event.target!.result as string;
            return updated;
          });
        }
      };
      reader.readAsDataURL(file);
    });
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      addFiles(Array.from(e.target.files));
    }
    e.target.value = '';
  };

  useEffect(() => {
    serviceTypesApi.getAll()
      .then((response) => {
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
      
      // Upload area photos if any
      if (files.length > 0 && requestId) {
        setUploadingFiles(true);
        try {
          const results = await Promise.all(
            files.map((file) =>
              attachmentsApi.upload(String(requestId), file).catch((err) => {
                console.error('Failed to upload photo:', err);
                return null;
              })
            )
          );

          const failedCount = results.filter((result) => result === null).length;
          if (failedCount > 0) {
            console.warn(`${failedCount} photo(s) failed to upload`);
          }
        } catch (uploadError) {
          console.error('Error uploading photos:', uploadError);
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
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Service Type *
            </label>
            <select
              value={formData.service_type_id}
              onChange={(e) => setFormData({ ...formData, service_type_id: e.target.value })}
              required
              className="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white bg-white dark:bg-gray-800"
            >
              <option value="" className="dark:bg-gray-800 dark:text-white">Select a service...</option>
              {serviceTypes.filter(s => s.is_active !== false).map((service) => (
                <option key={service.id} value={service.id} className="dark:bg-gray-800 dark:text-white">
                  {service.icon || '📋'} {service.name} - {service.department}
                </option>
              ))}
            </select>
            {selectedService && (
              <p className="text-sm text-gray-600 dark:text-gray-400 mt-2">{selectedService.description}</p>
            )}
          </Card>

          {/* Title */}
          <Card>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Title *
            </label>
            <input
              type="text"
              value={formData.title}
              onChange={(e) => setFormData({ ...formData, title: e.target.value })}
              required
              className="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white bg-white dark:bg-gray-800 placeholder:text-gray-400 dark:placeholder:text-gray-500"
              placeholder="Brief description of the issue"
            />
          </Card>

          {/* Description */}
          <Card>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Description *
            </label>
            <textarea
              value={formData.description}
              onChange={(e) => setFormData({ ...formData, description: e.target.value })}
              required
              rows={4}
              className="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white bg-white dark:bg-gray-800 placeholder:text-gray-400 dark:placeholder:text-gray-500"
              placeholder="Provide detailed information about your request..."
            />
          </Card>

          {/* Location */}
          <Card>
            <h3 className="text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">Location *</h3>
            <div className="space-y-3">
              <div>
                <label className="block text-xs text-gray-600 dark:text-gray-400 mb-1">Address</label>
                <input
                  type="text"
                  value={formData.address}
                  onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                  required
                  className="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white bg-white dark:bg-gray-800 placeholder:text-gray-400 dark:placeholder:text-gray-500"
                  placeholder="Street address"
                />
              </div>
              <div>
                <label className="block text-xs text-gray-600 dark:text-gray-400 mb-1">Barangay</label>
                <input
                  type="text"
                  value={formData.barangay}
                  onChange={(e) => setFormData({ ...formData, barangay: e.target.value })}
                  className="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white bg-white dark:bg-gray-800 placeholder:text-gray-400 dark:placeholder:text-gray-500"
                  placeholder="Barangay"
                />
              </div>
              <div>
                <label className="block text-xs text-gray-600 dark:text-gray-400 mb-1">City</label>
                <input
                  type="text"
                  value={formData.city}
                  onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                  className="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white bg-white dark:bg-gray-800 placeholder:text-gray-400 dark:placeholder:text-gray-500"
                  placeholder="City"
                />
              </div>
            </div>
          </Card>

          {/* Priority */}
          <Card>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Priority
            </label>
            <select
              value={formData.priority}
              onChange={(e) => setFormData({ ...formData, priority: e.target.value as any })}
              className="w-full px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 dark:text-white bg-white dark:bg-gray-800"
            >
              <option value="low" className="dark:bg-gray-800 dark:text-white">Low</option>
              <option value="medium" className="dark:bg-gray-800 dark:text-white">Medium</option>
              <option value="high" className="dark:bg-gray-800 dark:text-white">High</option>
              <option value="urgent" className="dark:bg-gray-800 dark:text-white">Urgent</option>
            </select>
          </Card>

          {/* Area Photo */}
          <Card>
            <label className="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
              Area Photo
            </label>
            <p className="text-xs text-gray-500 dark:text-gray-400 mb-3">
              Take a photo or choose from your gallery so admins can see the situation
            </p>
            <div className="border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-6 text-center bg-gray-50/50 dark:bg-gray-800/40">
              <span className="text-3xl mb-2 block">📷</span>
              <p className="text-sm text-gray-600 dark:text-gray-300 mb-4">
                {files.length > 0
                  ? `${files.length} photo${files.length > 1 ? 's' : ''} selected`
                  : 'No photos added yet'}
              </p>

              <input
                ref={cameraInputRef}
                type="file"
                accept="image/*"
                capture="environment"
                onChange={handleFileChange}
                className="hidden"
              />
              <input
                ref={galleryInputRef}
                type="file"
                accept="image/*"
                multiple
                onChange={handleFileChange}
                className="hidden"
              />

              <div className="flex flex-col sm:flex-row gap-2 justify-center">
                <button
                  type="button"
                  onClick={() => cameraInputRef.current?.click()}
                  className="inline-flex items-center justify-center px-4 py-2.5 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100"
                >
                  📸 Take Photo
                </button>
                <button
                  type="button"
                  onClick={() => galleryInputRef.current?.click()}
                  className="inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                  🖼️ Choose from Gallery
                </button>
              </div>

              {files.length > 0 && (
                <div className="mt-4 space-y-3">
                  <div className="grid grid-cols-2 gap-3">
                    {files.map((file, index) => (
                      <div key={index} className="relative border border-gray-200 rounded-lg p-2 bg-gray-50">
                        <div className="relative">
                          {filePreviews[index] ? (
                            // eslint-disable-next-line @next/next/no-img-element
                            <img
                              src={filePreviews[index]}
                              alt={file.name}
                              className="w-full h-28 object-cover rounded"
                            />
                          ) : (
                            <div className="w-full h-28 bg-gray-200 rounded flex items-center justify-center">
                              <span className="text-2xl">📷</span>
                            </div>
                          )}
                          <button
                            type="button"
                            onClick={() => removeFile(index)}
                            className="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600"
                            aria-label="Remove photo"
                          >
                            ×
                          </button>
                        </div>
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
                ? 'Uploading photos...'
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

