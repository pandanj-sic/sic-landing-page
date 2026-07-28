#!/bin/sh
set -e

# Cache configuration, routes, and views for performance
echo "Caching Laravel configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure storage link exists
php artisan storage:link || true

# Set up database and run migrations
DB_CONN=$(php -r "echo env('DB_CONNECTION');" 2>/dev/null || echo "sqlite")
if [ "$DB_CONN" = "sqlite" ]; then
    echo "SQLite database detected."
    mkdir -p database
    touch database/database.sqlite
    
    if [ ! -s database/database.sqlite ]; then
        echo "SQLite database empty. Running migrations and seeders..."
        php artisan migrate --force
        php artisan db:seed --force
    else
        echo "Running migrations..."
        php artisan migrate --force
    fi
else
    echo "Database ($DB_CONN) detected. Running migrations..."
    php artisan migrate --force || echo "Migration failed or database still starting up."
fi

# Ensure storage and bootstrap/cache are writable
echo "Setting correct permissions..."
chown -R www-data:www-data storage bootstrap/cache database

# Run the command passed to the container (e.g. frankenphp run)
exec "$@"
