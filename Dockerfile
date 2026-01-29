FROM php:8.2-cli

# Dépendances nécessaires
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libcurl4-openssl-dev \
  && docker-php-ext-install zip curl \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copier d’abord composer pour cache
COPY composer.json composer.lock ./

# Installer composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

# Copier tout le projet
COPY . .

# Render fournit PORT (obligatoire)
ENV PORT=10000
EXPOSE 10000

# Démarrage: serveur PHP intégré
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT -t ."]
