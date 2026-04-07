FROM php:8.2-apache

# 1. Installation des dépendances système + libpq-dev pour PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl

# 2. INSTALLATION DE PDO_PGSQL (Obligatoire pour PostgreSQL sur Render)
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Configuration d'Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Copie du projet
COPY . /var/www/html
WORKDIR /var/www/html

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Droits d'accès (Correction de la petite faute de frappe à la fin)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache