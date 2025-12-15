# Laravel RBAC API

A simple Role-Based Access Control (RBAC) API built with Laravel, Laravel Passport, and PostgreSQL.

## Features

- **Authentication**: User registration, login, logout using Laravel Passport
- **Custom RBAC System**: Roles and permissions without third-party packages
- **User CRUD**: Full user management with permission-based access control
- **External API Integration**: Fetch users from JSONPlaceholder API
- **Standardized Responses**: Consistent JSON response format

## Requirements

- Docker & Docker Compose
- Git

## Tech Stack

- Laravel 12.x
- Laravel Passport 13.x
- PostgreSQL 15
- PHP 8.2
- Nginx

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd <project-folder>
```

### 2. Copy environment file

```bash
cp .env.example .env
```

### 3. Update `.env` with PostgreSQL settings

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=rbac_api
DB_USERNAME=laravel
DB_PASSWORD=secret
```

### 4. Build and start Docker containers

```bash
docker compose build
docker compose up -d
```

### 5. Install dependencies (if needed)

```bash
docker compose exec app composer install
```

### 6. Generate application key

```bash
docker compose exec app php artisan key:generate
```

### 7. Run migrations and seeders

```bash
docker compose exec app php artisan migrate --seed
```

### 8. Install Passport

```bash
docker compose exec app php artisan passport:install
```

When prompted:
- Select "yes" to run migrations
- Select "yes" to create personal access client
- Select "0" for user provider

## API Base URL

```
http://localhost:8000/api
```

## Default Users (from seeders)

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@example.com | password123 |
| Admin | admin@example.com | password123 |
| Manager | manager@example.com | password123 |
| User | user@example.com | password123 |

## Roles & Permissions

### Roles

| Role | Description |
|------|-------------|
| super-admin | Full access to all features |
| admin | Administrative access |
| manager | Limited admin access (list, read, create, update users) |
| user | Basic access (list, read users only) |

### Permissions

| Permission Slug | Description |
|-----------------|-------------|
| users-list | View list of users |
| users-create | Create new users |
| users-read | View user details |
| users-update | Update user information |
| users-delete | Delete users |
| users-assign-roles | Assign roles to users |

## API Endpoints

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/auth/register` | Register new user | No |
| POST | `/api/auth/login` | Login user | No |
| POST | `/api/auth/logout` | Logout user | Yes |
| GET | `/api/auth/me` | Get current user | Yes |

### Users (Permission Required)

| Method | Endpoint | Description | Permission |
|--------|----------|-------------|------------|
| GET | `/api/users` | List all users | users-list |
| POST | `/api/users` | Create user | users-create |
| GET | `/api/users/{id}` | Get user by ID | users-read |
| PUT | `/api/users/{id}` | Update user | users-update |
| DELETE | `/api/users/{id}` | Delete user | users-delete |
| POST | `/api/users/{id}/assign-roles` | Assign roles | users-assign-roles |

### Roles (Admin Only)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/roles` | List all roles |
| POST | `/api/roles` | Create role |
| GET | `/api/roles/{id}` | Get role by ID |
| PUT | `/api/roles/{id}` | Update role |
| DELETE | `/api/roles/{id}` | Delete role |
| POST | `/api/roles/{id}/assign-permissions` | Assign permissions |

### Permissions (Admin Only)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/permissions` | List all permissions |
| POST | `/api/permissions` | Create permission |
| GET | `/api/permissions/{id}` | Get permission by ID |
| PUT | `/api/permissions/{id}` | Update permission |
| DELETE | `/api/permissions/{id}` | Delete permission |

### External API

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/external/users` | Fetch external users | Yes |

## Response Format

### Success Response

```json
{
  "success": true,
  "code": 200,
  "data": {},
  "message": "Success message"
}
```

### Error Response

```json
{
  "success": false,
  "code": 400,
  "data": {},
  "message": "Error message"
}
```

## Example API Calls

### Register

```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```

### Get Users (with token)

```bash
curl -X GET http://localhost:8000/api/users \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

### External API

```bash
curl -X GET http://localhost:8000/api/external/users \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

## Docker Commands

```bash
# Start containers
docker compose up -d

# Stop containers
docker compose down

# View logs
docker compose logs -f

# Execute artisan commands
docker compose exec app php artisan <command>

# Access PostgreSQL
docker compose exec db psql -U laravel -d rbac_api
```

## Testing

```bash
docker compose exec app php artisan test
```

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── RoleController.php
│   │   │   ├── PermissionController.php
│   │   │   └── ExternalApiController.php
│   │   └── Middleware/
│   │       ├── CheckPermission.php
│   │       └── CheckRole.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   └── Permission.php
│   └── Traits/
│       └── ApiResponse.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── PermissionSeeder.php
│       ├── RoleSeeder.php
│       └── UserSeeder.php
├── routes/
│   └── api.php
├── docker-compose.yml
├── Dockerfile
└── README.md
```

## License

MIT License
