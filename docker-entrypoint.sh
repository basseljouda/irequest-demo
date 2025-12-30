#!/bin/sh

set -e

echo "Waiting for database connection..."

# Wait for MySQL to be ready
until php artisan migrate:status > /dev/null 2>&1; do
    echo "Database is unavailable - sleeping"
    sleep 1
done

echo "Database is up - executing command"

exec "$@"

