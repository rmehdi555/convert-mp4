## Laravel Docker Project

A Laravel application with Docker setup including PHP-FPM, Nginx, MySQL, Redis, and phpMyAdmin.

### Quick Start

1. **Start services:**
```powershell
docker compose up -d --build
```

2. **Configure `.env`:**
```ini
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password

# Note: External MySQL port is 3308 to avoid conflicts

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Note: External Redis port is 6380 to avoid conflicts

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

3. **Run Laravel setup:**
```powershell
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose exec app php artisan storage:link
docker compose exec app php artisan queue:work
```

4. **Access the application:**
- App: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`

### Services
- **App**: PHP 8.2-FPM with Laravel
- **Webserver**: Nginx
- **Database**: MySQL 8.0
- **Cache**: Redis
- **Admin**: phpMyAdmin

### Development
- **Run migration**:`docker compose exec app php artisan migrate`
- **Queue Worker**: `docker compose exec app php artisan queue:work`
- **Stop/Restart**: `docker compose down && docker compose up -d`
