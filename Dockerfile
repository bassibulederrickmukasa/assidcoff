# Use the official PHP image
FROM php:8.2-apache

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

# Set the working directory
WORKDIR /var/www/html

# Copy the application files to the container
COPY . .

# Create necessary directories
RUN mkdir -p /var/www/html/config /var/www/html/logs /var/www/html/uploads

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/config \
    && chmod -R 755 /var/www/html/logs \
    && chmod -R 755 /var/www/html/uploads
