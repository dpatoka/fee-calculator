# ─── BASE ───────────────────────────────────────────
FROM php:8.3-cli AS base

RUN apt-get update && apt-get install -y \
    unzip libonig-dev libxml2-dev libicu-dev git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mbstring xml dom intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock* ./

# ─── DEV ────────────────────────────────────────────
FROM base AS dev

RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY .dev/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN composer install --no-scripts --no-autoloader

RUN git config --global --add safe.directory /app

COPY . .
RUN composer dump-autoload
RUN mkdir -p var/phpstan          # <─ moved here

ENTRYPOINT ["php", "bin/calculate"]
CMD ["1000", "12"]

# ─── PROD ───────────────────────────────────────────
FROM base AS prod

RUN composer install --no-dev --no-scripts --no-autoloader

COPY src/ src/
COPY bin/ bin/

RUN composer dump-autoload --optimize

ENTRYPOINT ["php", "bin/calculate"]
CMD ["1000", "12"]