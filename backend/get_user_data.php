<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Usuário não autenticado']);
  exit;
}

$host = 'localhost';
$db = 'dion_store';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);

  $stmt = $pdo->prepare("SELECT nome, email, telefone, cep, endereco, cidade, estado FROM usuarios WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $userData = $stmt->fetch(PDO::FETCH_ASSOC);

  echo json_encode($userData ?: ['error' => 'Usuário não encontrado']);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Erro de conexão com o banco']);
}
