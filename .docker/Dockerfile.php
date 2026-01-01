FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    supervisor \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip unzip \
    libpq-dev \
    git \
    pkg-config \
    libssl-dev \
    libxml2-dev \
    build-essential \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pcntl zip gd bcmath sockets intl \
    && pecl install -o -f redis-6.2.0 swoole \
    && docker-php-ext-enable redis bcmath swoole \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/pear

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY php/limit.ini /usr/local/etc/php/conf.d/limit.ini

# Supervisor configuration
COPY supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY supervisor/laravel.conf /etc/supervisor/conf.d/laravel.conf

# Set permissions
RUN groupadd --gid 1000 appuser \
    && useradd --uid 1000 -g appuser \
    -G www-data,root --shell /bin/bash \
    --create-home appuser

# Copy initialization script
COPY init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh

# Expose port 9000
EXPOSE 9000

# Use init script as entrypoint
CMD ["/usr/local/bin/init.sh"]
