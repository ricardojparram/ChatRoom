FROM php:8.1-apache

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar configuración de Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Copiar el proyecto al contenedor
COPY . /var/www/html/

WORKDIR /var/www/html

# Instalar dependencias del proyecto (Ratchet, etc.) si no existe vendor/
RUN if [ ! -d "vendor" ]; then composer install --no-dev --optimize-autoloader; fi

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
