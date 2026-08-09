FROM php:8.2-cli

# System dependencies + Node.js repository
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

# PHP extensions
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

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

# Copy composer files first for Docker layer caching
COPY composer.json composer.lock ./

RUN composer install \
    --optimize-autoloader \
    --no-interaction \
    --no-dev \
    --no-scripts

# Copy package files
COPY package.json package-lock.json* ./

# Install frontend dependencies
RUN npm install

# Copy complete application
COPY . .

# Build frontend assets
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

# Railway provides PORT
EXPOSE 8080

CMD php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

