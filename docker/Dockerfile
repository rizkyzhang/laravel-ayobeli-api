# Stage 1: Build the application
FROM php:8.2-fpm AS build

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    curl \
    unzip \
    zip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Stage 2: Create the final image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Copy PHP extensions from the build stage
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Copy application files from the build stage
COPY --from=build /var/www /var/www

# Set working directory
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Change ownership of storage and bootstrap/cache directories
RUN chown -R www:www /var/www/storage /var/www/bootstrap/cache

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["doppler", "run", "--", "php-fpm"]
