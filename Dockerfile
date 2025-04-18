FROM php:apache

# Installer les extensions nécessaires
RUN docker-php-ext-install mysqli
