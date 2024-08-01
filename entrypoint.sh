#!/bin/bash

# Chạy lệnh Composer install nếu cần
composer install --no-interaction --prefer-dist --optimize-autoloader

# Chạy lệnh Artisan key:generate nếu .env không có APP_KEY
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Tiếp tục với lệnh CMD mặc định của Dockerfile
exec "$@"