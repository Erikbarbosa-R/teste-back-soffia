<?php
// Arquivo simples para testar se o servidor está funcionando
header('Content-Type: application/json');
echo json_encode(['status' => 'ok', 'message' => 'Server is running']);
?>
