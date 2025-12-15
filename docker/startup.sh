#!/bin/bash

# Remove set -e so the container doesn't crash on a single error
# set -e

echo "--- STARTING LARAVEL APP ---"

# Configure Apache to listen on the Railway-assigned PORT
PORT=${PORT:-80}
echo "Configuring Apache to listen on port $PORT..."
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# 1. Attempt to cache config
echo "Caching config..."
php artisan config:cache || echo "Config cache failed"

# 2. Run migrations (with error handling)
echo "Running migrations..."
php artisan migrate --force

if [ $? -ne 0 ]; then
    echo "❌ MIGRATION FAILED!"
    echo "Check your usage of variables, specifically DB_HOST, DB_USERNAME, DB_PASSWORD."
    echo "Container will continue starting so you can debug..."
else
    echo "✅ Migrations successful."
    # Only generate keys if migration worked
    php artisan passport:keys || echo "Passport keys generation failed"
fi

# 3. Clear other caches
echo "Clearing caches..."
php artisan cache:clear
php artisan route:clear

# 4. Start Apache
echo "Starting Apache on port $PORT..."
apache2-foreground
