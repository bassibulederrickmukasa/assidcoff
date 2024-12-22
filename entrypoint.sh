#!/bin/bash

# Set proper permissions (only if the directories exist)
if [ -d "/var/www/html/storage" ]; then
    chown -R www-data:www-data /var/www/html/storage
fi
if [ -d "/var/www/html/bootstrap/cache" ]; then
    chown -R www-data:www-data /var/www/html/bootstrap/cache
fi

exec apache2-foreground