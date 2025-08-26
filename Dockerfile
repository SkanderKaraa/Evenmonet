    FROM php:8.2-apache

    # Installer dépendances système
    RUN apt-get update && apt-get install -y \
        libpq-dev zip unzip git \
        && docker-php-ext-install pdo pdo_pgsql \
        && a2enmod rewrite

    # Copier vhost Apache
    COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

    # Installer Composer
    COPY --from=composer:2.7 /usr/bin/composer /usr/local/bin/composer

    # Définir le dossier de travail
    WORKDIR /var/www/html

    # Ajuster les permissions
    RUN chown -R www-data:www-data /var/www/html
