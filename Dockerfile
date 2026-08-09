FROM dunglas/frankenphp:php8.2-bookworm

RUN install-php-extensions \
    gd \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    bcmath \
    intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

COPY package.json package-lock.json ./

RUN npm install

COPY . .

RUN npm run build

RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/logs \
    bootstrap/cache

RUN chmod -R 775 storage bootstrap/cache

RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache