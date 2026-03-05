// API Configuration
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

// Helper function to get auth token from localStorage
const getAuthToken = (): string | null => {
  if (typeof window === 'undefined') return null;
  return localStorage.getItem('auth_token');
};

// Helper function to set auth token
const setAuthToken = (token: string | null): void => {
  if (typeof window === 'undefined') return;
  if (token) {
    localStorage.setItem('auth_token', token);
  } else {
    localStorage.removeItem('auth_token');
  }
};

// Helper function to get user from localStorage
const getUser = () => {
  if (typeof window === 'undefined') return null;
  const userStr = localStorage.getItem('auth_user');
  return userStr ? JSON.parse(userStr) : null;
};

// Helper function to set user
const setUser = (user: any): void => {
  if (typeof window === 'undefined') return;
  if (user) {
    localStorage.setItem('auth_user', JSON.stringify(user));
  } else {
    localStorage.removeItem('auth_user');
  }
};

// Base fetch function with auth
const apiFetch = async (
  endpoint: string,
  options: RequestInit = {}
): Promise<Response> => {
  const token = getAuthToken();
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    ...options.headers,
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    ...options,
    headers,
  });

  if (response.status === 401) {
    // Unauthorized - clear auth and redirect to login
    setAuthToken(null);
    setUser(null);
    if (typeof window !== 'undefined') {
      window.location.href = '/login';
    }
    throw new Error('Unauthorized');
  }

  if (!response.ok) {
    const error = await response.json().catch(() => ({ message: 'An error occurred' }));
    throw new Error(error.message || `HTTP error! status: ${response.status}`);
  }

  return response;
};

// Auth API
export const authApi = {
  register: async (data: {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    mobile?: string;
    address?: string;
    user_type?: 'resident' | 'non_resident';
  }) => {
    const response = await apiFetch('/register', {
      method: 'POST',
      body: JSON.stringify(data),
    });
    const result = await response.json();
    if (result.token) {
      setAuthToken(result.token);
      setUser(result.user);
    }
    return result;
  },

  login: async (data: { email: string; password: string }) => {
    const response = await apiFetch('/login', {
      method: 'POST',
      body: JSON.stringify(data),
    });
    const result = await response.json();
    if (result.token) {
      setAuthToken(result.token);
      setUser(result.user);
    }
    return result;
  },

  logout: async () => {
    try {
      await apiFetch('/logout', { method: 'POST' });
    } catch (error) {
      // Continue even if API call fails
    } finally {
      setAuthToken(null);
      setUser(null);
    }
  },

  getUser: async () => {
    const response = await apiFetch('/user');
    const user = await response.json();
    setUser(user);
    return user;
  },

  updateProfile: async (data: {
    name?: string;
    mobile?: string;
    address?: string;
  }) => {
    const response = await apiFetch('/user', {
      method: 'PUT',
      body: JSON.stringify(data),
    });
    const user = await response.json();
    setUser(user);
    return user;
  },

  updateAvatar: async (file: File) => {
    const formData = new FormData();
    formData.append('avatar', file);

    const token = getAuthToken();
    const headers: HeadersInit = {
      Accept: 'application/json',
    };

    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }

    const response = await fetch(`${API_BASE_URL}/user/avatar`, {
      method: 'POST',
      headers,
      body: formData,
    });

    if (!response.ok) {
      const error = await response.json().catch(() => ({ message: 'Upload failed' }));
      throw new Error(error.message || 'Upload failed');
    }

    const user = await response.json();
    setUser(user);
    return user;
  },
};

// Service Types API
export const serviceTypesApi = {
  getAll: async () => {
    const response = await apiFetch('/service-types');
    return response.json();
  },

  getById: async (id: string) => {
    const response = await apiFetch(`/service-types/${id}`);
    return response.json();
  },
};

// Requests API
export const requestsApi = {
  getAll: async (params?: { status?: string; service_type_id?: string }) => {
    const queryString = params
      ? '?' + new URLSearchParams(params as any).toString()
      : '';
    const response = await apiFetch(`/requests${queryString}`);
    return response.json();
  },

  getById: async (id: string) => {
    const response = await apiFetch(`/requests/${id}`);
    return response.json();
  },

  create: async (data: {
    service_type_id: string;
    title: string;
    description: string;
    address: string;
    barangay?: string;
    city?: string;
    latitude?: number;
    longitude?: number;
    priority?: 'low' | 'medium' | 'high' | 'urgent';
  }) => {
    const response = await apiFetch('/requests', {
      method: 'POST',
      body: JSON.stringify(data),
    });
    return response.json();
  },

  update: async (id: string, data: {
    title?: string;
    description?: string;
    status?: string;
    priority?: string;
  }) => {
    const response = await apiFetch(`/requests/${id}`, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
    return response.json();
  },

  updateStatus: async (id: string, status: string, notes?: string) => {
    const response = await apiFetch(`/requests/${id}/status`, {
      method: 'PUT',
      body: JSON.stringify({ status, notes }),
    });
    return response.json();
  },
};

// Attachments API
export const attachmentsApi = {
  upload: async (requestId: string, file: File) => {
    const formData = new FormData();
    formData.append('file', file);

    const token = getAuthToken();
    const headers: HeadersInit = {
      'Accept': 'application/json',
    };

    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }

    const response = await fetch(
      `${API_BASE_URL}/requests/${requestId}/attachments`,
      {
        method: 'POST',
        headers,
        body: formData,
      }
    );

    if (!response.ok) {
      const error = await response.json().catch(() => ({ message: 'Upload failed' }));
      throw new Error(error.message || 'Upload failed');
    }

    return response.json();
  },

  delete: async (id: string) => {
    const response = await apiFetch(`/attachments/${id}`, {
      method: 'DELETE',
    });
    return response.json();
  },
};

// Notifications API
export const notificationsApi = {
  getAll: async () => {
    const response = await apiFetch('/notifications');
    return response.json();
  },

  getById: async (id: string) => {
    const response = await apiFetch(`/notifications/${id}`);
    return response.json();
  },

  markAsRead: async (id: string) => {
    const response = await apiFetch(`/notifications/${id}/read`, {
      method: 'PUT',
    });
    return response.json();
  },

  markAllAsRead: async () => {
    const response = await apiFetch('/notifications/read-all', {
      method: 'PUT',
    });
    return response.json();
  },
};

// Feedback API
export const feedbackApi = {
  create: async (requestId: string, data: {
    comment: string;
    rating?: number;
  }) => {
    const response = await apiFetch(`/requests/${requestId}/feedback`, {
      method: 'POST',
      body: JSON.stringify(data),
    });
    return response.json();
  },

  getByRequestId: async (requestId: string) => {
    const response = await apiFetch(`/requests/${requestId}/feedback`);
    return response.json();
  },
};

// Export auth helpers
export { getAuthToken, setAuthToken, getUser, setUser };

