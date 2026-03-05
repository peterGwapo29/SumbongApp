'use client';

import { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { authApi, getUser, setUser, setAuthToken } from '@/lib/api';
import { User } from '@/types';

interface AuthContextType {
  user: User | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  register: (data: {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    mobile?: string;
    address?: string;
    user_type?: 'resident' | 'non_resident';
  }) => Promise<void>;
  logout: () => Promise<void>;
  updateUser: (data: { name?: string; mobile?: string; address?: string }) => Promise<void>;
  updateAvatar: (file: File) => Promise<void>;
  refreshUser: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUserState] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  const mergeUser = (current: User | null, incoming: User): User => {
    if (!current) {
      return incoming;
    }

    return {
      ...current,
      ...incoming,
      mobile: incoming.mobile ?? current.mobile,
      address: incoming.address ?? current.address,
    };
  };

  useEffect(() => {
    const initializeAuth = async () => {
      const storedUser = getUser();
      if (storedUser) {
        setUserState(storedUser);
        try {
          const freshUser = await authApi.getUser();
          setUserState((prev) => mergeUser(prev, freshUser));
        } catch {
          setAuthToken(null);
          setUser(null);
          setUserState(null);
        }
      }
      setLoading(false);
    };

    void initializeAuth();
  }, []);

  const login = async (email: string, password: string) => {
    const result = await authApi.login({ email, password });
    setUserState((prev) => mergeUser(prev, result.user));
  };

  const register = async (data: {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    mobile?: string;
    address?: string;
    user_type?: 'resident' | 'non_resident';
  }) => {
    const result = await authApi.register(data);
    setUserState((prev) => mergeUser(prev, result.user));
  };

  const logout = async () => {
    await authApi.logout();
    setUserState(null);
  };

  const updateUser = async (data: { name?: string; mobile?: string; address?: string }) => {
    const updatedUser = await authApi.updateProfile(data);
    setUserState((prev) => mergeUser(prev, updatedUser));
  };

  const updateAvatar = async (file: File) => {
    const updatedUser = await authApi.updateAvatar(file);
    setUserState((prev) => mergeUser(prev, updatedUser));
  };

  const refreshUser = async () => {
    const updatedUser = await authApi.getUser();
    setUserState((prev) => mergeUser(prev, updatedUser));
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        loading,
        login,
        register,
        logout,
        updateUser,
        updateAvatar,
        refreshUser,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
}

