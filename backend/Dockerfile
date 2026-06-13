FROM php:8.3-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libzip-dev libssl-dev \
    libcurl4-openssl-dev libsqlite3-dev libpq-dev \
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

RUN touch .env

EXPOSE 8000

CMD sh -c "\
    echo 'APP_KEY='$APP_KEY > .env && \
    echo 'APP_ENV='$APP_ENV >> .env && \
    echo 'APP_DEBUG='$APP_DEBUG >> .env && \
    echo 'DB_CONNECTION='$DB_CONNECTION >> .env && \
    echo 'DB_HOST='$DB_HOST >> .env && \
    echo 'DB_PORT='$DB_PORT >> .env && \
    echo 'DB_DATABASE='$DB_DATABASE >> .env && \
    echo 'DB_USERNAME='$DB_USERNAME >> .env && \
    echo 'DB_PASSWORD='$DB_PASSWORD >> .env && \
    echo 'TYPESENSE_HOST='$TYPESENSE_HOST >> .env && \
    echo 'TYPESENSE_PORT='$TYPESENSE_PORT >> .env && \
    echo 'TYPESENSE_PROTOCOL='$TYPESENSE_PROTOCOL >> .env && \
    echo 'TYPESENSE_API_KEY='$TYPESENSE_API_KEY >> .env && \
    echo 'TYPESENSE_SEARCH_ONLY_KEY='$TYPESENSE_SEARCH_ONLY_KEY >> .env && \
    echo 'FRONTEND_URL='$FRONTEND_URL >> .env && \
    php artisan config:clear && \
    php artisan migrate:fresh --force && \
    php artisan db:seed --force && \
    php artisan typesense:reindex --fresh && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"