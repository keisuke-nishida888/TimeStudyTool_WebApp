FROM php:7.3-fpm-buster

RUN apt-get update && \
    apt-get install -y \
        libgd-dev \
        libzip-dev \
        zip \
        libpq-dev \
        libonig-dev \
        libxml2-dev \
        unzip \
        && docker-php-ext-install pdo_mysql gd zip

# Allow Composer to run as superuser
ENV COMPOSER_ALLOW_SUPERUSER 1

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents
COPY . /var/www/html

# Install project dependencies
RUN composer install

# Expose port 80
EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
