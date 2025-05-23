# Stage 1: Build Stage
FROM composer:2 as build

WORKDIR /app

COPY . .
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Stage 2: Runtime Stage
FROM php:8.2-fpm-alpine

# Install system dependencies required for PostgreSQL, extensions and Composer
RUN apk update && apk add --no-cache \
    libpq \
    bash \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    zlib-dev \
    libxml2-dev \
    postgresql-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_pgsql mysqli

WORKDIR /var/www

COPY --from=build /app /var/www

RUN chown -R www-data:www-data /var/www && php artisan storage:link && php artisan l5-swagger:generate

EXPOSE 8000
USER www-data:www-data

CMD php artisan serve --host=0.0.0.0 --port=8000

