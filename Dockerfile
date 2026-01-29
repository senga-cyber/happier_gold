FROM php:8.2-cli

# Dépendances système + extensions PHP
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libcurl4-openssl-dev \
 && docker-php-ext-install zip curl \
 && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

ENV PORT=10000
EXPOSE 10000

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t ."]
