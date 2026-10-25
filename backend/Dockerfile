
FROM php:8.4-cli-bookworm

WORKDIR /var/www/html

# Install system packages and PHP extensions for Laravel + PostgreSQL
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-install \
        pdo_pgsql \
        bcmath \
        zip \
        intl \
        mbstring \
    && rm -rf /var/lib/apt/lists/*

# Install Composer from an image with an explicit version
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Copy dependency manifests first to take advantage of layer caching
COPY composer.json composer.lock ./

# Install dependencies before copying application source code
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --no-autoloader

# Copy Laravel application
COPY . .

# Generate optimized Composer autoload files
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
