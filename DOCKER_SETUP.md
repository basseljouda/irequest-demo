# Docker Setup Guide for iRequest Demo

This guide will help you set up and run the iRequest Laravel application using Docker.

## Prerequisites

- Docker Desktop (or Docker Engine + Docker Compose)
- Git
- At least 4GB of available RAM

## Quick Start

### Option 1: Using the Setup Script (Recommended)

```bash
# Make the script executable (Linux/Mac)
chmod +x docker-setup.sh

# Run the setup script
./docker-setup.sh
```

### Option 2: Manual Setup

1. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Update `.env` file with Docker settings:**
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
- **MySQL:** localhost:3306
- **Redis:** localhost:6379

## Docker Services

### Services Overview

| Service | Container Name | Port | Description |
|---------|---------------|------|-------------|
| app | irequest_app | 9000 | PHP 7.4-FPM application |
| nginx | irequest_nginx | 8080 | Nginx web server |
| db | irequest_db | 3306 | MySQL 8.0 database |
| redis | irequest_redis | 6379 | Redis cache/session store |
| phpmyadmin | irequest_phpmyadmin | 8081 | Database management UI |

## Common Commands

### Using Docker Compose

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

# Rebuild containers
docker-compose build --no-cache
docker-compose up -d
```

### Using Makefile (Alternative)

```bash
# Show all available commands
make help

# Build containers
make build

# Start containers
make up

# Stop containers
make down

# Full installation
make install

# Run migrations
make migrate

# Run tests
make test

# Access shell
make shell

# Clean cache
make clean
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
docker-compose exec app php artisan route:list
docker-compose exec app php artisan test
```

### Composer Commands

```bash
docker-compose exec app composer install
docker-compose exec app composer update
docker-compose exec app composer require [package]
docker-compose exec app composer dump-autoload
```

### Database Access

```bash
# MySQL CLI
docker-compose exec db mysql -u irequest -proot irequest

# Or use phpMyAdmin at http://localhost:8081
# Login: irequest / root
```

### Redis CLI

```bash
docker-compose exec redis redis-cli
```

## File Structure

```
.
├── Dockerfile                 # PHP-FPM container definition
├── docker-compose.yml        # Docker services orchestration
├── .dockerignore            # Files to exclude from Docker build
├── docker-setup.sh          # Automated setup script
├── Makefile                 # Convenience commands
├── docker/
│   ├── nginx/
│   │   └── default.conf     # Nginx configuration
│   ├── php/
│   │   └── local.ini        # PHP configuration
│   ├── mysql/
│   │   └── my.cnf           # MySQL configuration
│   └── README.md            # Docker documentation
└── DOCKER_SETUP.md          # This file
```

## Troubleshooting

### Permission Issues

```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Clear All Cache

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear
```

### Database Connection Issues

1. Check if database container is running:
   ```bash
   docker-compose ps
   ```

2. Check database logs:
   ```bash
   docker-compose logs db
   ```

3. Verify `.env` settings match docker-compose.yml

### Port Already in Use

If ports 8080, 8081, 3306, or 6379 are already in use, modify `docker-compose.yml`:

```yaml
ports:
  - "8080:80"  # Change 8080 to another port
```

### Rebuild Everything

```bash
# Stop and remove all containers, volumes, and networks
docker-compose down -v

# Rebuild from scratch
docker-compose build --no-cache
docker-compose up -d
```

### View Container Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

## Development Workflow

### Making Code Changes

Code changes are automatically reflected since the project directory is mounted as a volume. However, you may need to:

1. Clear cache after config changes:
   ```bash
   docker-compose exec app php artisan config:clear
   ```

2. Rebuild assets (if using Laravel Mix):
   ```bash
   docker-compose exec app npm run dev
   ```

### Running Tests

```bash
# Run all tests
docker-compose exec app php artisan test

# Run specific test
docker-compose exec app php artisan test tests/Unit/UserTest.php
```

### Database Migrations

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Rollback last migration
docker-compose exec app php artisan migrate:rollback

# Fresh migration with seeding
docker-compose exec app php artisan migrate:fresh --seed
```

## Production Considerations

⚠️ **This Docker setup is for development/demo purposes only.**

For production, consider:

1. Using environment-specific configurations
2. Setting up proper SSL/TLS certificates
3. Using production-ready database configurations
4. Implementing proper backup strategies
5. Setting up monitoring and logging
6. Using Docker secrets for sensitive data
7. Implementing proper security hardening

## Additional Resources

- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [Laravel Documentation](https://laravel.com/docs)
- [PHP-FPM Documentation](https://www.php.net/manual/en/install.fpm.php)

## Support

For issues or questions:
1. Check the logs: `docker-compose logs -f`
2. Verify all containers are running: `docker-compose ps`
3. Review the configuration files in `docker/` directory

