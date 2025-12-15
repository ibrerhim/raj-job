# Docker Deployment Guide for Railway

This guide will walk you through deploying your Laravel RBAC API to Railway using **Docker**.

## Prerequisites

1.  GitHub repository with your code pushed.
2.  Railway account.

## Step 1: Create Project & Database

1.  **New Project** > **Deploy from GitHub repo**.
2.  Select your repo.
3.  Add **PostgreSQL** database service (Right click > New > Database > PostgreSQL).

## Step 2: Configure Service to use Dockerfile

1.  Click on your **Laravel App** service.
2.  Go to **Settings**.
3.  Scroll down to **Build**.
4.  **Dockerfile Path**: Enter `Dockerfile.prod` (Make sure not to use the default `Dockerfile` which is for dev).
5.  Railway should automatically detect it's a Docker deployment now.

## Step 3: Environment Variables

Go to the **Variables** tab and add these. Use the "Raw Editor" to paste:

```env
APP_NAME="Laravel RBAC API"
APP_ENV=production
APP_KEY=  <-- Generate one: php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://<your-railway-url>.up.railway.app
LOG_CHANNEL=stderr

DB_CONNECTION=pgsql
DB_HOST=${{Postgres.PGHOST}}
DB_PORT=${{Postgres.PGPORT}}
DB_DATABASE=${{Postgres.PGDATABASE}}
DB_USERNAME=${{Postgres.PGUSER}}
DB_PASSWORD=${{Postgres.PGPASSWORD}}

FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Important for Docker/Apache
PORT=80
```

## Step 4: First Deployment

1.  Once variables are saved, Railway might trigger a redeploy. If not, click **Deploy**.
2.  Watch the logs. You should see "Running migrations..." followed by Apache starting.
3.  Go to **Settings** > **Networking** > **Generate Domain** to get your public URL.
4.  Update `APP_URL` variable with this domain.

## Step 5: Post-Deploy Setup using Railway CLI or Web Console

You need to seed the database and create the passport client once.

1.  Go to the service **"Connect"** tab or just use the **"Command"** palette (Cmd+K on Mac inside Railway).
2.  Select **"Run Command"** (or Shell).
3.  Run:
    ```bash
    php artisan db:seed
    php artisan passport:client --personal --name="Railway Client"
    ```

## Important Notes

*   **Passport Keys**: The `startup.sh` generates new passport keys every time the container restarts. This means existing tokens might become invalid after a restart/redeploy.
*   **Fix**: To make tokens persistent, generate keys locally (`php artisan passport:keys`), view them (`cat storage/oauth-private.key`), and save their content into Environment Variables `PASSPORT_PRIVATE_KEY` and `PASSPORT_PUBLIC_KEY` in Railway.
