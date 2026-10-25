
# FrankenPHP with PHP 8.4, Debian Bookworm
FROM dunglas/frankenphp:1-php8.4-bookworm

WORKDIR /app

# Install PHP extensions required by Laravel and PostgreSQL
RUN install-php-extensions \
    pdo_pgsql \
    bcmath \
    zip \
    intl \
    mbstring

# Install Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Copy dependency manifests first for Docker layer caching
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --no-autoloader

# Copy Laravel application
COPY . .

# Optimize Composer autoload and prepare Laravel directories
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# FrankenPHP / Caddy configuration
COPY Caddyfile /etc/caddy/Caddyfile

EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
