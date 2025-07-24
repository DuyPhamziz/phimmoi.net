FROM php:8.2-apache

# Cài đặt extension
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Bật mod_rewrite
RUN a2enmod rewrite

# Cấu hình public làm document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Copy source vào container
COPY . /var/www/html/

# Đặt thư mục làm việc
WORKDIR /var/www/html

# Cài dependency
RUN composer install --prefer-dist --optimize-autoloader

# Generate optimized autoload
RUN composer dump-autoload -o
