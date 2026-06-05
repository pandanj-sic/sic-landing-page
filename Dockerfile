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
    php-fileinfo \
    php-pdo \
    php-pdo_sqlite \
    php-sqlite3

# Set dummy environment variables so Laravel can boot in-memory during build
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=:memory:
ENV APP_KEY=base64:yhVqVwGjZ4Jz+Hwqn7mK1Jp8uD/29xJpG6tB7+c1R1I=

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

# Remove development files to shrink the image
RUN rm -rf node_modules


# Stage 2: Production PHP & Nginx Runtime (PHP 8.4)
FROM serversideup/php:8.4-fpm-nginx

WORKDIR /var/www/html

# Copy pre-built application files, vendor dependencies, and assets from Stage 1
COPY --from=builder --chown=www-data:www-data /app /var/www/html

# Copy the entrypoint script into ServerSideUp's automatic startup hooks directory
COPY --from=builder --chown=www-data:www-data /app/scripts/run.sh /opt/docker/provision/entrypoint.d/run.sh

# Ensure the provision script is executable
RUN chmod +x /opt/docker/provision/entrypoint.d/run.sh

# Set production environment variables
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Expose port 8080 (serversideup default port)
EXPOSE 8080
