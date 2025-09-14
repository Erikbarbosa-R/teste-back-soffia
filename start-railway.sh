#!/bin/bash
# Script de inicialização otimizado para Railway

set -e

echo "=== Iniciando CMS API no Railway ==="
echo "Timestamp: $(date)"
echo "Porta definida pelo Railway: $PORT"

# Verificar se a porta está definida
if [ -z "$PORT" ]; then
    echo "ERRO: Variável PORT não está definida!"
    exit 1
fi

# Aguardar banco de dados estar disponível
echo "Aguardando banco de dados estar disponível..."
sleep 10

# Executar migrations (com tratamento de erro)
echo "Executando migrations..."
php artisan migrate --force || {
    echo "AVISO: Erro ao executar migrations, continuando..."
}

# Executar seeders (com tratamento de erro)
echo "Executando seeders..."
php artisan db:seed --class=CmsSeeder --force || {
    echo "AVISO: Erro ao executar seeders, continuando..."
}

# Limpar cache
echo "Limpando cache..."
php artisan config:clear || echo "Cache já limpo"
php artisan cache:clear || echo "Cache já limpo"
php artisan route:clear || echo "Cache de rotas já limpo"

# Iniciar servidor
echo "=== Iniciando servidor PHP na porta $PORT ==="
echo "URL de acesso: http://0.0.0.0:$PORT"
echo "Health check: http://0.0.0.0:$PORT/"
echo "=========================================="

exec php -S 0.0.0.0:$PORT -t public
