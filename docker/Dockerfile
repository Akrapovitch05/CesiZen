# ---------------------------------------------------------------------------
# Image applicative CESIZen — PHP 8.2 FPM
# Construction multi-étages : une base commune, puis une cible par
# environnement (dev / test / prod). Garantit que l'image testée en CI est
# construite à l'identique de celle livrée en production.
# ---------------------------------------------------------------------------

# --------------------------- Étage 1 : base --------------------------------
FROM php:8.2-fpm-alpine AS base

RUN apk add --no-cache git icu-dev libzip-dev oniguruma-dev bash \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql intl zip opcache \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Les dépendances sont installées avant le code applicatif : tant que
# composer.json/lock ne changent pas, Docker réutilise le cache de couche.
COPY composer.json composer.lock symfony.lock ./

# ---------------------- Étage 2 : dépendances prod -------------------------
FROM base AS vendor-prod
ENV APP_ENV=prod
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

# ------------------- Étage 3 : dépendances dev / test ----------------------
FROM base AS vendor-dev
RUN composer install --no-scripts --no-interaction --prefer-dist

# --------------------------- Étage 4 : dev ---------------------------------
FROM base AS dev
ENV APP_ENV=dev
COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/zz-cesizen.ini
COPY --from=vendor-dev /var/www/html/vendor ./vendor
COPY . .
RUN mkdir -p var/cache var/log && chown -R www-data:www-data var

# --------------------------- Étage 5 : test --------------------------------
# Cible utilisée par la CI : mêmes dépendances que dev, APP_ENV=test.
FROM dev AS test
ENV APP_ENV=test

# --------------------------- Étage 6 : prod --------------------------------
FROM base AS prod
ENV APP_ENV=prod APP_DEBUG=0
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/zz-cesizen.ini
COPY --from=vendor-prod /var/www/html/vendor ./vendor
COPY . .
# Le cache est préchauffé à la construction : aucun temps de compilation
# au premier appel en production.
RUN php bin/console cache:clear --env=prod --no-debug \
    && php bin/console assets:install public --env=prod \
    && mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public
USER www-data

# --------------------------- Étage 7 : web ---------------------------------
# Image nginx embarquant les fichiers statiques issus de l'étage « prod ».
# nginx doit disposer de public/ pour servir les assets et résoudre le
# front controller : sans lui, toute requête aboutit à un 404 de PHP-FPM.
# Construire les deux images depuis le même Dockerfile garantit que les
# assets servis correspondent exactement au code exécuté.
FROM nginx:1.27-alpine AS web
COPY --from=prod /var/www/html/public /var/www/html/public
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
