<?php

header('Content-Type: application/json; charset=utf-8');
require_once('conexao.php');

ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Função para retornar resposta JSON e terminar execução
function retornarJSON($dados) {
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

// Função para retornar erro
function retornarErro($mensagem, $codigo = 400) {
    retornarJSON([
        'sucesso' => false,
        'mensagem' => $mensagem,
        'codigo' => $codigo
    ]);
}

// Iniciar sessão
session_start();

// ==========================================
// CONFIGURAÇÃO DO BANCO DE DADOS
// ==========================================

$host = 'localhost';
$dbname = 'dion_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // TESTE: Verificar se o PHP está acessando a tabela produtos corretamente
try {
    $stmt = $pdo->query("SELECT * FROM produtos");
    $produtos = $stmt->fetchAll();
    error_log('Produtos teste: ' . print_r($produtos, true));
} catch (PDOException $e) {
    error_log('Erro ao acessar produtos: ' . $e->getMessage());
}

// TESTE: Verificar se o PHP está acessando a tabela favoritos corretamente
try {
    $stmt = $pdo->query("SELECT * FROM favoritos");
    $favoritos = $stmt->fetchAll();
    error_log('Favoritos teste: ' . print_r($favoritos, true));
} catch (PDOException $e) {
    error_log('Erro ao acessar favoritos: ' . $e->getMessage());
}

// TESTE: Verificar JOIN entre favoritos e produtos para usuario_id 12
try {
    $stmt = $pdo->query("SELECT * FROM favoritos f INNER JOIN produtos p ON p.id_produto = f.produto_id WHERE f.usuario_id = 12");
    $favJoin = $stmt->fetchAll();
    error_log('Favoritos JOIN teste: ' . print_r($favJoin, true));
} catch (PDOException $e) {
    error_log('Erro ao acessar favoritos JOIN: ' . $e->getMessage());
}

} catch(PDOException $e) {
    error_log("Erro de conexão PDO: " . $e->getMessage());
    retornarErro("Erro interno do servidor. Tente novamente mais tarde.", 500);
}

// ==========================================
// VERIFICAÇÃO DE AUTENTICAÇÃO
// ==========================================

function verificarUsuarioLogado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

// Verificar se o usuário está logado
if (!verificarUsuarioLogado()) {
    retornarErro("Usuário não autenticado. Faça login para continuar.", 401);
}

$usuario_id = $_SESSION['usuario_id'];

// ==========================================
// VERIFICAR MÉTODO DE REQUISIÇÃO
// ==========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    retornarErro("Método não permitido. Use POST.", 405);
}

// ==========================================
// PROCESSAR DADOS JSON
// ==========================================

$input = file_get_contents('php://input');
$dados = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    retornarErro("Dados JSON inválidos: " . json_last_error_msg(), 400);
}

// ==========================================
// FUNÇÕES DO SISTEMA DE FAVORITOS
// ==========================================

/**
 * Adiciona um produto aos favoritos do usuário
 */
function adicionarFavorito($pdo, $usuario_id, $produto_id) {
    try {
        $pdo->beginTransaction();
        
        // Verificar se o produto existe e está ativo
        $stmt = $pdo->prepare("SELECT id_produto FROM produtos WHERE id_produto = :produto_id AND ativo = 1");
        $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() == 0) {
            $pdo->rollBack();
            return ['sucesso' => false, 'mensagem' => 'Produto não encontrado ou não está disponível.'];
        }
        
        // Verificar se já não está nos favoritos
        $stmt = $pdo->prepare("SELECT id FROM favoritos WHERE usuario_id = :usuario_id AND produto_id = :produto_id");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $pdo->rollBack();
            return ['sucesso' => false, 'mensagem' => 'Produto já está nos favoritos.'];
        }
        
        // Adicionar aos favoritos
        $stmt = $pdo->prepare("INSERT INTO favoritos (usuario_id, produto_id, data_adicao) VALUES (:usuario_id, :produto_id, NOW())");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $pdo->commit();
            return ['sucesso' => true, 'mensagem' => 'Produto adicionado aos favoritos com sucesso!'];
        } else {
            $pdo->rollBack();
            return ['sucesso' => false, 'mensagem' => 'Erro ao adicionar produto aos favoritos.'];
        }
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Erro ao adicionar favorito: " . $e->getMessage());
        return ['sucesso' => false, 'mensagem' => 'Erro interno. Tente novamente mais tarde.'];
    }
}

/**
 * Remove um produto dos favoritos do usuário
 */
function removerFavorito($pdo, $usuario_id, $produto_id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM favoritos WHERE usuario_id = :usuario_id AND produto_id = :produto_id");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return ['sucesso' => true, 'mensagem' => 'Produto removido dos favoritos com sucesso!'];
            } else {
                return ['sucesso' => false, 'mensagem' => 'Produto não encontrado nos favoritos.'];
            }
        } else {
            return ['sucesso' => false, 'mensagem' => 'Erro ao remover produto dos favoritos.'];
        }
        
    } catch (PDOException $e) {
        error_log("Erro ao remover favorito: " . $e->getMessage());
        return ['sucesso' => false, 'mensagem' => 'Erro interno. Tente novamente mais tarde.'];
    }
}

/**
 * Verifica se um produto está nos favoritos do usuário
 */
function verificarFavorito($pdo, $usuario_id, $produto_id) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM favoritos WHERE usuario_id = :usuario_id AND produto_id = :produto_id");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        error_log("Erro ao verificar favorito: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtém todos os favoritos de um usuário
 */
function obterFavoritos($pdo, $usuario_id, $limite = null, $offset = 0) {
    try {
        $sql = "
            SELECT 
                p.id_produto as id,
                p.nome,
                p.descricao,
                p.preco,
                c.id_categoria as categoria_id,
                c.nome as categoria,
                f.data_adicao
            FROM produtos p 
            INNER JOIN favoritos f ON p.id_produto = f.produto_id
            LEFT JOIN categorias c ON p.categoria = c.id_categoria
            WHERE f.usuario_id = $usuario_id
            ORDER BY f.data_adicao DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetchAll();
        error_log('Favoritos retornados (debug sem bind): ' . print_r($resultado, true));
        return $resultado;

    } catch (PDOException $e) {
        error_log("Erro ao obter favoritos: " . $e->getMessage());
        return [];
    }
}

/**
 * Conta o total de favoritos de um usuário
 */
function contarFavoritos($pdo, $usuario_id) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM favoritos WHERE usuario_id = :usuario_id");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return (int)$stmt->fetchColumn();
        
    } catch (PDOException $e) {
        error_log("Erro ao contar favoritos: " . $e->getMessage());
        return 0;
    }
}

/**
 * Alterna o status de favorito de um produto
 */
function alternarFavorito($pdo, $usuario_id, $produto_id) {
    if (verificarFavorito($pdo, $usuario_id, $produto_id)) {
        $resultado = removerFavorito($pdo, $usuario_id, $produto_id);
        $resultado['acao'] = 'removido';
        return $resultado;
    } else {
        $resultado = adicionarFavorito($pdo, $usuario_id, $produto_id);
        $resultado['acao'] = 'adicionado';
        return $resultado;
    }
}

/**
 * Limpa todos os favoritos de um usuário
 */
function limparFavoritos($pdo, $usuario_id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM favoritos WHERE usuario_id = :usuario_id");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $totalRemovidos = $stmt->rowCount();
            return [
                'sucesso' => true, 
                'mensagem' => "Todos os favoritos foram removidos ($totalRemovidos produtos).",
                'total_removidos' => $totalRemovidos
            ];
        } else {
            return ['sucesso' => false, 'mensagem' => 'Erro ao limpar favoritos.'];
        }
        
    } catch (PDOException $e) {
        error_log("Erro ao limpar favoritos: " . $e->getMessage());
        return ['sucesso' => false, 'mensagem' => 'Erro interno. Tente novamente mais tarde.'];
    }
}

// ==========================================
// ROTEAMENTO DE AÇÕES
// ==========================================

// Verificar se a ação foi especificada
if (!isset($dados['acao'])) {
    retornarErro("Ação não especificada.");
}

$acao = $dados['acao'];
$resposta = ['sucesso' => false, 'mensagem' => 'Ação inválida'];

switch ($acao) {
    case 'adicionar':
        $produto_id = filter_var($dados['produto_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$produto_id || $produto_id <= 0) {
            retornarErro("ID do produto inválido.");
        }
        $resposta = adicionarFavorito($pdo, $usuario_id, $produto_id);
        break;
    case 'remover':
        $produto_id = filter_var($dados['produto_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$produto_id || $produto_id <= 0) {
            retornarErro("ID do produto inválido.");
        }
        $resposta = removerFavorito($pdo, $usuario_id, $produto_id);
        break;
    case 'alternar':
        $produto_id = filter_var($dados['produto_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$produto_id || $produto_id <= 0) {
            retornarErro("ID do produto inválido.");
        }
        $resposta = alternarFavorito($pdo, $usuario_id, $produto_id);
        break;
    case 'verificar':
        $produto_id = filter_var($dados['produto_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$produto_id || $produto_id <= 0) {
            retornarErro("ID do produto inválido.");
        }
        $isFavorito = verificarFavorito($pdo, $usuario_id, $produto_id);
        $resposta = [
            'sucesso' => true,
            'favorito' => $isFavorito,
            'mensagem' => $isFavorito ? 'Produto está nos favoritos' : 'Produto não está nos favoritos'
        ];
        break;
    case 'listar':
        $limite = filter_var($dados['limite'] ?? null, FILTER_VALIDATE_INT);
        $offset = filter_var($dados['offset'] ?? 0, FILTER_VALIDATE_INT);
        $favoritos = obterFavoritos($pdo, $usuario_id, $limite, $offset);
        $total = contarFavoritos($pdo, $usuario_id);
        $resposta = [
            'sucesso' => true,
            'favoritos' => $favoritos,
            'total' => $total,
            'mensagem' => 'Favoritos obtidos com sucesso'
        ];
        break;
    case 'contar':
        $total = contarFavoritos($pdo, $usuario_id);
        $resposta = [
            'sucesso' => true,
            'total' => $total,
            'mensagem' => 'Total de favoritos obtido com sucesso'
        ];
        break;
    case 'limpar':
        $resposta = limparFavoritos($pdo, $usuario_id);
        break;
    default:
        retornarErro("Ação '$acao' não reconhecida.", 400);
        break;
}

// ==========================================
// RETORNAR RESPOSTA
// ==========================================
retornarJSON($resposta);
        
        ?>