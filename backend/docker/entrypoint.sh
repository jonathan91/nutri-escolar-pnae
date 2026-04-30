#!/bin/sh
set -e

# Set permissions
chown -R www-data:www-data /var/www/html/var 2>/dev/null || true

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g 'daemon off;'
