FROM php:8.2-apache

# 1. Installation des dépendances système et des extensions PHP requises (y compris PostgreSQL)
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# 2. Activation du module de réécriture d'Apache (indispensable pour les routes Laravel)
RUN a2enmod headers rewrite

# 3. Configuration du dossier public comme racine du serveur Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 4. Copie de tout le code du projet dans le conteneur
WORKDIR /var/www/html
COPY . .

# 5. Installation de Composer et des dépendances PHP du projet
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# 6. Attribution des bonnes permissions pour les dossiers de cache et de stockage de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Exécuter les migrations automatiquement avant de lancer le serveur
CMD php artisan config:clear && php artisan route:clear && php artisan migrate --force && apache2-foreground

EXPOSE 80