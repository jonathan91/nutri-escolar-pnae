FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    libpq-dev \
    libzip-dev \
    icu-dev \
    unzip \
    git \
    openssl \
    && docker-php-ext-install pdo pdo_pgsql zip intl opcache \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .

RUN composer run-script post-install-cmd --no-interaction 2>/dev/null || true

RUN mkdir -p config/jwt \
    && openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:nutri_jwt_passphrase 2>/dev/null \
    && openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout -passin pass:nutri_jwt_passphrase 2>/dev/null \
    || true

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var

COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

CMD ["/usr/local/bin/entrypoint.sh"]
