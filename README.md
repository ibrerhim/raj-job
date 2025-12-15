Laravel RBAC API


To run this project, you only need:
- Docker Desktop
- Docker Compose
- Git

Technology Stack

- Framework: Laravel 12.x
- Authentication: Laravel Passport 13.x
- Database: PostgreSQL 15
- Language: PHP 8.2
- Server: Nginx

Installation Guide

Follow these steps to get the project up and running on your local machine.

1. Clone the repository
Open your terminal and clone the project to your local machine.
git clone <repository-url>
cd <project-folder>

2. Configure environment
Copy the example environment file to create your own configuration.
cp .env.example .env

set the following variables in the .env file

APP_NAME="Laravel RBAC API"
APP_ENV=local
APP_KEY=base64:g52bkAx0+2w5LgMzWo7vdmXyn//+Gpce70d0q2kNFsI=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=rbac_api
DB_USERNAME=laravel
DB_PASSWORD=secret

CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database

3. Configure Database
Open the .env file and ensure the database settings match the Docker configuration (connection: pgsql, host: db, port: 5432, database: rbac_api, username: laravel, password: secret).

4. Start the Application
Build and start the Docker containers. This pulls all necessary images and sets up the environment.
docker compose build
docker compose up -d

5. Install Dependencies
Run composer inside the container to install PHP dependencies.
docker compose exec app composer install

6. Generate App Key
Generate the application encryption key.
docker compose exec app php artisan key:generate

7. Setup Database
Run the migrations to create the database tables and seed them with default roles and users.
docker compose exec app php artisan migrate --seed

8. Install Passport
Initialize Laravel Passport to generate the encryption keys for API tokens.
docker compose exec app php artisan passport:install

Select "yes" when asked to run migrations (if prompted) and to create the personal access client. Enter "0" for the user provider.

The API is now live at http://localhost:8000/api

Default Test Users

The database is pre-populated with these users for testing purposes. All use the password: password123

Super Admin (superadmin@example.com) - Full access to everything
Admin (admin@example.com) - Administrative access
Manager (manager@example.com) - Can manage users (create, read, update)
User (user@example.com) - Read-only access to user lists

API Documentation

Here is a quick overview of the available endpoints.

Authentication
POST /api/auth/register - Register a new account
POST /api/auth/login - Login to receive an access token
POST /api/auth/logout - Invalidate current token
GET /api/auth/me - Get current user profile

User Management (Requires Permissions)
GET /api/users - List all users
POST /api/users - Create a new user
GET /api/users/{id} - View a specific user
PUT /api/users/{id} - Update a user
DELETE /api/users/{id} - Remove a user
POST /api/users/{id}/assign-roles - Assign roles to a user

External Data
GET /api/external/users - Fetch users from the external JSONPlaceholder API

Testing with Postman

Included in the project is a "postman_collection.json" file that makes testing the API easy.

1. Import Collection
Open Postman, click "Import" in the top left, and drag in the "postman_collection.json" file from this project folder.

2. Authenticate
Expand the "Laravel RBAC API" collection, go to the "Auth" folder, and open the "Login" request.
Click "Send" (the default credentials are for the admin).

3. Token Automation
A script runs automatically after a successful login to save your access token. You do not need to copy-paste it manually.

4. Test Endpoints
Now you can run any other request in the collection (like "List Users"). The token will be automatically applied to the Authorization header.

Useful Commands

Start containers: docker compose up -d
Stop containers: docker compose down
View logs: docker compose logs -f
Run tests: docker compose exec app php artisan test
Access database: docker compose exec db psql -U laravel -d rbac_api

