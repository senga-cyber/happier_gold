FROM php:8.2-cli

# Installer extensions utiles + git + zip (composer en a besoin)
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
 && docker-php-ext-install zip \
 && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier le code
COPY . .

# Installer dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Render écoute sur $PORT (souvent 10000)
ENV PORT=10000
EXPOSE 10000

# Lancer le serveur PHP intégré
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t ."]
