# Docker Setup for iRequest Demo

This directory contains Docker configuration files for the iRequest Laravel application.

## Quick Start

1. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Update `.env` file with Docker database settings:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=irequest
   DB_USERNAME=irequest
   DB_PASSWORD=root

   REDIS_HOST=redis
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

3. **Build and start containers:**
   ```bash
   docker-compose up -d --build
   ```

4. **Install dependencies:**
   ```bash
   docker-compose exec app composer install
   ```

5. **Generate application key:**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. **Run migrations:**
   ```bash
   docker-compose exec app php artisan migrate
   ```

7. **Set permissions:**
   ```bash
   docker-compose exec app chmod -R 775 storage bootstrap/cache
   docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
   ```

## Accessing the Application

- **Application:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081

## Useful Commands

### Container Management
```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose stop

# Stop and remove containers
docker-compose down

# View logs
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

### Artisan Commands
```bash
# Run any artisan command
docker-compose exec app php artisan [command]

# Examples:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

### Composer Commands
```bash
docker-compose exec app composer install
docker-compose exec app composer update
docker-compose exec app composer require [package]
```

### Database Access
```bash
# MySQL CLI
docker-compose exec db mysql -u irequest -proot irequest

# Or use phpMyAdmin at http://localhost:8081
```

### Redis CLI
```bash
docker-compose exec redis redis-cli
```

## Services

- **app**: PHP 7.4-FPM application container
- **nginx**: Nginx web server
- **db**: MySQL 8.0 database
- **redis**: Redis cache/session store
- **phpmyadmin**: Database management interface (optional)

## Volumes

- `dbdata`: Persistent MySQL data
- `redisdata`: Persistent Redis data

## Network

All services are connected via the `irequest-network` bridge network.

## Troubleshooting

### Permission Issues
```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Clear Cache
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

### Rebuild Containers
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

