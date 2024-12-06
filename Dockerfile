# Use the official PHP image from Docker Hub
FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory inside the container
WORKDIR /var/www/html

# Copy the local code into the container
COPY . /var/www/html/

# Expose port 80 for the web server
EXPOSE 80

# Start the Apache server when the container starts
CMD ["apache2-foreground"]
