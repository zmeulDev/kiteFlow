# Chio

Chio is a modern web application built with [Laravel](https://laravel.com).

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL or PostgreSQL

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd chio
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install frontend dependencies:
   ```bash
   npm install
   npm run build
   ```

4. Set up the environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configure your database settings in the `.env` file, then run the migrations:
   ```bash
   php artisan migrate
   ```

6. Serve the application:
   ```bash
   php artisan serve
   ```

## License

This project is proprietary software. All rights reserved.
