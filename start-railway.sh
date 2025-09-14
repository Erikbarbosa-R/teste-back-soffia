#!/bin/bash
# Script de inicialização otimizado para Railway

set -e

echo "=== Iniciando CMS API no Railway ==="
echo "Timestamp: $(date)"
echo "Porta definida pelo Railway: $PORT"
echo "Variáveis de ambiente importantes:"
echo "  APP_ENV: $APP_ENV"
echo "  DB_HOST: $DB_HOST"
echo "  DB_PORT: $DB_PORT"
echo "  DB_DATABASE: $DB_DATABASE"
echo "  DB_USERNAME: $DB_USERNAME"

# Verificar se a porta está definida
if [ -z "$PORT" ]; then
    echo "ERRO: Variável PORT não está definida!"
    exit 1
fi

# Aguardar banco de dados estar disponível
echo "Aguardando banco de dados estar disponível..."
sleep 15

# Testar conexão com banco de dados
echo "Testando conexão com banco de dados..."
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Conexão com banco OK\n';
} catch (Exception \$e) {
    echo 'Erro na conexão: ' . \$e->getMessage() . '\n';
    exit(1);
}
" || {
    echo "ERRO: Não foi possível conectar ao banco de dados"
    echo "Verifique as variáveis DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD"
    exit 1
}

# Executar migrations
echo "Executando migrations..."
php artisan migrate --force || {
    echo "AVISO: Erro ao executar migrations, continuando..."
}

# Executar seeders
echo "Executando seeders..."
php artisan db:seed --class=CmsSeeder --force || {
    echo "AVISO: Erro ao executar seeders, continuando..."
}

# Limpar cache
echo "Limpando cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Iniciar servidor
echo "=== Iniciando servidor PHP na porta $PORT ==="
echo "URL de acesso: http://0.0.0.0:$PORT"
echo "Health check: http://0.0.0.0:$PORT/"
echo "=========================================="

exec php -S 0.0.0.0:$PORT -t public
