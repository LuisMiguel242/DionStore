<?php
session_start();

// Configuração do banco de dados
$host = 'localhost';
$dbname = 'dion_store';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Função para verificar se o usuário está logado
function verificarUsuarioLogado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

// Função para validar se o produto existe (CORRIGIDO)
function validarProduto($pdo, $produto_id) {
    $stmt = $pdo->prepare("SELECT id_produto FROM produtos WHERE id_produto = :produto_id");
    $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch() !== false;
}

// Função para verificar se o produto já está nos favoritos
function produtoJaFavoritado($pdo, $usuario_id, $produto_id) {
    $stmt = $pdo->prepare("SELECT id FROM favoritos WHERE usuario_id = :usuario_id AND produto_id = :produto_id");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch() !== false;
}

// Processar adição aos favoritos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_favorito'])) {
    $resposta = array('sucesso' => false, 'mensagem' => '');
    
    // Verificar se o usuário está logado
    if (!verificarUsuarioLogado()) {
        $resposta['mensagem'] = 'Você precisa estar logado para adicionar favoritos.';
    } else {
        $usuario_id = $_SESSION['usuario_id'];
        $produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
        
        // Validar ID do produto
        if (!$produto_id || $produto_id <= 0) {
            $resposta['mensagem'] = 'ID do produto inválido.';
        } else {
            // Verificar se o produto existe
            if (!validarProduto($pdo, $produto_id)) {
                $resposta['mensagem'] = 'Produto não encontrado.';
            } else {
                // Verificar se já está nos favoritos
                if (produtoJaFavoritado($pdo, $usuario_id, $produto_id)) {
                    $resposta['mensagem'] = 'Produto já está nos seus favoritos.';
                } else {
                    // Adicionar aos favoritos
                    try {
                        $stmt = $pdo->prepare("INSERT INTO favoritos (usuario_id, produto_id, data_adicao) VALUES (:usuario_id, :produto_id, NOW())");
                        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                        $stmt->bindParam(':produto_id', $produto_id, PDO::PARAM_INT);
                        $stmt->execute();
                        
                        $resposta['sucesso'] = true;
                        $resposta['mensagem'] = 'Produto adicionado aos favoritos com sucesso!';
                    } catch (PDOException $e) {
                        $resposta['mensagem'] = 'Erro ao adicionar aos favoritos: ' . $e->getMessage();
                    }
                }
            }
        }
    }
    
    // Retornar resposta em JSON para requisições AJAX
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($resposta);
        exit;
    }
}

// Buscar produtos (camisas) - CORRIGIDO
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE categoria = 1 ORDER BY nome");
$stmt->execute();
$produtos = $stmt->fetchAll();

// Buscar favoritos do usuário logado
$favoritos_usuario = array();
if (verificarUsuarioLogado()) {
    $stmt = $pdo->prepare("SELECT produto_id FROM favoritos WHERE usuario_id = :usuario_id");
    $stmt->bindParam(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
    $stmt->execute();
    $favoritos_usuario = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html class="wide wow-animation" lang="pt-BR">
<head>
    <title>Camisas - Dion</title>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width height=device-height initial-scale=1.0 maximum-scale=1.0 user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <link rel="icon" href="images/diondefinitivobranco.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Work+Sans:300,400,500,700,800%7CPoppins:300,400,700">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/style.css" id="main-styles-link">
    <link rel="stylesheet" href="css/camisas.css" id="main-styles-link">
    <script src="js/camisas.js"></script>

    
    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">
</head>

<body>
    <!-- Novo painel superior -->
    <div class="top-panel">
        <!-- Menu hambúrguer à esquerda -->
        <div class="hamburger-menu" id="hamburgerMenu">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Logo centralizada -->
        <div class="top-panel-logo">
            <a href="index.html">
                <img src="images/diondefinitivobranco.png" alt="Dion Logo">
            </a>
        </div>

        <!-- Área direita com ícones -->
        <div class="top-panel-right">
            <!-- Ícone de Favoritos -->
            <a href="favoritos.php" class="icon-button" id="favoritesIcon" title="Favoritos">
                <i class="mdi mdi-heart"></i>
                <span class="icon-badge" id="favoritesBadge"><?php echo count($favoritos_usuario); ?></span>
            </a>

            <!-- Ícone do Carrinho -->
            <a href="carrinho.php" class="icon-button" id="cartIcon" title="Carrinho">
                <i class="mdi mdi-shopping-cart"></i>
                <span class="icon-badge" id="cartBadge">0</span>
            </a>

            <!-- Avatar do usuário -->
            <div class="user-avatar" id="userProfileIcon">
                <span id="userInitials">
                    <?php 
                    if (verificarUsuarioLogado()) {
                        echo substr($_SESSION['usuario_nome'] ?? 'U', 0, 1);
                    } else {
                        echo 'U';
                    }
                    ?>
                </span>
            </div>
        </div>

        <div class="user-dropdown" id="userDropdown">
            <div class="user-info">
                <div class="user-name" id="userName">
                    <?php echo verificarUsuarioLogado() ? ($_SESSION['usuario_nome'] ?? 'Usuário') : 'Não logado'; ?>
                </div>
                <div class="user-email" id="userEmail">
                    <?php echo verificarUsuarioLogado() ? ($_SESSION['usuario_email'] ?? 'email@exemplo.com') : 'Faça login'; ?>
                </div>
                <div class="user-location">
                    <span class="icon novi-icon mdi mdi-map-marker"></span>
                    <span id="userLocation">Sarutaia, SP</span>
                </div>
            </div>

            <ul class="menu-options">
                <li><a href="index.html"><span class="icon novi-icon mdi mdi-home"></span> Menu Principal</a></li>
                <li><a href="produtos.html"><span class="icon novi-icon mdi mdi-shopping"></span> Produtos</a></li>
                <li><a href="meus-dados.html" id="myDataLink"><span class="icon novi-icon mdi mdi-account"></span> Meus Dados</a></li>
                <li><a href="pedidos.html"><span class="icon novi-icon mdi mdi-package-variant"></span> Meus Pedidos</a></li>
                <li><a href="favoritos.php"><span class="icon novi-icon mdi mdi-heart"></span> Favoritos</a></li>
            </ul>

            <button class="logout-btn" id="logoutBtn">Sair</button>
        </div>
    </div>

    <!-- Menu lateral -->
    <div class="menu-overlay" id="menuOverlay"></div>
    <div class="side-menu" id="sideMenu">
        <div class="side-menu-content">
            <h3>Menu</h3>
            <ul class="side-menu-nav">
                <li><a href="index.html" class="active">Início</a></li>
                <li><a href="about-us.htl">Sobre Nós</a></li>
                <li><a href="produtos.html">Produtos</a></li>
                <li><a href="contacts.html">Contato</a></li>
                <li><a href="pedidos.html">Meus Pedidos</a></li>
                <li><a href="favoritos.php">Favoritos</a></li>
                <li><a href="carrinho.html">Carrinho</a></li>
            </ul>
        </div>
    </div>

    <div class="page">
        <!-- Cabeçalho da página -->
        <section class="page-header">
            <div class="container">
                <h1 class="wow slideInLeft">CAMISAS DION</h1>
                <p class="wow slideInLeft" data-wow-delay="0.2s">Descubra nossa coleção exclusiva de camisas street</p>
            </div>
        </section>

        <!-- Status do usuário -->
        <div class="container">
            <?php /* Removido alerta de boas-vindas do usuário logado */ ?>
        </div>

        <!-- Alertas dinâmicos -->
        <div class="container">
            <div id="alertas"></div>
        </div>

        <!-- Loading spinner -->
        <div class="loading-spinner" id="loading" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; text-align: center;">
            <div class="spinner" style="border: 4px solid #f3f3f3; border-top: 4px solid #ff6b00; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 10px;"></div>
            <p>Processando...</p>
        </div>

        <!-- Filtros -->
        <section class="filters-section">
            <div class="container">
                <div class="filter-group">
                    <span class="filter-label">Filtrar por:</span>
                    <select class="filter-select" id="sizeFilter">
                        <option value="">Todos os tamanhos</option>
                        <option value="P">P</option>
                        <option value="M">M</option>
                        <option value="G">G</option>
                        <option value="GG">GG</option>
                        <option value="XG">XG</option>
                    </select>
                    
                    <select class="filter-select" id="priceFilter">
                        <option value="">Faixa de preço</option>
                        <option value="0-100">Até R$ 100</option>
                        <option value="100-200">R$ 100 - R$ 200</option>
                        <option value="200-300">R$ 200 - R$ 300</option>
                        <option value="300+">Acima de R$ 300</option>
                    </select>
                    
                    <select class="filter-select" id="sortFilter">
                        <option value="newest">Mais recentes</option>
                        <option value="price-low">Menor preço</option>
                        <option value="price-high">Maior preço</option>
                        <option value="popular">Mais populares</option>
                    </select>
                    
                    <div class="search-box">
                        <span class="search-icon">🔍</span>
                        <input type="text" class="search-input" placeholder="Buscar camisas..." id="searchInput">
                    </div>
                </div>
            </div>
        </section>

        <!-- Grid de produtos -->
        <section class="products-grid">
            <div class="container">
                <div class="row" id="productsContainer">
                    <!-- Camisa Dion Brasil -->
                    <div class="col-lg-4 col-md-6 wow" data-category="brasil" data-price="199">
                        <div class="product-card">
                            <div class="product-image">
                                <div class="product-badge">POPULAR</div>
                                <img src="images/dioncamisabrasil.png" alt="Camisa Dion Brasil">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Brasil</h3>
                                <p class="product-description">O Futebol envolvido na arte do Skate. Design exclusivo inspirado na paixão nacional.</p>
                                <div class="product-price">R$ 199,00</div>
                                <div class="product-meta">
                                    <span>99 vendidos</span>
                                    <span>30 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option active">G</div>
                                    <div class="size-option">GG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(1, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(1, this)"
                                            <?php echo in_array(1, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(1, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion Marrom -->
                    <div class="col-lg-4 col-md-6 wow" data-category="casual" data-price="130">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/camisamarromdion.png" alt="Camisa Dion Marrom">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Marrom</h3>
                                <p class="product-description">O Drip de uma forma básica para te vestir. Estilo minimalista e sofisticado.</p>
                                <div class="product-price">R$ 130,00</div>
                                <div class="product-meta">
                                    <span>29 vendidos</span>
                                    <span>199 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                    <div class="size-option">XG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(2, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(2, this)"
                                            <?php echo in_array(2, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(2, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion Street Black -->
                    <div class="col-lg-4 col-md-6 wow" data-category="street" data-price="175">
                        <div class="product-card">
                            <div class="product-image">
                                <div class="product-badge">NOVO</div>
                                <img src="images/camisablackdion.png" alt="Camisa Dion Street Black">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Street Black</h3>
                                <p class="product-description">Estilo urbano em preto clássico. Design moderno para quem vive a cultura de rua.</p>
                                <div class="product-price">R$ 175,00</div>
                                <div class="product-meta">
                                    <span>15 vendidos</span>
                                    <span>45 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(3, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(3, this)"
                                            <?php echo in_array(3, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(3, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion Skateboard -->
                    <div class="col-lg-4 col-md-6 wow" data-category="skate" data-price="165">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/camisaskatedion.png" alt="Camisa Dion Skateboard">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Skateboard</h3>
                                <p class="product-description">Para os verdadeiros skatistas. Design autêntico com referências do mundo do skate.</p>
                                <div class="product-price">R$ 165,00</div>
                                <div class="product-meta">
                                    <span>67 vendidos</span>
                                    <span>80 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(4, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(4, this)"
                                            <?php echo in_array(4, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(4, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion White Premium -->
                    <div class="col-lg-4 col-md-6 wow" data-category="premium" data-price="220">
                        <div class="product-card">
                            <div class="product-image">
                                <div class="product-badge">PREMIUM</div>
                                <img src="images/camisapremiumdion.png" alt="Camisa Dion White Premium">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion White Premium</h3>
                                <p class="product-description">Linha premium com acabamento diferenciado. Qualidade superior para ocasiões especiais.</p>
                                <div class="product-price">R$ 220,00</div>
                                <div class="product-meta">
                                    <span>35 vendidos</span>
                                    <span>25 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(5, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(5, this)"
                                            <?php echo in_array(5, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(5, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion Vintage -->
                    <div class="col-lg-4 col-md-6 wow" data-category="vintage" data-price="155">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/camisavintage.png" alt="Camisa Dion Vintage">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Vintage</h3>
                                <p class="product-description">Estilo retrô com toque moderno. Para quem aprecia o clássico com personalidade.</p>
                                <div class="product-price">R$ 155,00</div>
                                <div class="product-meta">
                                    <span>43 vendidos</span>
                                    <span>60 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                    <div class="size-option">XG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(6, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(6, this)"
                                            <?php echo in_array(6, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(6, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion Neon -->
                    <div class="col-lg-4 col-md-6 wow" data-category="neon" data-price="185">
                        <div class="product-card">
                            <div class="product-image">
                                <div class="product-badge">LIMITADO</div>
                                <img src="images/camisaneondion.png" alt="Camisa Dion Neon">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Neon</h3>
                                <p class="product-description">Edição limitada com cores vibrantes. Para quem quer se destacar na multidão.</p>
                                <div class="product-price">R$ 185,00</div>
                                <div class="product-meta">
                                    <span>12 vendidos</span>
                                    <span>18 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(7, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(7, this)"
                                            <?php echo in_array(7, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(7, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion Urban -->
                    <div class="col-lg-4 col-md-6 wow" data-category="urban" data-price="145">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/camisaruraldion.png" alt="Camisa Dion Urban">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Urban</h3>
                                <p class="product-description">Estilo urbano contemporâneo. Perfeita para o dia a dia na cidade.</p>
                                <div class="product-price">R$ 145,00</div>
                                <div class="product-meta">
                                    <span>78 vendidos</span>
                                    <span>95 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                    <div class="size-option">XG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(8, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(8, this)"
                                            <?php echo in_array(8, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(8, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion Tropical -->
                    <div class="col-lg-4 col-md-6 wow" data-category="tropical" data-price="170">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/camisatropicaldion.png" alt="Camisa Dion Tropical">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Tropical</h3>
                                <p class="product-description">Inspiração tropical com estilo único. Para quem busca um visual fresco e descontraído.</p>
                                <div class="product-price">R$ 170,00</div>
                                <div class="product-meta">
                                    <span>22 vendidos</span>
                                    <span>40 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(9, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(9, this)"
                                            <?php echo in_array(9, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(9, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion Classic -->
                    <div class="col-lg-4 col-md-6 wow" data-category="classic" data-price="140">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/camisaclassicdion.png" alt="Camisa Dion Classic">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Classic</h3>
                                <p class="product-description">Design clássico atemporal. Peça versátil para qualquer ocasião.</p>
                                <div class="product-price">R$ 140,00</div>
                                <div class="product-meta">
                                    <span>105 vendidos</span>
                                    <span>120 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                    <div class="size-option">XG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(10, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(10, this)"
                                            <?php echo in_array(10, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(10, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion Exclusive -->
                    <div class="col-lg-4 col-md-6 wow" data-category="exclusive" data-price="250">
                        <div class="product-card">
                            <div class="product-image">
                                <div class="product-badge">EXCLUSIVO</div>
                                <img src="images/camisaexclusivadion.png" alt="Camisa Dion Exclusive">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Exclusive</h3>
                                <p class="product-description">Peça exclusiva de edição limitada. Para os verdadeiros conhecedores da marca.</p>
                                <div class="product-price">R$ 250,00</div>
                                <div class="product-meta">
                                    <span>8 vendidos</span>
                                    <span>12 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(11, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(11, this)"
                                            <?php echo in_array(11, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(11, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Camisa Dion Summer -->
                    <div class="col-lg-4 col-md-6 wow" data-category="summer" data-price="160">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/camisasummerdion.png" alt="Camisa Dion Summer">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Camisa Dion Summer</h3>
                                <p class="product-description">Coleção verão com cores vibrantes. Perfeita para os dias mais quentes.</p>
                                <div class="product-price">R$ 160,00</div>
                                <div class="product-meta">
                                    <span>45 vendidos</span>
                                    <span>65 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">P</div>
                                    <div class="size-option">M</div>
                                    <div class="size-option">G</div>
                                    <div class="size-option">GG</div>
                                    <div class="size-option">XG</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(12, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(12, this)"
                                            <?php echo in_array(12, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(12, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Paginação -->
        <section class="pagination-section">
            <div class="container">
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        <span>Mostrando 1-12 de 12 produtos</span>
                    </div>
                    <div class="pagination-controls">
                        <button class="pagination-btn" disabled>« Anterior</button>
                        <button class="pagination-btn active">1</button>
                        <button class="pagination-btn" disabled>Próximo »</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção de características -->
        <section class="features-section">
            <div class="container">
                <h2 class="section-title">Por que escolher as Camisas Dion?</h2>
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="mdi mdi-tshirt-crew"></i>
                            </div>
                            <h3>Qualidade Premium</h3>
                            <p>Tecidos selecionados e acabamento impecável em todas as peças.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="mdi mdi-truck-delivery"></i>
                            </div>
                            <h3>Entrega Rápida</h3>
                            <p>Envio em 24h para todo o Brasil com frete grátis acima de R$ 199.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="mdi mdi-shield-check"></i>
                            </div>
                            <h3>Garantia de Satisfação</h3>
                            <p>30 dias para trocas e devoluções sem complicação.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter -->
        <section class="newsletter-section">
            <div class="container">
                <div class="newsletter-content">
                    <h2>Receba nossas novidades</h2>
                    <p>Seja o primeiro a saber sobre lançamentos e promoções exclusivas</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="Seu melhor e-mail" required>
                        <button type="submit">Inscrever-se</button>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="footer-section">
                        <h4>Dion Store</h4>
                        <p>Sua marca de estilo urbano e streetwear. Qualidade e design em cada peça.</p>
                        <div class="social-links">
                            <a href="#"><i class="mdi mdi-instagram"></i></a>
                            <a href="#"><i class="mdi mdi-facebook"></i></a>
                            <a href="#"><i class="mdi mdi-twitter"></i></a>
                            <a href="#"><i class="mdi mdi-youtube"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="footer-section">
                        <h5>Navegação</h5>
                        <ul>
                            <li><a href="index.php">Início</a></li>
                            <li><a href="produtos.php">Produtos</a></li>
                            <li><a href="about-us.php">Sobre</a></li>
                            <li><a href="contato.php">Contato</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="footer-section">
                        <h5>Minha Conta</h5>
                        <ul>
                            <li><a href="meus-dados.html">Meus Dados</a></li>
                            <li><a href="pedidos.html">Pedidos</a></li>
                            <li><a href="favoritos.php">Favoritos</a></li>
                            <li><a href="carrinho.html">Carrinho</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="footer-section">
                        <h5>Contato</h5>
                        <p><i class="mdi mdi-map-marker"></i> Sarutaiá, SP - Brasil</p>
                        <p><i class="mdi mdi-phone"></i> (14) 99999-9999</p>
                        <p><i class="mdi mdi-email"></i> contato@dionstore.com</p>
                    </div>
                </div>
            </div>
            <hr>
            <div class="footer-bottom">
                <p>&copy; 2024 Dion Store. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/core.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/camisas.js"></script>
    <script>
                // Elementos do dropdown do usuário
                const userProfileIcon = document.getElementById('userProfileIcon');
                const userDropdown = document.getElementById('userDropdown');
                const userInitials = document.getElementById('userInitials');
                const userName = document.getElementById('userName');
                const userEmail = document.getElementById('userEmail');
                const userLocation = document.getElementById('userLocation');
                const logoutBtn = document.getElementById('logoutBtn');
               

                // Função para abrir/fechar menu lateral
                function toggleSideMenu() {
                  hamburgerMenu.classList.toggle('active');
                  sideMenu.classList.toggle('active');
                  menuOverlay.classList.toggle('active');

                  // Previne scroll do body quando menu está aberto
                  if (sideMenu.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                  } else {
                    document.body.style.overflow = '';
                  }
                }

                // Função para fechar menu lateral
                function closeSideMenu() {
                  hamburgerMenu.classList.remove('active');
                  sideMenu.classList.remove('active');
                  menuOverlay.classList.remove('active');
                  document.body.style.overflow = '';
                }

                // Event listeners para menu hambúrguer
                hamburgerMenu.addEventListener('click', toggleSideMenu);
                menuOverlay.addEventListener('click', closeSideMenu);

                // Fechar menu ao clicar em um link
                const sideMenuLinks = sideMenu.querySelectorAll('a');
                sideMenuLinks.forEach(link => {
                  link.addEventListener('click', closeSideMenu);
                });

                // Funções do dropdown do usuário
                function checkUserLogin() {
                  // Simulando dados do usuário - substituir pela lógica real
                  const userData = {
                    nome: 'João Silva',
                    email: 'joao@exemplo.com',
                    cidade: 'Sarutaia',
                    estado: 'SP'
                  };

                  if (userData) {
                    showUserProfile(userData);
                  } else {
                    userProfileIcon.style.display = 'none';
                  }
                }
                document.addEventListener('DOMContentLoaded', function () {
                  const userProfileIcon = document.getElementById('userProfileIcon');
                  const userDropdown = document.getElementById('userDropdown');
                  const userInitials = document.getElementById('userInitials');
                  const userName = document.getElementById('userName');
                  const userEmail = document.getElementById('userEmail');
                  const userLocation = document.getElementById('userLocation');
                  const logoutBtn = document.getElementById('logoutBtn');
                  const myDataLink = document.getElementById('myDataLink');

                  function checkUserLogin() {
                    const userData = JSON.parse(localStorage.getItem('userData'));
                    if (userData) {
                      showUserProfile(userData);
                    } else {
                      userProfileIcon.style.display = 'none';
                    }
                  }

                  function showUserProfile(userData) {
                    userProfileIcon.style.display = 'block';
                    const nameParts = userData.nome.trim().split(' ');
                    userInitials.textContent = nameParts.length > 1
                      ? nameParts[0][0] + nameParts[nameParts.length - 1][0]
                      : userData.nome[0];
                    userName.textContent = userData.nome || 'Usuário';
                    userEmail.textContent = userData.email || 'email@exemplo.com';
                    userLocation.textContent = userData.cidade && userData.estado
                      ? `${userData.cidade}, ${userData.estado}`
                      : 'Cidade, Estado';
                  }

                  function toggleUserDropdown() {
                    userDropdown.classList.toggle('active');
                  }

                  function logout() {
                    localStorage.removeItem('userData');
                    window.location.href = 'login.php';
                  }

                  userProfileIcon?.addEventListener('click', function (e) {
                    e.stopPropagation();
                    toggleUserDropdown();
                  });

                  document.addEventListener('click', function (event) {
                    if (userDropdown.classList.contains('active') && !userProfileIcon.contains(event.target)) {
                      userDropdown.classList.remove('active');
                    }
                  });

                  myDataLink?.addEventListener('click', function (e) {
                    e.preventDefault();
                    const userData = JSON.parse(localStorage.getItem('userData'));
                    if (userData) {
                      window.location.href = 'meus-dados.html';
                    } else {
                      alert('Você precisa estar logado para acessar seus dados.');
                      window.location.href = 'login.php';
                    }
                  });

                  logoutBtn?.addEventListener('click', logout);
                  checkUserLogin();
                });
              
        // Animação da página
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.product-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });
            
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.3s ease';
                observer.observe(card);
            });
        });

        // Função para adicionar aos favoritos
        function adicionarFavorito(produto_id, button) {
            // Verificar se o usuário está logado
            <?php if (!verificarUsuarioLogado()): ?>
                alert('Você precisa estar logado para adicionar produtos aos favoritos.');
                return;
            <?php endif; ?>
            
            // Mostrar loading
            const loading = document.getElementById('loading');
            loading.style.display = 'block';
            
            // Desabilitar o botão
            button.disabled = true;
            
            // Criar FormData
            const formData = new FormData();
            formData.append('adicionar_favorito', '1');
            formData.append('produto_id', produto_id);
            formData.append('ajax', '1');
            
            // Fazer requisição AJAX
            fetch('camisas.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Esconder loading
                loading.style.display = 'none';
                
                if (data.sucesso) {
                    // Atualizar botão
                    button.classList.add('favorited');
                    button.querySelector('i').classList.remove('mdi-heart-outline');
                    button.querySelector('i').classList.add('mdi-heart');
                    
                    // Atualizar contador de favoritos
                    const badge = document.getElementById('favoritesBadge');
                    if (badge) {
                        const count = parseInt(badge.textContent) + 1;
                        badge.textContent = count;
                    }
                    
                    // Mostrar sucesso
                    mostrarAlerta('success', data.mensagem);
                } else {
                    // Reabilitar botão em caso de erro
                    button.disabled = false;
                    // mostrarAlerta('error', data.mensagem);
                }
            })
            .catch(error => {
                // Esconder loading
                loading.style.display = 'none';
                // Reabilitar botão
                button.disabled = false;
                console.error('Erro:', error);
                // mostrarAlerta('error', 'Erro ao processar solicitação.');
                // alert('Erro ao adicionar aos favoritos.');
            });
        }

        // Função para mostrar alertas
        function mostrarAlerta(tipo, mensagem) {
            const alertas = document.getElementById('alertas');
            const classeAlerta = tipo === 'success' ? 'alert-success' : 'alert-danger';
            
            const alerta = document.createElement('div');
            alerta.className = `alert ${classeAlerta} alert-dismissible fade show`;
            alerta.innerHTML = `
                ${mensagem}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            alertas.appendChild(alerta);
            
            // Remover alerta após 5 segundos
            setTimeout(() => {
                alerta.remove();
            }, 5000);
        }

        // Filtros de produtos
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const title = product.querySelector('.product-title').textContent.toLowerCase();
                const description = product.querySelector('.product-description').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    product.closest('.col-lg-4').style.display = 'block';
                } else {
                    product.closest('.col-lg-4').style.display = 'none';
                }
            });
        });

        // Filtro por preço
        document.getElementById('priceFilter').addEventListener('change', function(e) {
            const range = e.target.value;
            const products = document.querySelectorAll('.col-lg-4');
            
            products.forEach(product => {
                const price = parseInt(product.dataset.price);
                let show = false;
                
                switch(range) {
                    case '0-100':
                        show = price <= 100;
                        break;
                    case '100-200':
                        show = price > 100 && price <= 200;
                        break;
                    case '200-300':
                        show = price > 200 && price <= 300;
                        break;
                    case '300+':
                        show = price > 300;
                        break;
                    default:
                        show = true;
                }
                
                product.style.display = show ? 'block' : 'none';
            });
        });

        // Ordenação
        document.getElementById('sortFilter').addEventListener('change', function(e) {
            const sortType = e.target.value;
            const container = document.getElementById('productsContainer');
            const products = Array.from(container.children);
            
            products.sort((a, b) => {
                switch(sortType) {
                    case 'price-low':
                        return parseInt(a.dataset.price) - parseInt(b.dataset.price);
                    case 'price-high':
                        return parseInt(b.dataset.price) - parseInt(a.dataset.price);
                    case 'name':
                        return a.querySelector('.product-title').textContent.localeCompare(b.querySelector('.product-title').textContent);
                    default:
                        return 0;
                }
            });
            
            products.forEach(product => container.appendChild(product));
        });

        // Seleção de tamanho
        document.querySelectorAll('.size-option').forEach(option => {
            option.addEventListener('click', function() {
                const parent = this.closest('.product-sizes');
                parent.querySelectorAll('.size-option').forEach(o => o.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // CSS para animação de spinner
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>