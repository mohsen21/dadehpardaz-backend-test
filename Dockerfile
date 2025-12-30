FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-client \
    libpq-dev \
    wget \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install MinIO Client (mc) - supports both amd64 and arm64
RUN ARCH=$(uname -m) && \
    if [ "$ARCH" = "aarch64" ] || [ "$ARCH" = "arm64" ]; then \
        wget https://dl.min.io/client/mc/release/linux-arm64/mc -O /usr/local/bin/mc; \
    else \
        wget https://dl.min.io/client/mc/release/linux-amd64/mc -O /usr/local/bin/mc; \
    fi && \
    chmod +x /usr/local/bin/mc

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Install PHP dependencies (will be overridden in development with volume)
RUN composer install --no-dev --optimize-autoloader --no-interaction || true

# Expose port 9000 for PHP-FPM
EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]

