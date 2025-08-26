FROM php:8.2-apache

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip git wget \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite

# Copier vhost Apache
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Installer Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/local/bin/composer

# Installer Sonar Scanner
RUN wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.9.3.6956-linux.zip \
    && unzip sonar-scanner-cli-4.9.3.6956-linux.zip -d /opt \
    && rm sonar-scanner-cli-4.9.3.6956-linux.zip

# Définir SonarScanner dans le PATH
ENV SONAR_SCANNER_HOME=/opt/sonar-scanner-4.9.3.6956-linux
ENV PATH="$SONAR_SCANNER_HOME/bin:$PATH"

# Définir le dossier de travail
WORKDIR /var/www/html

# Créer dossiers cache, logs et autres nécessaires
RUN mkdir -p var/cache var/log var/sessions var/translations \
    && chown -R www-data:www-data var \
    && chmod -R 775 var

# Exécuter PHP en tant que www-data par défaut
USER www-data
