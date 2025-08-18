FROM php:8.2-apache

# Installer dépendances
RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip git \
    && docker-php-ext-install pdo pdo_pgsql

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier le fichier vhost
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html
