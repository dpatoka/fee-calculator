FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    unzip \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    mbstring \
    xml \
    dom \
    intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock* ./

RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --optimize

RUN mkdir -p var/phpstan

RUN git config --global --add safe.directory /app

CMD ["php", "-a"]