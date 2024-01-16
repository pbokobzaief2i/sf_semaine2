# Utiliser l'image de base PHP 8.2
FROM php:8.2

# Installer les dépendances nécessaires pour Symfony
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl

RUN docker-php-ext-install mysqli pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Définir le répertoire de travail
WORKDIR /app

# User principal
RUN groupadd -g 1000 wsl_group && \
    useradd -u 1000 -g wsl_group -m wsl_user

USER wsl_user
