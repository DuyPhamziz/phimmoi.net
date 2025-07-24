# Sử dụng image PHP chính thức với Apache
FROM php:8.2-apache

# Cài đặt các extension cần thiết cho PHP
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip

# Cài Composer (dùng từ container composer chính thức)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Bật mod_rewrite để Apache hỗ trợ route đẹp
RUN a2enmod rewrite

# Cập nhật thư mục public làm document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Sao chép toàn bộ mã nguồn vào image
COPY . /var/www/html/

# Cài đặt Composer packages
WORKDIR /var/www/html
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN composer dump-autoload -o
