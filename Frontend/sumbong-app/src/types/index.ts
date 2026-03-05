// User Entity
export interface User {
  id: string;
  name: string;
  email: string;
  mobile?: string | null;
  address?: string | null;
  avatar_url?: string;
  user_type: 'resident' | 'non_resident';
  verified: boolean;
  role_id: string;
  role?: Role;
  created_at: string;
  updated_at: string;
}

// Role Entity
export interface Role {
  id: string;
  name: 'resident' | 'staff' | 'admin' | 'clerk' | 'inspector';
  permissions: string[];
  created_at: string;
}

// ServiceType Entity
export interface ServiceType {
  id: string;
  name: string;
  description: string;
  department: string;
  icon?: string;
  is_active?: boolean;
  created_at: string;
}

// Request Status
export type RequestStatus = 'created' | 'assigned' | 'in_progress' | 'resolved' | 'closed';

// Request Entity
export interface Request {
  id: string | number;
  user_id: string;
  user?: User;
  service_type_id: string;
  service_type?: ServiceType;
  title: string;
  description: string;
  status: RequestStatus;
  location: Location | string;
  address?: string;
  barangay?: string;
  city?: string;
  priority: 'low' | 'medium' | 'high' | 'urgent';
  created_at: string;
  updated_at: string;
  attachments?: Attachment[];
  assignments?: Assignment[];
  status_history?: RequestStatusHistory[];
  feedback?: Feedback[];
}

// Location (as attributes in Request)
export interface Location {
  address: string;
  latitude?: number;
  longitude?: number;
  barangay?: string;
  city?: string;
}

// RequestStatusHistory Entity
export interface RequestStatusHistory {
  id: string;
  request_id: string;
  status: RequestStatus;
  notes?: string;
  changed_by: string;
  changed_by_user?: User;
  created_at: string;
}

// Assignment Entity
export interface Assignment {
  id: string;
  request_id: string;
  user_id: string;
  user?: User;
  assigned_by: string;
  assigned_at: string;
  status: 'active' | 'completed';
}

// Attachment Entity
export interface Attachment {
  id: string;
  request_id: string;
  file_url: string;
  file_type: 'image' | 'video' | 'document';
  file_name: string;
  file_size: number;
  uploaded_by: string;
  created_at: string;
}

// Feedback Entity
export interface Feedback {
  id: string;
  request_id?: string;
  user_id: string;
  user?: User;
  comment: string;
  rating?: number;
  created_at: string;
}

// Notification Entity
export interface Notification {
  id: string;
  title: string;
  message: string;
  type: 'alert' | 'request_update' | 'assignment' | 'system';
  target_audience?: 'all' | 'residents' | 'staff';
  created_at: string;
  deliveries?: NotificationDelivery[];
}

// NotificationDelivery Entity
export interface NotificationDelivery {
  id: string;
  notification_id: string;
  notification?: Notification;
  user_id: string;
  user?: User;
  read: boolean;
  read_at?: string;
  delivered_at: string;
}

// AuditLog Entity
export interface AuditLog {
  id: string;
  user_id: string;
  user?: User;
  action: string;
  entity_type: string;
  entity_id: string;
  details?: Record<string, any>;
  ip_address?: string;
  created_at: string;
}

