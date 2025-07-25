# Stage 1: Build with Composer
FROM composer:2 AS builder

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
COPY . .

# Stage 2: Production image
FROM php:8.2-apache

# Install system libraries and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libpq-dev libzip-dev zip unzip git curl \
       libonig-dev libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql zip mbstring gd xml \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules
RUN a2enmod rewrite headers

# Set DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri \
    -e 's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!g' \
    /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides
RUN sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copy application files
COPY --from=builder /app /var/www/html

# Create .htaccess in public if not exists
RUN if [ ! -f /var/www/html/public/.htaccess ]; then \
      echo "<IfModule mod_rewrite.c>" > /var/www/html/public/.htaccess && \
      echo "  RewriteEngine On" >> /var/www/html/public/.htaccess && \
      echo "  RewriteCond %{REQUEST_FILENAME} !-f" >> /var/www/html/public/.htaccess && \
      echo "  RewriteRule ^ index.php [QSA,L]" >> /var/www/html/public/.htaccess && \
      echo "</IfModule>" >> /var/www/html/public/.htaccess; \
    fi

# Fix permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 80
CMD ["apache2-foreground"]
