FROM php:8.2-apache

# Cài các extension cần thiết (thêm nếu bạn cần)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql


# Bật mod_rewrite cho Apache (quan trọng cho MVC)
RUN a2enmod rewrite

# Copy code vào container
COPY . /var/www/html/

# Chỉ định thư mục public là document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Sửa lại Apache config để dùng public/ làm root
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Cài composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Cài các thư viện PHP từ composer
# RUN composer install --no-interaction --optimize-autoloader


