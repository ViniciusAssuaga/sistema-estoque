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

# 5. Instala o Composer (Copiando apenas os arquivos de dependência primeiro para aproveitar o cache)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# 6. Instala dependências do Frontend (Aproveita o cache do Node)
COPY package.json package-lock.json* ./
RUN npm install

# 7. Copia o restante do código da aplicação
COPY . .

# 8. Executa scripts pós-instalação do Composer e compila o Vite
RUN composer dump-autoload --no-dev --optimize
RUN npm run build

# 9. Configura permissões corretas para o usuário do Apache (www-data)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public

EXPOSE 80

# 10. Executa as migrations e inicia o Apache
CMD php artisan migrate --force --no-interaction && apache2-foreground
