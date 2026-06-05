# Stage 1: Build Svelte / Vite Frontend Assets
FROM node:24-alpine AS assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Production PHP & Nginx Runtime
FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

# Install Composer dependencies first to leverage Docker build cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction

# Copy application files (ignoring those in .dockerignore)
COPY . .

# Copy compiled assets from Stage 1
COPY --from=assets-builder /app/public/build /var/www/html/public/build

# Generate final optimized autoloader
RUN composer dump-autoload --no-dev --optimize

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
