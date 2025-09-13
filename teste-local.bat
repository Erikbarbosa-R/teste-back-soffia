@echo off
echo ========================================
echo    TESTANDO API LOCALMENTE
echo ========================================

echo.
echo 1. Construindo container de teste...
docker-compose -f docker-compose.test.yml build

echo.
echo 2. Iniciando aplicacao...
docker-compose -f docker-compose.test.yml up -d

echo.
echo 3. Aguardando aplicacao inicializar...
timeout /t 10 /nobreak

echo.
echo 4. Testando rotas...
echo.
echo Testando /api/ping:
curl -s http://localhost:8000/api/ping
echo.
echo.
echo Testando /api/health:
curl -s http://localhost:8000/api/health
echo.
echo.

echo 5. Para parar o teste:
echo docker-compose -f docker-compose.test.yml down
echo.
echo ========================================
echo    TESTE CONCLUIDO
echo ========================================
pause
