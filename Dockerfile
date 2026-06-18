# ==========================================
# Stage 1: Build Node.js Assets
# ==========================================
FROM node:20-alpine as node_builder
WORKDIR /app

# Memanfaatkan Docker cache untuk node_modules
COPY package*.json ./
RUN npm install

# Copy seluruh source code dan build aset
COPY . .
RUN npm run build

# ==========================================
# Stage 2: Build PHP Application Environment
# ==========================================
FROM php:8.4-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies dan PHP extensions yang dibutuhkan
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd zip intl opcache

# Install Composer dari image resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Memanfaatkan Docker cache untuk vendor Laravel
# Menyalin composer files terlebih dahulu agar tidak install ulang jika hanya mengubah code/blade
COPY composer*.json ./
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress

# Copy seluruh source code aplikasi
COPY . .

# Pastikan folder public ada sebelum disalin dari stage builder
RUN mkdir -p public/build

# Salin hasil build aset frontend dari Stage 1
COPY --from=node_builder /app/public/build ./public/build

# Jalankan script composer post-autoload-dump jika diperlukan
RUN composer dump-autoload --no-dev --optimize

# Atur permission direktori untuk keamanan Laravel & Web Server
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Expose port 9000 dan jalankan php-fpm
EXPOSE 9000
CMD ["php-fpm"]