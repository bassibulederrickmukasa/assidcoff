FROM php:8.1-apache # Or your preferred PHP version

# Install system dependencies (if needed)
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy your application code
COPY . .

# Install PHP extensions (if needed)
RUN docker-php-ext-install pdo pdo_mysql

# Install project dependencies
RUN composer install --no-dev --optimize-autoloader

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80