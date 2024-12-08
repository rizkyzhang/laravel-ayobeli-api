# Stage 1: Build the application
FROM php:8.2-fpm AS build

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    apt-transport-https\
    build-essential \
    ca-certificates \
    curl \
    gnupg \
    unzip \
    zip

# Setup Doppler CLI
RUN curl -sLf --retry 3 --tlsv1.2 --proto "=https" 'https://packages.doppler.com/public/cli/gpg.DE2A7741A397C129.key' | gpg --dearmor -o /usr/share/keyrings/doppler-archive-keyring.gpg
RUN echo "deb [signed-by=/usr/share/keyrings/doppler-archive-keyring.gpg] https://packages.doppler.com/public/cli/deb/debian any-version main" | tee /etc/apt/sources.list.d/doppler-cli.list
RUN apt-get update && apt-get install -y doppler

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY ../.. /var/www

# Install PHP dependencies
RUN composer install --no-dev --no-progress --optimize-autoloader --prefer-dist

## Stage 2: Create the final image
#FROM php:8.2-fpm
#
## Set working directory
#WORKDIR /var/www
#
## Copy Doppler binary from the build stage
#COPY --from=build /usr/bin/doppler /usr/bin/doppler
#
## Copy PHP extensions from the build stage
#COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
#COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d
#
## Copy application files from the build stage
#COPY --from=build /var/www /var/www

# Set working directory
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Change ownership of storage and bootstrap/cache directories
RUN chown -R www:www /var/www/storage /var/www/bootstrap/cache

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD [ "doppler", "run", "--", "php-fpm"]
