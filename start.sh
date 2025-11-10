#!/bin/bash
# Navigate to the app folder
cd Balipure

# Install dependencies (optional if already done)
composer install --no-interaction --optimize-autoloader
npm install
npm run build

# Cache configs (optional, speeds up Laravel)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Serve the Laravel app
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
