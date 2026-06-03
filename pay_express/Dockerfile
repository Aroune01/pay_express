FROM php:8.2-apache

# Copier tous les fichiers du projet dans le serveur de Render
COPY . /var/www/html/

# Donner les droits d'accès au serveur
RUN chown -R www-data:www-data /var/www/html/

# Exposer le port par défaut
EXPOSE 80