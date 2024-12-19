# Stage 1: Builder (writable)
FROM php:8.2-apache AS builder  # Use php:8.2-apache consistently

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql

# Set DNS within the container (important for Composer)
RUN echo "nameserver 8.8.8.8" > /etc/resolv.conf

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Workaround for https://github.com/docker/docker-php-extension-install/issues/160
RUN echo "<?php echo PHP_VERSION; ?>" > /var/www/html/phpinfo.php

# Copy only composer files first to leverage Docker layer caching
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copy the application files to the container AFTER composer install
COPY . .

# Stage 2: Final (using the same base image)
FROM php:8.2-apache # Use php:8.2-apache consistently

# Copy application files from the builder stage
COPY --from=builder /var/www/html /var/www/html

# Create necessary directories (if needed - depends on your app)
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Set proper permissions (if needed - depends on your app)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80