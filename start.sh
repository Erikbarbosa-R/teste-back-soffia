#!/bin/bash

# Definir porta padrão se não estiver definida
export PORT=${PORT:-8000}

# Executar migrações
php artisan migrate --force

# Limpar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Iniciar servidor
php artisan serve --host=0.0.0.0 --port=$PORT
