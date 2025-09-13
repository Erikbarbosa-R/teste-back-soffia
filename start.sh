#!/bin/bash

# Definir porta padrão se não estiver definida
export PORT=${PORT:-8000}

# Executar migrações
php artisan migrate --force

# Limpar todos os caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Recriar caches
php artisan config:cache
php artisan route:cache

# Iniciar servidor
php artisan serve --host=0.0.0.0 --port=$PORT
