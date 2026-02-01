# Quick Setup Guide

## Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL/MariaDB or SQLite

## Step-by-Step Setup

1. **Install PHP Dependencies**
   ```bash
   cd Backend/sumbong-app-backend
   composer install
   ```

2. **Install Laravel Sanctum**
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Update .env File**
   ```env
   APP_URL=http://localhost:8000
   FRONTEND_URL=http://localhost:3000
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sumbong_app
   DB_USERNAME=root
   DB_PASSWORD=
   
   SANCTUM_STATEFUL_DOMAINS=localhost:3000
   SESSION_DOMAIN=localhost
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed Database**
   ```bash
   php artisan db:seed
   ```

7. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

8. **Start Development Server**
   ```bash
   php artisan serve
   ```

## Default Admin Account
- Email: `admin@sumbong.app`
- Password: `password`

**⚠️ IMPORTANT: Change these credentials immediately after first login!**

## API Base URL
```
http://localhost:8000/api
```

## Frontend Integration

In your Next.js frontend, create an API client:

```typescript
// lib/api.ts
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

export const api = {
  async request(endpoint: string, options: RequestInit = {}) {
    const token = localStorage.getItem('token');
    
    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...(token && { Authorization: `Bearer ${token}` }),
        ...options.headers,
      },
    });

    if (!response.ok) {
      throw new Error(`API Error: ${response.statusText}`);
    }

    return response.json();
  },
};
```

## Testing the API

You can test the API using curl or Postman:

```bash
# Register a user
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password"
  }'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }'

# Get service types (requires token)
curl -X GET http://localhost:8000/api/service-types \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Troubleshooting

### CORS Issues
If you encounter CORS errors, make sure:
1. Your frontend URL is in `SANCTUM_STATEFUL_DOMAINS` in `.env`
2. Update `config/sanctum.php` if needed

### Storage Issues
If file uploads don't work:
1. Ensure `storage/app/public` directory exists
2. Run `php artisan storage:link`
3. Check file permissions on `storage` directory

### Database Issues
If migrations fail:
1. Check database credentials in `.env`
2. Ensure database exists
3. Try `php artisan migrate:fresh` (⚠️ This will delete all data)

