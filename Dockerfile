# -------- build vendor (composer)
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload -o

# -------- runtime
FROM php:8.2-cli-alpine

RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev oniguruma-dev libzip-dev git; \
    docker-php-ext-install -j"$(nproc)" intl mbstring zip; \
    apk del .build-deps; \
    apk add --no-cache tzdata icu-data-full icu-libs

WORKDIR /app

# copy file
COPY --from=vendor /app /app
RUN chmod -R 755 /app/public

ENV CI_ENVIRONMENT=production
EXPOSE 8080
CMD ["sh","-c","php -d variables_order=EGPCS -S 0.0.0.0:${PORT:-8080} -t public"]
