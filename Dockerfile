# Multi-stage build for optimized production image
FROM composer:2 AS composer-build

# Install root composer dependencies
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Install plugin composer dependencies
WORKDIR /app/plugins
COPY wp-content/plugins/arborisis-search/composer.json wp-content/plugins/arborisis-search/composer.lock* ./arborisis-search/
RUN cd arborisis-search && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || true

# Node.js build stage for theme
FROM node:18-alpine AS node-build
WORKDIR /app
COPY wp-content/themes/arborisis/package.json ./
RUN npm install
COPY wp-content/themes/arborisis ./
RUN npm run build

# Main image
FROM php:8.2-fpm-alpine AS base

# Install system dependencies in one layer
RUN apk add --no-cache \
    nginx \
    bash \
    curl \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    imagemagick-dev \
    libgomp \
    icu-dev \
    oniguruma-dev \
    mysql-client \
    redis \
    fcgi \
    supervisor \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo_mysql \
        opcache \
        exif \
        intl \
        zip \
        bcmath \
        mbstring \
    && pecl install redis-6.0.2 imagick-3.7.0 \
    && docker-php-ext-enable redis imagick \
    && apk del .build-deps \
    && rm -rf /tmp/* /var/cache/apk/*

# Install WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Production PHP configuration
COPY php.ini /usr/local/etc/php/conf.d/custom.ini
RUN echo "[opcache]" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=60" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Configure Nginx
COPY nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx-site.conf /etc/nginx/http.d/default.conf
COPY docker/nginx-cache.conf /etc/nginx/conf.d/cache.conf

# Configure PHP-FPM for production
RUN echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_children = 50" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.start_servers = 10" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.min_spare_servers = 5" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_spare_servers = 20" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.max_requests = 500" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "pm.status_path = /status" >> /usr/local/etc/php-fpm.d/www.conf \
    && echo "ping.path = /ping" >> /usr/local/etc/php-fpm.d/www.conf

# Set working directory
WORKDIR /var/www/html

# Download and install WordPress core
RUN curl -o wordpress.tar.gz -fSL https://wordpress.org/latest.tar.gz \
    && tar -xzf wordpress.tar.gz --strip-components=1 \
    && rm wordpress.tar.gz \
    && chown -R www-data:www-data /var/www/html

# Copy custom wp-content (plugins and themes) - this will overlay the default wp-content
COPY --chown=www-data:www-data ./wp-content /var/www/html/wp-content

# Copy composer dependencies from build stage
COPY --from=composer-build --chown=www-data:www-data /app/vendor ./vendor
COPY --from=composer-build --chown=www-data:www-data /app/plugins/arborisis-search/vendor ./wp-content/plugins/arborisis-search/vendor

# Copy compiled theme assets
COPY --from=node-build --chown=www-data:www-data /app/dist ./wp-content/themes/arborisis/dist

# Copy composer binary for potential runtime use
COPY --from=composer-build /usr/bin/composer /usr/local/bin/composer

# Create required directories with correct permissions
RUN mkdir -p \
    /var/www/html/wp-content/uploads \
    /var/www/html/wp-content/cache \
    /var/cache/nginx/static \
    /var/cache/nginx/api \
    && chown -R www-data:www-data \
        /var/www/html/wp-content \
        /var/cache/nginx

# Configure Supervisor
COPY docker/supervisord.conf /etc/supervisord.conf

# Add entrypoint script for wp-config.php generation
COPY docker/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Security: Run as non-root where possible
RUN chown -R www-data:www-data /var/lib/nginx /var/log/nginx

# Expose port
EXPOSE 80

# Health check with better parameters
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1

# Set entrypoint to handle wp-config.php
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# Start supervisor (manages nginx + php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
