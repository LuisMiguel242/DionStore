<?php
// Incluir arquivo de conexão com o banco de dados
require_once('conexao.php');

// Verificar se o usuário está logado
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Redirecionar para a página de login se não estiver logado
    header("Location: index.html");
    exit();
}

// Obter informações do usuário
$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];
$usuario_email = $_SESSION['usuario_email'];

// Opcionalmente, buscar mais informações do usuário no banco de dados
$sql = "SELECT data_cadastro, ultimo_login FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario_dados = $resultado->fetch_assoc();
$stmt->close();

// Fechar conexão
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Usuário</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 20px;
        }
        .user-panel {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .user-welcome {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="user-panel">
            <div class="user-welcome">
                <h2>Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>!</h2>
                <p>Email: <?php echo htmlspecialchars($usuario_email); ?></p>
                <?php if (isset($usuario_dados)): ?>
                <p>Membro desde: <?php echo date('d/m/Y', strtotime($usuario_dados['data_cadastro'])); ?></p>
                <p>Último login: <?php echo date('d/m/Y H:i', strtotime($usuario_dados['ultimo_login'])); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <h4>Suas Informações</h4>
                    <p>Este é seu painel de usuário. Aqui você pode gerenciar sua conta e ver informações relevantes.</p>
                    
                    <!-- Aqui você pode adicionar mais seções conforme necessário -->
                    <div class="mt-4">
                        <h5>Ações</h5>
                        <a href="editar-perfil.php" class="btn btn-primary mr-2">Editar Perfil</a>
                        <a href="alterar-senha.php" class="btn btn-secondary mr-2">Alterar Senha</a>
                        <a href="logout.php" class="btn btn-danger">Sair</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>