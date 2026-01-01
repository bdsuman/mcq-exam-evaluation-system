#!/bin/bash
set -e

# Wait for database to be ready
echo "Waiting for database..."
sleep 5

# Fix storage permissions
echo "Setting up Laravel storage permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Install Composer dependencies
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --optimize-autoloader || true
fi

# Run migrations (ignore errors, migrations handled separately)
echo "Running migrations..."
php artisan migrate --force 2>&1 | head -20 || true

# Clear and cache config
echo "Optimizing Laravel..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

echo "Initialization complete!"

# Start supervisord in foreground
echo "Starting supervisord (php-fpm + scheduler)..."
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
