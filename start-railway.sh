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

# Executar migrations pendentes
echo "Executando migrations pendentes..."
php artisan migrate --force || {
    echo "AVISO: Erro ao executar migrations, tentando executar individualmente..."
    
    # Tentar executar migration de comments especificamente
    echo "Executando migration de comments..."
    php artisan migrate --path=database/migrations/2025_09_14_163402_create_comments_table.php --force || {
        echo "AVISO: Migration de comments já executada ou erro"
    }
}

# Verificar status das migrations
echo "Verificando status das migrations..."
php artisan migrate:status || echo "Não foi possível verificar status das migrations"

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
