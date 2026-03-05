import { ServiceType, Request, User, Notification } from '@/types';

// Mock Service Types
export const mockServiceTypes: ServiceType[] = [
  {
    id: '1',
    name: 'Garbage Pickup',
    description: 'Request for garbage collection',
    department: 'Sanitation',
    icon: '🗑️',
    created_at: new Date().toISOString(),
  },
  {
    id: '2',
    name: 'Streetlight Repair',
    description: 'Report broken or malfunctioning streetlights',
    department: 'Public Works',
    icon: '💡',
    created_at: new Date().toISOString(),
  },
  {
    id: '3',
    name: 'Building Permit',
    description: 'Apply for building construction permit',
    department: 'Engineering',
    icon: '🏗️',
    created_at: new Date().toISOString(),
  },
  {
    id: '4',
    name: 'Business Permit',
    description: 'Apply for business operation permit',
    department: 'Business Licensing',
    icon: '🏪',
    created_at: new Date().toISOString(),
  },
  {
    id: '5',
    name: 'Pothole Repair',
    description: 'Report potholes on roads',
    department: 'Public Works',
    icon: '🛣️',
    created_at: new Date().toISOString(),
  },
  {
    id: '6',
    name: 'Drainage Issue',
    description: 'Report drainage problems',
    department: 'Public Works',
    icon: '🌊',
    created_at: new Date().toISOString(),
  },
];

// Mock User
export const mockUser: User = {
  id: '1',
  name: 'Juan Dela Cruz',
  email: 'juan@example.com',
  mobile: '+63 912 345 6789',
  address: '123 Main Street, Barangay 1',
  user_type: 'resident',
  verified: true,
  role_id: '1',
  created_at: new Date().toISOString(),
  updated_at: new Date().toISOString(),
};

// Mock Requests
export const mockRequests: Request[] = [
  {
    id: '1',
    user_id: '1',
    service_type_id: '1',
    title: 'Garbage not collected',
    description: 'Garbage has not been collected for 3 days in our area',
    status: 'in_progress',
    location: {
      address: '123 Main Street, Barangay 1',
      latitude: 14.5995,
      longitude: 120.9842,
      barangay: 'Barangay 1',
      city: 'Manila',
    },
    priority: 'high',
    created_at: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString(),
    updated_at: new Date().toISOString(),
  },
  {
    id: '2',
    user_id: '1',
    service_type_id: '2',
    title: 'Broken streetlight',
    description: 'Streetlight on corner of Main and First Street is not working',
    status: 'assigned',
    location: {
      address: 'Corner Main and First Street',
      latitude: 14.6000,
      longitude: 120.9850,
      barangay: 'Barangay 1',
      city: 'Manila',
    },
    priority: 'medium',
    created_at: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000).toISOString(),
    updated_at: new Date().toISOString(),
  },
];

// Mock Notifications
export const mockNotifications: Notification[] = [
  {
    id: '1',
    title: 'New Service Available',
    message: 'Business permit applications are now available online',
    type: 'alert',
    target_audience: 'all',
    created_at: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000).toISOString(),
  },
  {
    id: '2',
    title: 'Request Update',
    message: 'Your garbage pickup request is now in progress',
    type: 'request_update',
    target_audience: 'residents',
    created_at: new Date(Date.now() - 2 * 60 * 60 * 1000).toISOString(),
  },
];

