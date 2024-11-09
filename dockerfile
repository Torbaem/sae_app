FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    zip \
    unzip \
    git \
    curl

# Instala Composer
COPY --from=composer:2.1 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan key:generate

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage

COPY ./docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD service php8.2-fpm start && nginx -g 'daemon off;'
