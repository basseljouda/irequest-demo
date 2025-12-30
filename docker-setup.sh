#!/bin/bash

set -e

echo "🚀 Setting up iRequest Demo with Docker..."

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        echo "⚠️  .env.example not found. Please create .env manually."
    fi
fi

# Update .env with Docker database settings
echo "🔧 Updating .env with Docker database settings..."
sed -i.bak 's/DB_HOST=.*/DB_HOST=db/' .env
sed -i.bak 's/DB_DATABASE=.*/DB_DATABASE=irequest/' .env
sed -i.bak 's/DB_USERNAME=.*/DB_USERNAME=irequest/' .env
sed -i.bak 's/DB_PASSWORD=.*/DB_PASSWORD=root/' .env
sed -i.bak 's/REDIS_HOST=.*/REDIS_HOST=redis/' .env
sed -i.bak 's/REDIS_PORT=.*/REDIS_PORT=6379/' .env

# Remove backup file
rm -f .env.bak

echo "🐳 Building Docker containers..."
docker-compose build

echo "🚀 Starting Docker containers..."
docker-compose up -d

echo "⏳ Waiting for database to be ready..."
sleep 10

echo "📦 Installing Composer dependencies..."
docker-compose exec -T app composer install --no-interaction

echo "🔑 Generating application key..."
docker-compose exec -T app php artisan key:generate --force || true

echo "🗄️  Running database migrations..."
docker-compose exec -T app php artisan migrate --force || true

echo "🔐 Setting permissions..."
docker-compose exec -T app chmod -R 775 storage bootstrap/cache
docker-compose exec -T app chown -R www-data:www-data storage bootstrap/cache

echo "✨ Setup complete!"
echo ""
echo "📱 Access your application at: http://localhost:8080"
echo "🗄️  Access phpMyAdmin at: http://localhost:8081"
echo ""
echo "📚 Useful commands:"
echo "  - View logs: docker-compose logs -f"
echo "  - Stop containers: docker-compose stop"
echo "  - Access shell: docker-compose exec app bash"
echo "  - Run migrations: docker-compose exec app php artisan migrate"
echo ""

