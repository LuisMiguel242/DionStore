<?php


$host = 'localhost';
$dbname = 'dion_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    date_default_timezone_set('America/Sao_Paulo');
} catch (PDOException $e) {
    // Em caso de erro, interrompe o script e mostra mensagem (pode ser ajustado para log)
    http_response_code(500);
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao conectar com o banco de dados: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
