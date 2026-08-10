FROM php:8.2-cli

# System dependencies + Node.js
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# PHP GD configuration
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

# PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Composer dependencies
COPY composer.json composer.lock ./

RUN composer install \
    --optimize-autoloader \
    --no-interaction \
    --no-dev \
    --no-scripts

# Node dependencies
COPY package.json package-lock.json* ./

RUN npm install

# Copy application
COPY . .

# Build frontend
RUN npm run build

# Laravel directories
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Permissions
RUN chmod -R 775 storage bootstrap/cache

# Laravel package discovery
RUN php artisan package:discover --ansi

EXPOSE 8080

# Run migrations and start Laravel
CMD php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}