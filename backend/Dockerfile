FROM php:8.3-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libssl-dev \
    libcurl4-openssl-dev libsqlite3-dev \
    && docker-php-ext-install zip pdo pdo_sqlite pdo_mysql pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy full app
COPY . .

# Debug: show what files are here
RUN ls -la

# Install PHP deps
RUN composer install --optimize-autoloader --no-scripts --no-interaction --verbose

# Debug: confirm vendor exists
RUN ls -la vendor/

# Setup
RUN mkdir -p database \
    && touch database/database.sqlite \
    && mkdir -p storage/framework/cache \
                storage/framework/sessions \
                storage/framework/views \
                storage/logs \
                bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache database

RUN cp .env.example .env 2>/dev/null || true

EXPOSE 8000

CMD sh -c "\
    php artisan key:generate --force && \
    php artisan config:clear && \
    php artisan migrate:fresh --force && \
    php artisan db:seed --force && \
    php artisan typesense:reindex --fresh && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"