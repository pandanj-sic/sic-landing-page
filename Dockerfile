# Stage 1: Build Frontend Assets & Vendor dependencies
FROM node:24-alpine AS builder

# Install PHP, standard extensions, and Composer for Laravel Wayfinder generation
RUN apk add --no-cache \
    curl \
    php \
    php-curl \
    php-openssl \
    php-mbstring \
    php-xml \
    php-dom \
    php-json \
    php-phar \
    php-common \
    php-ctype \
    php-session \
    php-simplexml \
    php-tokenizer \
    php-xmlwriter \
    php-fileinfo

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copy dependency files first
COPY package*.json composer.json composer.lock ./

# Install dependencies
RUN npm install --no-audit --no-fund
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --ignore-platform-reqs

# Copy application files
COPY . .

# Generate autoloader and build assets
RUN composer dump-autoload --no-dev --optimize --no-scripts
RUN npm run build

# Stage 2: Production PHP & Nginx Runtime
FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

# Copy pre-built application files, vendor dependencies, and assets from Stage 1
COPY --from=builder /app /var/www/html

# Setup environment variables for richarvey/nginx-php-fpm
ENV WEBROOT /var/www/html/public
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV SKIP_COMPOSER 1

# Expose port 80
EXPOSE 80
