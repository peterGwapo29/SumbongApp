# Sumbong App Backend - Laravel API

A comprehensive Laravel backend API for managing service requests, users, notifications, and more.

## Features

- **Authentication**: User registration, login, and profile management using Laravel Sanctum
- **Service Types Management**: CRUD operations for service types
- **Request Management**: Create, view, update, and manage service requests
- **File Attachments**: Upload and manage attachments for requests
- **Notifications**: System-wide notifications with delivery tracking
- **User Management**: Admin panel for managing users
- **Role-Based Access Control**: Different roles (admin, staff, clerk, inspector, resident)
- **Request Assignment**: Assign requests to staff members
- **Status Tracking**: Track request status changes with history
- **Feedback System**: Users can provide feedback on resolved requests
- **Admin Dashboard**: Comprehensive admin panel for managing the system

## Installation

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure Database**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sumbong_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

6. **Configure CORS**
   Update `config/cors.php` to allow your frontend domain:
   ```php
   'allowed_origins' => ['http://localhost:3000'], // Your Next.js frontend URL
   ```

## API Endpoints

### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires auth)
- `GET /api/user` - Get current user (requires auth)
- `PUT /api/user` - Update user profile (requires auth)

### Service Types
- `GET /api/service-types` - List all active service types
- `GET /api/service-types/{id}` - Get service type details
- `POST /api/service-types` - Create service type (admin only)
- `PUT /api/service-types/{id}` - Update service type (admin only)
- `DELETE /api/service-types/{id}` - Delete service type (admin only)

### Requests
- `GET /api/requests` - List requests (filtered by user role)
- `POST /api/requests` - Create a new request
- `GET /api/requests/{id}` - Get request details
- `PUT /api/requests/{id}` - Update request
- `DELETE /api/requests/{id}` - Delete request
- `PUT /api/requests/{id}/status` - Update request status (staff/admin only)
- `POST /api/requests/{id}/assign` - Assign request to staff (staff/admin only)

### Attachments
- `POST /api/requests/{requestId}/attachments` - Upload attachment
- `DELETE /api/attachments/{id}` - Delete attachment

### Notifications
- `GET /api/notifications` - Get user notifications
- `GET /api/notifications/{id}` - Get notification details
- `PUT /api/notifications/{id}/read` - Mark notification as read
- `PUT /api/notifications/read-all` - Mark all notifications as read

### Feedback
- `POST /api/requests/{requestId}/feedback` - Add feedback to request
- `GET /api/requests/{requestId}/feedback` - Get request feedback

### Admin Endpoints
- `GET /api/admin/users` - List all users (admin only)
- `GET /api/admin/requests` - List all requests (admin only)
- `GET /api/admin/stats` - Get dashboard statistics (admin only)
- `POST /api/admin/notifications` - Create notification (admin only)

## Admin Panel

Access the admin panel at `/admin` (requires authentication and admin role).

### Default Admin Credentials
- Email: `admin@sumbong.app`
- Password: `password`

**⚠️ Change these credentials in production!**

## Database Structure

### Tables
- `users` - User accounts
- `roles` - User roles and permissions
- `service_types` - Available service types
- `requests` - Service requests/complaints
- `attachments` - File attachments for requests
- `assignments` - Request assignments to staff
- `request_status_history` - Status change history
- `notifications` - System notifications
- `notification_deliveries` - Notification delivery tracking
- `feedback` - User feedback on requests
- `audit_logs` - System audit logs

## API Authentication

The API uses Laravel Sanctum for authentication. Include the token in the Authorization header:

```
Authorization: Bearer {token}
```

## File Uploads

Attachments are stored in `storage/app/public/attachments`. Make sure to:
1. Create the storage link: `php artisan storage:link`
2. Set proper permissions on `storage` directory

## Development

Run the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## Testing

Run tests:
```bash
php artisan test
```

## Seeding

Seed the database with initial data:
```bash
php artisan db:seed
```

This will create:
- Default roles (admin, staff, clerk, inspector, resident)
- Admin user
- Sample service types

## CORS Configuration

Update `config/cors.php` to allow requests from your frontend:
```php
'allowed_origins' => ['http://localhost:3000'],
'allowed_origins_patterns' => [],
'allowed_headers' => ['*'],
'allowed_methods' => ['*'],
'supports_credentials' => true,
```

## Security Notes

1. Change default admin credentials
2. Use strong passwords in production
3. Enable HTTPS in production
4. Regularly update dependencies
5. Review and adjust CORS settings
6. Set proper file permissions

## License

MIT
