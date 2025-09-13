#!/bin/bash

# Aguardar o banco de dados estar disponível
echo "Aguardando banco de dados..."
while ! nc -z db 5432; do
  sleep 1
done

echo "Banco de dados disponível!"

# Executar migrações
echo "Executando migrações..."
php artisan migrate --force

# Iniciar PHP-FPM
echo "Iniciando PHP-FPM..."
exec php-fpm
