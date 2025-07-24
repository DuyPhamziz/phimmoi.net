FROM php:8.2-apache

# Cài đặt các extension cần thiết
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Bật mod_rewrite cho Apache
RUN a2enmod rewrite

# Cấu hình thư mục public làm document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Sao chép toàn bộ source code vào container
COPY . /var/www/html/

# Chạy composer install để tạo thư mục vendor/
WORKDIR /var/www/html
RUN composer install --no-interaction --prefer-dist --optimize-autoloader