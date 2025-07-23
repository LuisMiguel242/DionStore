<?php
var_dump($_SESSION);
session_start();
header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Usuário não autenticado'
    ]);
    exit;
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
    exit;
}

// Obter dados JSON da requisição
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Dados inválidos'
    ]);
    exit;
}

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'dion_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro de conexão com o banco de dados'
    ]);
    exit;
}

// Validar e sanitizar dados
$user_id = $_SESSION['user_id'];
$nome = trim($input['nome'] ?? '');
$email = trim($input['email'] ?? '');
$telefone = trim($input['telefone'] ?? '');
$nascimento = trim($input['nascimento'] ?? '');
$endereco = trim($input['endereco'] ?? '');
$cidade = trim($input['cidade'] ?? '');
$estado = trim($input['estado'] ?? '');
$cep = trim($input['cep'] ?? '');

// Validações básicas
$errors = [];

if (empty($nome)) {
    $errors[] = 'Nome é obrigatório';
}

if (empty($email)) {
    $errors[] = 'Email é obrigatório';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email inválido';
}

// Verificar se o email já existe para outro usuário
if (!empty($email)) {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        $errors[] = 'Este email já está sendo usado por outro usuário';
    }
}

// Validar data de nascimento se fornecida
if (!empty($nascimento)) {
    $date = DateTime::createFromFormat('Y-m-d', $nascimento);
    if (!$date || $date->format('Y-m-d') !== $nascimento) {
        $errors[] = 'Data de nascimento inválida';
    }
}

// Validar CEP se fornecido
if (!empty($cep)) {
    $cep_clean = preg_replace('/[^0-9]/', '', $cep);
    if (strlen($cep_clean) !== 8) {
        $errors[] = 'CEP deve ter 8 dígitos';
    }
}

// Validar estado se fornecido
if (!empty($estado) && strlen($estado) !== 2) {
    $errors[] = 'Estado deve ter 2 caracteres';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Dados inválidos',
        'errors' => $errors
    ]);
    exit;
}

try {
    // Atualizar dados do usuário
    $sql = "UPDATE usuarios SET 
            nome = ?, 
            email = ?, 
            telefone = ?, 
            cep = ?, 
            endereco = ?, 
            cidade = ?, 
            estado = ?";
    
    $params = [$nome, $email, $telefone, $cep, $endereco, $cidade, $estado];
    
    // Adicionar data de nascimento se fornecida
    if (!empty($nascimento)) {
        $sql .= ", nascimento = ?";
        $params[] = $nascimento;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $user_id;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() > 0) {
        // Buscar dados atualizados do usuário
        $stmt = $pdo->prepare("SELECT id, nome, email, telefone, cep, endereco, cidade, estado, nascimento, usuario FROM usuarios WHERE id = ?");
        $stmt->execute([$user_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user_data) {
            // Atualizar dados da sessão
            $_SESSION['user_data'] = $user_data;
            
            echo json_encode([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso!',
                'user_data' => $user_data
            ]);
        } else {
            throw new Exception('Erro ao recuperar dados atualizados');
        }
    } else {
        echo json_encode([
            'success' => true,
            'message' => 'Nenhuma alteração foi feita',
            'user_data' => $_SESSION['user_data']
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Erro ao atualizar perfil: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro interno do servidor'
    ]);
} catch (Exception $e) {
    error_log("Erro ao processar atualização: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar solicitação'
    ]);
}
?>