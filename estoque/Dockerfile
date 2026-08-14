FROM php:8.2-apache

# Copia os arquivos da pasta estoque para o servidor web do Apache
COPY . /var/www/html/

# Habilita suporte a Banco de Dados MySQL no PHP
RUN docker-php-ext-install pdo pdo_mysql

# Dá permissão para o Apache ler os arquivos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80