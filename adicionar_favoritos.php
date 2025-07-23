<?php
session_start();
header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$produto_id = $input['produto_id'];
$usuario_id = $_SESSION['usuario_id'];

// Conexão com o banco de dados (ajuste os dados conforme seu ambiente)
$conn = new mysqli('localhost', 'root', '', 'seu_banco');

if ($conn->connect_error) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro de conexão']);
    exit;
}

// Verifica se já está nos favoritos
$sql = "SELECT * FROM favoritos WHERE usuario_id = ? AND produto_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $usuario_id, $produto_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Já está nos favoritos']);
    exit;
}

// Insere nos favoritos
$sql = "INSERT INTO favoritos (usuario_id, produto_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $usuario_id, $produto_id);

if ($stmt->execute()) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao inserir']);
}
?>
