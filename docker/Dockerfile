# Imagem base do PHP 8.2 com FPM (FastCGI Process Manager)
FROM php:8.2-fpm

# Define o diretório de trabalho dentro do container
WORKDIR /var/www/html

# Instala dependências do sistema necessárias para as extensões do PHP
RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    libzip-dev \
    unzip \
    zip \
    git \
    curl

# Limpa o cache do apt para manter a imagem menor
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala as extensões do PHP necessárias para o Laravel e PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql zip bcmath

# Instala o Composer (gerenciador de dependências do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia os arquivos da aplicação para o diretório de trabalho
# Embora usemos volumes, isso é útil para o build inicial da imagem
COPY . /var/www/html

# Define as permissões corretas para os diretórios do Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expõe a porta 9000 para que o Nginx possa se comunicar com o PHP-FPM
EXPOSE 9000

# Comando para iniciar o PHP-FPM quando o container for executado
CMD ["php-fpm"]