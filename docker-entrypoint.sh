#!/bin/bash
set -e

echo "Waiting for database to be ready..."
until php artisan db:show &> /dev/null 2>&1; do
    echo "Database is unavailable - sleeping"
    sleep 2
done

echo "Database is ready!"

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force || true

# Seed database if needed
if [ "$DB_SEED" = "true" ]; then
    echo "Seeding database..."
    php artisan db:seed --force || true
fi

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Clear and cache config (only in production)
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizing application..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

exec "$@"

