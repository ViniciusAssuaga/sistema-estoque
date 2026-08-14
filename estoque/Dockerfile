FROM php:8.2-apache

# Habilita suporte ao MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copia todo o conteúdo de 'estoque' para a pasta raiz do Apache
COPY ./estoque /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80