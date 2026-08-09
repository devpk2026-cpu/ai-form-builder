FROM dunglas/frankenphp:php8.2-bookworm

RUN install-php-extensions \
    gd \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    bcmath \
    intl \
    exif

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --no-scripts

COPY . .

RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

RUN php artisan package:discover --ansi

RUN npm install
RUN npm run build

RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

EXPOSE 10000

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]