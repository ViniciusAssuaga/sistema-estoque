FROM php:8.3-apache

# 1. Instala dependências do sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Instala extensões PHP essenciais do Laravel
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring bcmath gd

# 3. Ativa o mod_rewrite do Apache
RUN a2enmod rewrite

# 4. Aponta o Apache para a pasta public do Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Define o diretório de trabalho
WORKDIR /var/www/html

# 5. Instala o Composer na imagem
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Copia todo o código da aplicação (incluindo as pastas vendor e public/build locais)
COPY . .

# 7. Regera o autoloader do Composer de forma otimizada
RUN composer dump-autoload --optimize

# 8. Configura permissões corretas para o usuário do Apache (www-data)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

EXPOSE 80

# 9. Executa as migrations e inicia o Apache
#CMD php artisan migrate --force --no-interaction && apache2-foreground
CMD ["apache2-foreground"]
