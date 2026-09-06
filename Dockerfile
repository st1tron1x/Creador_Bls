FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
        git curl libpq-dev libzip-dev zip unzip libpng-dev ca-certificates gnupg chromium \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Desactivar OPcache en desarrollo: con el bind-mount de Windows/WSL2, la fecha de
# modificación de los archivos no siempre se actualiza como OPcache espera, y termina
# sirviendo versiones viejas compiladas del código PHP incluso después de editarlo.
RUN echo "opcache.enable=0" > /usr/local/etc/php/conf.d/opcache-dev.ini \
    && echo "opcache.enable_cli=0" >> /usr/local/etc/php/conf.d/opcache-dev.ini

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev || true

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]