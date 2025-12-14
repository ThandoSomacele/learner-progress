#!/bin/bash
set -e

echo "Running database migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force

echo "Creating storage symlinks..."
php artisan storage:link || true

echo "Optimizing application..."
php artisan optimize

echo "Starting FrankenPHP..."
exec frankenphp run --config /app/Caddyfile
