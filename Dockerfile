FROM node:24-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM dunglas/frankenphp:1-php8.4-alpine

WORKDIR /app

RUN install-php-extensions \
    bcmath mbstring intl zip gd exif \
    pdo_mysql pdo_pgsql pdo_sqlite opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --prefer-dist \
    --no-interaction --optimize-autoloader

COPY --from=frontend /app/public/build ./public/build
COPY Caddyfile /etc/caddy/Caddyfile

RUN mkdir -p storage/framework/sessions \
    storage/framework/views storage/framework/cache \
    storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

ENV PORT=80

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]