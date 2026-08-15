#!/bin/sh
PORT=${PORT:-8000}

# Run migrations and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

echo "Starting Laravel server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT
