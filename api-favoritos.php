<?php
header("Content-Type: application/json");

// Configurações do banco de dados
$host = 'localhost';
$db   = 'dion_store';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Conexão PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro na conexão com o banco']);
    exit;
}

$action = $_GET['action'] ?? '';

// -------------------------
// Teste rápido da API
// -------------------------
if ($action === 'test') {
    echo json_encode(['success' => true, 'message' => 'API funcionando']);
    exit;
}

// -------------------------
// Adicionar aos favoritos
// -------------------------
if ($action === 'adicionar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['usuario_id'], $input['produto_id'])) {
        echo json_encode(['success' => false, 'error' => 'Dados incompletos']);
        exit;
    }

    $usuario_id = intval($input['usuario_id']);
    $produto_id = intval($input['produto_id']);

    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO lista_desejos (id_usuario, id_produto) VALUES (?, ?)");
        $stmt->execute([$usuario_id, $produto_id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// -------------------------
// Remover dos favoritos
// -------------------------
if ($action === 'remover' && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $usuario_id = $_GET['usuario_id'] ?? null;
    $produto_id = $_GET['produto_id'] ?? null;

    if (!$usuario_id || !$produto_id) {
        echo json_encode(['success' => false, 'error' => 'Parâmetros ausentes']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM lista_desejos WHERE id_usuario = ? AND id_produto = ?");
        $stmt->execute([$usuario_id, $produto_id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// -------------------------
// Listar favoritos do usuário
// -------------------------
if ($action === 'listar' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $usuario_id = $_GET['usuario_id'] ?? null;

    if (!$usuario_id) {
        echo json_encode(['success' => false, 'error' => 'ID do usuário não informado']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT p.* FROM lista_desejos ld
            INNER JOIN produtos p ON ld.id_produto = p.id_produto
            WHERE ld.id_usuario = ?
        ");
        $stmt->execute([$usuario_id]);
        $favoritos = $stmt->fetchAll();

        echo json_encode(['success' => true, 'data' => ['favoritos' => $favoritos]]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// -------------------------
// Caso nenhuma ação seja válida
// -------------------------
http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Ação inválida ou método não permitido']);
exit;
