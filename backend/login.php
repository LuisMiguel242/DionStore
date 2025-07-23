<?php


$error = '';
$email = '';
$password = '';
$login_success = false;


if (isset($_POST['submit'])) {
   
    $servername = "localhost";
    $username = "root";
    $password_db = "";
    $dbname = "dion_store";

    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password_db);
     
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
      
        $email = trim($_POST['email']);
        $password = $_POST['senha'];
        
        
        if (empty($email) || empty($password)) {
            $error = "Por favor, preencha todos os campos!";
        } else {
            
            $stmt = $conn->prepare("SELECT id, nome, email, senha, usuario FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            // Verificando se encontrou algum usuário
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                
                // Verificando se a senha está correta
                if (password_verify($password, $user['senha'])) {
                    // Senha correta, iniciando a sessão
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nome'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['usurname'] = $user['usuario'];
                    
                    // Registrando o login no log de acessos
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $user_agent = $_SERVER['HTTP_USER_AGENT'];
                    $pagina = "login.php";
                    
                    $stmt = $conn->prepare("INSERT INTO logs_acesso (id_usuario, ip, pagina_acessada, user_agent) VALUES (:id_usuario, :ip, :pagina, :user_agent)");
                    $stmt->bindParam(':id_usuario', $user['id']);
                    $stmt->bindParam(':ip', $ip);
                    $stmt->bindParam(':pagina', $pagina);
                    $stmt->bindParam(':user_agent', $user_agent);
                    $stmt->execute();
                    
                    // Definir flag de sucesso em vez de redirecionar
                    $login_success = true;
                    
                } else {
                    $error = "Senha incorreta. Por favor, tente novamente.";
                }
            } else {
                $error = "Email não encontrado. Por favor, verifique seus dados ou cadastre-se.";
            }
        }
    } catch(PDOException $e) {
        $error = "Erro ao conectar com o banco de dados: " . $e->getMessage();
    }
    
    // Fechando a conexão
    $conn = null;
    
}

?>