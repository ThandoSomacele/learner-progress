# Learner Progress Dashboard - Coding Challenge

## Getting Started

Follow these steps to get the project up and running on your local machine:

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm

### Installation

1. **Install PHP dependencies:**

   ```bash
   composer install
   ```

2. **Install Node.js dependencies:**

   ```bash
   npm install
   ```

3. **Configure environment:**

   ```bash
   cp .env.example .env
   ```

4. **Generate application key:**

   ```bash
   php artisan key:generate
   ```

5. **Run database migrations and seeders:**

   ```bash
   php artisan migrate --seed
   ```

6. **Start the development server:**

   Option A - Start all services concurrently (recommended):

   ```bash
   composer dev
   ```

   This starts the Laravel server, queue worker, logs viewer, and Vite dev server simultaneously.

   Option B - Start services individually:

   ```bash
   # Terminal 1: Laravel server
   php artisan serve

   # Terminal 2: Vite dev server
   npm run dev

   # Terminal 3 (optional): Queue worker
   php artisan queue:listen

   # Terminal 4 (optional): View logs
   php artisan pail
   ```

7. **Access the application:**
   - Welcome page: <http://localhost:8000>
   - Learner Progress Dashboard: <http://localhost:8000/learner-progress>

### Running Tests

```bash
# Run all tests
composer test

# Or directly with artisan
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Code Quality

```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Check formatting without making changes
./vendor/bin/pint --test
```

## Deployment

### Deploy to Railway

This application is configured for easy deployment to [Railway](https://railway.app), which offers a free tier perfect for Laravel applications with SQLite.

#### Prerequisites

- A [Railway account](https://railway.app) (sign up with GitHub)
- Railway CLI (optional, for command-line deployment)

#### Deployment Steps

**Option 1: Deploy via Railway Dashboard (Recommended)**

1. Push your code to a GitHub repository
2. Go to [railway.app](https://railway.app) and click "Start a New Project"
3. Select "Deploy from GitHub repo"
4. Choose your repository
5. Railway will automatically detect the Laravel application and use Railpack (Railway's modern builder)
6. Click **Settings → Networking → Generate Domain** to create a public URL
7. Set the following environment variables in the Railway dashboard (**Variables** tab):
   - `APP_KEY`: Generate using `php artisan key:generate --show` locally (REQUIRED - app won't work without this!)
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_URL`: Your Railway app URL (e.g., `https://your-app-production.up.railway.app`)
   - All other variables from `.env.example`
8. Deploy! Railway will:
   - Install dependencies
   - Build frontend assets
   - Run migrations automatically
   - Start the application

**Option 2: Deploy via Railway CLI**

1. Install the Railway CLI:
   ```bash
   npm install -g @railway/cli
   ```

2. Login to Railway:
   ```bash
   railway login
   ```

3. Initialize and link your project:
   ```bash
   railway init
   railway link
   ```

4. Set environment variables:
   ```bash
   railway variables set APP_KEY=$(php artisan key:generate --show)
   railway variables set APP_ENV=production
   railway variables set APP_DEBUG=false
   # Add other environment variables as needed
   ```

5. Deploy:
   ```bash
   railway up
   ```

#### Configuration Files

The following file configures Railway deployment:

- **`railway.json`**: Specifies Railpack as the builder and deployment settings

Railway uses **Railpack**, which automatically handles Laravel applications with:
- FrankenPHP web server (modern, high-performance PHP server)
- Automatic database migrations and seeders
- Storage symlink creation
- Application optimization (config/route/view caching)

#### Post-Deployment

- Your app will be available at the domain you generated (e.g., `https://your-app-production.up.railway.app`)
- View logs in the Railway dashboard under the **Deployments** tab
- SQLite database persists in Railway's volume storage
- Each deployment automatically runs migrations and optimizations
- FrankenPHP provides high-performance request handling

#### Important Notes

- Railway's free tier includes 500 hours/month of usage
- The SQLite database is stored in a persistent volume
- For production, consider upgrading to a paid plan for better performance and uptime guarantees
