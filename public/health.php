<?php
// Arquivo de teste para Railway health check
http_response_code(200);
header('Content-Type: text/plain');
echo 'OK - Server is running';
exit;
?>
