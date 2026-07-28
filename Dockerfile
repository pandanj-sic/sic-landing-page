# Stage 1: Build frontend assets
FROM node:22-alpine AS node-builder
RUN apk add --no-cache \
    php \
    php-phar \
    php-mbstring \
    php-openssl \
    php-curl \
    php-tokenizer \
    php-xml \
    php-dom \
    php-pdo \
    php-pdo_sqlite \
    php-sqlite3 \
    php-fileinfo \
    php-session \
    php-xmlwriter \
    php-simplexml \
    php-xmlreader \
    php-iconv \
    php-gd \
    php-bcmath \
    php-zip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --no-scripts --ignore-platform-reqs
COPY package*.json ./
RUN npm ci
COPY . .
RUN composer dump-autoload --optimize
RUN npm run build

# Stage 2: Production PHP runtime
FROM dunglas/frankenphp:1-php8.4-alpine AS runner

# Install system dependencies
RUN apk add --no-cache \
    zip \
    unzip \
    git \
    sqlite-dev \
    sqlite

# Install PHP extensions (FrankenPHP helper script)
RUN install-php-extensions \
    bcmath \
    gd \
    zip \
    opcache \
    pdo_mysql \
    pdo_pgsql \
    pdo_sqlite \
    redis

# Set FrankenPHP server name (needed for Caddy)
ENV SERVER_NAME=":8080"
ENV PORT=8080

# Copy composer binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Copy compiled assets from node-builder
COPY --from=node-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
