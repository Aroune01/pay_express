FROM php:8.2-apache

# Installer l'extension PDO pour PostgreSQL et MySQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Copier les fichiers du projet dans le serveur Apache
COPY . /var/www/html/

# Donner les bonnes permissions
RUN chown -R www-data:www-data /var/www/html/

# Exposer le port par défaut d'Apache
EXPOSE 80