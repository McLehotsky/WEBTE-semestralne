# pdf-app/Dockerfile
FROM php:8.2-fpm

WORKDIR /var/www

# Inštalácie systémových balíkov a PHP rozšírení
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

# Inštalácia závislostí Laravelu
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

CMD ["php-fpm"]
