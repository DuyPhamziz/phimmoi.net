FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy source code to container
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Set file permissions
RUN chown -R www-data:www-data /var/www/html
