#!/bin/bash

# Exit on fail
set -e

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Generate Passport Keys if they don't exist
# (You might want to persist these via Env Vars in production for real persistence,
# but generating them on startup works for stateless if tokens don't need to persist across restarts ideally)
php artisan passport:keys

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Start Apache in foreground
echo "Starting Apache..."
apache2-foreground
