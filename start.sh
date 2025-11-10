#!/bin/bash

# Install PHP dependencies
composer install --no-interaction --optimize-autoloader

# Cache configs
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Serve Laravel app on Render-assigned port
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
