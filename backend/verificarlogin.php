<?php

require_once('conexao.php');


session_start();

$logado = isset($_SESSION['logado']) && $_SESSION['logado'] === true;
$usuario = null;

if ($logado) {
    
    $id_usuario = $_SESSION['usuario_id'];
    
    $stmt = $conn->prepare("SELECT id_usuario, nome, email, tipo_usuario FROM usuarios WHERE id_usuario = ? AND ativo = 1");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
    } else {
        
        session_unset();
        session_destroy();
        $logado = false;
    }
    
    $stmt->close();
}

$response = array(
    'logado' => $logado,
    'usuario' => $usuario
);


header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>