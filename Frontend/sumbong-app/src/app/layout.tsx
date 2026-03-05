import type { Metadata, Viewport } from 'next';
import './style/globals.css';
import './style/layout.css';
import { LoadingProvider } from '@/components/mobile/LoadingProvider';
import { AuthProvider } from '@/contexts/AuthContext';
import { NotificationsProvider } from '@/components/mobile/NotificationsProvider';

export const metadata: Metadata = {
  title: 'Sumbong App - City Service Request System',
  description: 'Report issues and request city services',
};

export const viewport: Viewport = {
  width: 'device-width',
  initialScale: 1,
  maximumScale: 1,
  userScalable: false,
  themeColor: '#2563eb',
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <body>
        <AuthProvider>
          <LoadingProvider>
            <NotificationsProvider>
              <main>{children}</main>
            </NotificationsProvider>
          </LoadingProvider>
        </AuthProvider>
      </body>
    </html>
  )
}