FROM php:8.1-fpm

WORKDIR /var/www

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar arquivos
COPY . /var/www

# Copiar script de entrada
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Definir permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Instalar dependências
RUN composer install --no-dev --optimize-autoloader

# Criar arquivo .env se não existir
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Gerar chaves
RUN php artisan key:generate --force
RUN php artisan jwt:secret --force

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
