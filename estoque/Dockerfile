FROM php:8.2-apache

# 1. Instala dependências do sistema e extensões necessárias do PHP para o Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo pdo_mysql mbstring exts-mbstring bcmath gd

# 2. Ativa o mod_rewrite do Apache (essencial para as rotas do Laravel funcionarem)
RUN a2enmod rewrite

# 3. APONTA O APACHE PARA A PASTA PUBLIC DO LARAVEL
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# 4. Copia os arquivos do Laravel (pasta estoque) para o container
COPY ./estoque /var/www/html/

# 5. Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 6. Dá permissão de escrita para as pastas de cache/logs do Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80