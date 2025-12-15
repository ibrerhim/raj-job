#!/bin/bash

# Exit on fail
set -e

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Run Seeders (Safe because our seeders employ updateOrCreate)
echo "Seeding database..."
php artisan db:seed --force

# Create Passport Client if not exists
# We check if the oauth_clients table is empty for personal access clients
CLIENT_EXISTS=$(php artisan tinker --execute="echo \Laravel\Passport\Client::where('personal_access_client', 1)->exists() ? 'true' : 'false';")

if [[ "$CLIENT_EXISTS" == *"false"* ]]; then
    echo "Creating Personal Access Client..."
    php artisan passport:client --personal --name="Railway Client" --no-interaction
else
    echo "Personal Access Client already exists."
fi

# Generate Passport Keys
# (Note: In production, these should ideally be set via ENV vars PASSPORT_PRIVATE_KEY / PASSPORT_PUBLIC_KEY
# so they don't regenerate on every restart, which invalidates tokens signed with the old keys)
php artisan passport:keys --force

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Start Apache in foreground
echo "Starting Apache..."
apache2-foreground
