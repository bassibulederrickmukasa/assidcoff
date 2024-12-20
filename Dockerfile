# Stage 1: Builder (for Composer dependencies)
FROM php:8.2-apache AS builder

# Install system dependencies and PHP extensions (adjust as needed)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql

# Copy Composer from the official image
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files FIRST
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copy the rest of your application code AFTER composer install
COPY . .

# Stage 2: Final image
FROM php:8.2-apache # Use the correct base image

# Copy application files from the builder stage
COPY --from=builder /var/www/html /var/www/html

# Set working directory
WORKDIR /var/www/html

# Set proper permissions (if needed)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Suppress the ServerName warning (optional)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Enable rewrite module (the correct way)
RUN a2enmod rewrite

# Set document root to the public directory (if you have one)
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Restart Apache to apply changes
RUN service apache2 restart

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]