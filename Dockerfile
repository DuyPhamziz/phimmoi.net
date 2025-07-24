FROM php:8.2-apache

# Cài đặt thư viện hệ thống
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpq-dev \
    libonig-dev libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip mbstring

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Bật mod_rewrite cho Apache
RUN a2enmod rewrite

# Đổi document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Copy toàn bộ mã nguồn
COPY . /var/www/html

# Làm việc trong thư mục app
WORKDIR /var/www/html

# Cài đặt PHP packages
RUN composer install --prefer-dist --optimize-autoloader || cat /tmp/composer.log || true
RUN composer dump-autoload -o
