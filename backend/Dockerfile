FROM php:8.2-cli

WORKDIR /var/www

# Install required extensions and Redis
RUN apt-get update && apt-get install -y \
    zip unzip curl git libzip-dev \
    && docker-php-ext-install zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Verify Redis installation
RUN php -m | grep redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy backend files
COPY . .

# Install dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]