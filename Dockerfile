# Stage 1: Build với Composer
FROM composer:2 AS builder

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copy source
COPY . .

# Stage 2: Ảnh Production
FROM php:8.2-apache

# Cài các thư viện hệ thống và extension PHP
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libpq-dev libzip-dev zip unzip git curl \
       libonig-dev libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip mbstring gd xml \
    && rm -rf /var/lib/apt/lists/*

# Bật mod_rewrite cho Apache
RUN a2enmod rewrite

# Đổi DocumentRoot sang thư mục public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri \
    -e 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!g' \
    /etc/apache2/sites-available/000-default.conf

# Copy mã nguồn từ builder
COPY --from=builder /app /var/www/html

# Cấp quyền cho các thư mục cache/storage (nếu framework bạn dùng cần)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 80
CMD ["apache2-foreground"]
