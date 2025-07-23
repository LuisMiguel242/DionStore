<?php

if(isset($_POST['submit']))
{
print_r($_POST['nome']);
print_r($_POST['email']);
print_r($_POST['senha']);
}

require_once('conexao.php');

// Inicializar variáveis de resposta
$response = array(
    'status' => false,
    'message' => '',
    'redirect' => ''
);

// Verificar se é uma requisição POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obter e sanitizar os dados do formulário
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha']; // Será hasheada posteriormente
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Validar campos obrigatórios
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmar_senha)) {
        $response['message'] = "Todos os campos são obrigatórios.";
        echo json_encode($response);
        exit;
    }
    
    // Validar formato de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Formato de email inválido.";
        echo json_encode($response);
        exit;
    }
    
    // Verificar se as senhas coincidem
    if ($senha !== $confirmar_senha) {
        $response['message'] = "As senhas não coincidem.";
        echo json_encode($response);
        exit;
    }
    
    // Verificar comprimento mínimo da senha
    if (strlen($senha) < 8) {
        $response['message'] = "A senha deve ter pelo menos 8 caracteres.";
        echo json_encode($response);
        exit;
    }
    
    // Verificar se o email já está cadastrado
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $response['message'] = "Este email já está cadastrado.";
        echo json_encode($response);
        exit;
    }
    
    // Hash da senha
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
    // Inserir usuário no banco de dados
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario, data_cadastro, status) VALUES (?, ?, ?, 'cliente', NOW(), 1)");
    $stmt->bind_param("sss", $nome, $email, $senha_hash);
    
    if ($stmt->execute()) {
        // Cadastro bem-sucedido
        $response['status'] = true;
        $response['message'] = "Cadastro realizado com sucesso! Faça login para continuar.";
        $response['redirect'] = "login.html"; // Redirecionar para a página de login
    } else {
        // Erro ao cadastrar
        $response['message'] = "Erro ao cadastrar: " . $conn->error;
    }
    
    $stmt->close();
}

// Retornar resposta em formato JSON
header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>