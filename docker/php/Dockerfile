FROM php:8.3-fpm

WORKDIR /app

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    librabbitmq-dev \
    libssl-dev \
    && docker-php-ext-install pdo_pgsql mbstring xml sockets opcache \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Install Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Permissions
RUN chown -R www-data:www-data /app

# Expose port for built-in PHP server (dev only)
EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
