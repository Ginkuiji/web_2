FROM php:8.2-fpm

# RUN apt-get update && apt-get install -y unzip git

# RUN docker-php-ext-install pdo pdo_mysql

# RUN a2enmod rewrite
# RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libonig-dev libzip-dev zip \
    libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libicu-dev g++ zlib1g-dev libcurl4-openssl-dev \
    libssl-dev libjpeg62-turbo-dev libwebp-dev libxpm-dev \
    libgd-dev libmcrypt-dev 
    # && docker-php-ext-install pdo pdo_mysql intl zip opcache gd

# RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install

EXPOSE 80

# CMD ["apache2-foreground"]
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html