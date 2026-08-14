FROM php:8.3-apache

# 1. Instala pacotes do sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl

# 2. Instala extensões PHP essenciais do Laravel
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring bcmath gd

# 3. Ativa o mod_rewrite do Apache
RUN a2enmod rewrite

# 4. Aponta o Apache para a pasta public do Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# 5. Copia os arquivos para o container
COPY . /var/www/html/

# 6. Instala o Composer e as dependências (com flag de bypass de versão)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 7. Permissões de escrita do Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
