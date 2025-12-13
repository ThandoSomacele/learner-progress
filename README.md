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
