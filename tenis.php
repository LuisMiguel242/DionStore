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

// Função para validar se o produto existe
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

// Buscar produtos (tênis) - categoria 2
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE categoria = 2 ORDER BY nome");
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
    <title>Tênis - Dion</title>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width height=device-height initial-scale=1.0 maximum-scale=1.0 user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <link rel="icon" href="images/dion-definitivo-branco.png" type="image/png">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Work+Sans:300,400,500,700,800%7CPoppins:300,400,700">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/style.css" id="main-styles-link">
    <link rel="stylesheet" href="css/tenis.css" id="main-styles-link">
    <script src="js/tenis.js"></script>

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
            <a href="index.php">
                <img src="images/dion-definitivo-branco.png" alt="Dion Logo">
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
                <li><a href="about-us.html">Sobre Nós</a></li>
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
                <h1 class="wow slideInLeft">TÊNIS DION</h1>
                <p class="wow slideInLeft" data-wow-delay="0.2s">Conforto e estilo para seus pés. Descubra nossa coleção de tênis streetwear</p>
            </div>
        </section>

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
                        <option value="36">36</option>
                        <option value="37">37</option>
                        <option value="38">38</option>
                        <option value="39">39</option>
                        <option value="40">40</option>
                        <option value="41">41</option>
                        <option value="42">42</option>
                        <option value="43">43</option>
                        <option value="44">44</option>
                        <option value="45">45</option>
                    </select>
                    
                    <select class="filter-select" id="priceFilter">
                        <option value="">Faixa de preço</option>
                        <option value="0-300">Até R$ 300</option>
                        <option value="300-500">R$ 300 - R$ 500</option>
                        <option value="500-800">R$ 500 - R$ 800</option>
                        <option value="800+">Acima de R$ 800</option>
                    </select>
                    
                    <select class="filter-select" id="sortFilter">
                        <option value="newest">Mais recentes</option>
                        <option value="price-low">Menor preço</option>
                        <option value="price-high">Maior preço</option>
                        <option value="popular">Mais populares</option>
                    </select>
                    
                    <div class="search-box">
                        <span class="search-icon">🔍</span>
                        <input type="text" class="search-input" placeholder="Buscar tênis..." id="searchInput">
                    </div>
                </div>
            </div>
        </section>

        <!-- Grid de produtos -->
        <section class="products-grid">
            <div class="container">
                <div class="row" id="productsContainer">
                    
                    <!-- Tênis Dion Street Runner -->
                    <div class="col-lg-4 col-md-6 wow" data-category="street" data-price="350">
                        <div class="product-card">
                            <div class="product-image">
                                <div class="product-badge">POPULAR</div>
                                <img src="https://via.placeholder.com/400x400/1a1a1a/ffffff?text=STREET+RUNNER" alt="Tênis Dion Street Runner">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Tênis Dion Street Runner</h3>
                                <p class="product-description">Tênis versátil para o dia a dia urbano. Conforto e estilo em cada passo.</p>
                                <div class="product-price">R$ 350,00</div>
                                <div class="product-meta">
                                    <span>127 vendidos</span>
                                    <span>45 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">38</div>
                                    <div class="size-option">39</div>
                                    <div class="size-option active">40</div>
                                    <div class="size-option">41</div>
                                    <div class="size-option">42</div>
                                    <div class="size-option">43</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(13, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(13, this)"
                                            <?php echo in_array(13, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(13, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tênis Dion Air Max -->
                    <div class="col-lg-4 col-md-6 wow" data-category="sport" data-price="420">
                        <div class="product-card">
                            <div class="product-image">
                                <div class="product-badge">NOVO</div>
                                <img src="https://via.placeholder.com/400x400/ff6b00/ffffff?text=AIR+MAX" alt="Tênis Dion Air Max">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Tênis Dion Air Max</h3>
                                <p class="product-description">Tecnologia de amortecimento avançada. Perfeito para corridas e atividades esportivas.</p>
                                <div class="product-price">R$ 420,00</div>
                                <div class="product-meta">
                                    <span>89 vendidos</span>
                                    <span>32 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">39</div>
                                    <div class="size-option">40</div>
                                    <div class="size-option">41</div>
                                    <div class="size-option">42</div>
                                    <div class="size-option">43</div>
                                    <div class="size-option">44</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(14, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(14, this)"
                                            <?php echo in_array(14, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(14, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tênis Dion Skate Pro -->
                    <div class="col-lg-4 col-md-6 wow" data-category="skate" data-price="385">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="https://via.placeholder.com/400x400/333333/ffffff?text=SKATE+PRO" alt="Tênis Dion Skate Pro">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Tênis Dion Skate Pro</h3>
                                <p class="product-description">Desenvolvido especialmente para skatistas. Durabilidade e grip superiores.</p>
                                <div class="product-price">R$ 385,00</div>
                                <div class="product-meta">
                                    <span>156 vendidos</span>
                                    <span>28 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">38</div>
                                    <div class="size-option">39</div>
                                    <div class="size-option">40</div>
                                    <div class="size-option">41</div>
                                    <div class="size-option">42</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(15, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(15, this)"
                                            <?php echo in_array(15, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(15, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tênis Dion Classic White -->
                    <div class="col-lg-4 col-md-6 wow" data-category="classic" data-price="320">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="https://via.placeholder.com/400x400/ffffff/333333?text=CLASSIC+WHITE" alt="Tênis Dion Classic White">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Tênis Dion Classic White</h3>
                                <p class="product-description">O branco que nunca sai de moda. Elegância e simplicidade em um só produto.</p>
                                <div class="product-price">R$ 320,00</div>
                                <div class="product-meta">
                                    <span>201 vendidos</span>
                                    <span>67 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">36</div>
                                    <div class="size-option">37</div>
                                    <div class="size-option">38</div>
                                    <div class="size-option">39</div>
                                    <div class="size-option">40</div>
                                    <div class="size-option">41</div>
                                    <div class="size-option">42</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(16, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(16, this)"
                                            <?php echo in_array(16, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(16, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tênis Dion Boost -->
                    <div class="col-lg-4 col-md-6 wow" data-category="running" data-price="480">
                        <div class="product-card">
                            <div class="product-image">
                                <div class="product-badge">PREMIUM</div>
                                <img src="https://via.placeholder.com/400x400/4169E1/ffffff?text=BOOST" alt="Tênis Dion Boost">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Tênis Dion Boost</h3>
                                <p class="product-description">Tecnologia Boost para máximo retorno de energia. Ideal para corridas de longa distância.</p>
                                <div class="product-price">R$ 480,00</div>
                                <div class="product-meta">
                                    <span>73 vendidos</span>
                                    <span>19 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">40</div>
                                    <div class="size-option">41</div>
                                    <div class="size-option">42</div>
                                    <div class="size-option">43</div>
                                    <div class="size-option">44</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(17, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(17, this)"
                                            <?php echo in_array(17, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(17, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tênis Dion High Top -->
                    <div class="col-lg-4 col-md-6 wow" data-category="casual" data-price="365">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="https://via.placeholder.com/400x400/8B0000/ffffff?text=HIGH+TOP" alt="Tênis Dion High Top">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Tênis Dion High Top</h3>
                                <p class="product-description">Estilo retrô com cano alto. Proteção e estilo para looks despojados.</p>
                                <div class="product-price">R$ 365,00</div>
                                <div class="product-meta">
                                    <span>94 vendidos</span>
                                    <span>41 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">38</div>
                                    <div class="size-option">39</div>
                                    <div class="size-option">40</div>
                                    <div class="size-option">41</div>
                                    <div class="size-option">42</div>
                                    <div class="size-option">43</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(18, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(18, this)"
                                            <?php echo in_array(18, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(18, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tênis Dion Neon Flash -->
                    <div class="col-lg-4 col-md-6 wow" data-category="neon" data-price="395">
                        <div class="product-card">
                            <div class="product-image">
                                <div class="product-badge">LIMITADO</div>
                                <img src="https://via.placeholder.com/400x400/00ff00/000000?text=NEON+FLASH" alt="Tênis Dion Neon Flash">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Tênis Dion Neon Flash</h3>
                                <p class="product-description">Edição limitada com detalhes neon. Para quem quer chamar atenção por onde passa.</p>
                                <div class="product-price">R$ 395,00</div>
                                <div class="product-meta">
                                    <span>34 vendidos</span>
                                    <span>15 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">39</div>
                                    <div class="size-option">40</div>
                                    <div class="size-option">41</div>
                                    <div class="size-option">42</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(19, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(19, this)"
                                            <?php echo in_array(19, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(19, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tênis Dion Urban Walker -->
                    <div class="col-lg-4 col-md-6 wow" data-category="urban" data-price="340">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="https://via.placeholder.com/400x400/696969/ffffff?text=URBAN+WALKER" alt="Tênis Dion Urban Walker">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">Tênis Dion Urban Walker</h3>
                                <p class="product-description">Confortável para longas caminhadas urbanas. Design moderno e funcional.</p>
                                <div class="product-price">R$ 340,00</div>
                                <div class="product-meta">
                                    <span>112 vendidos</span>
                                    <span>58 em estoque</span>
                                </div>
                                <div class="product-sizes">
                                    <div class="size-option">37</div>
                                    <div class="size-option">38</div>
                                    <div class="size-option">39</div>
                                    <div class="size-option">40</div>
                                    <div class="size-option">41</div>
                                    <div class="size-option">42</div>
                                    <div class="size-option">43</div>
                                </div>
                                <div class="product-actions">
                                    <button class="btn-add-cart">Adicionar ao Carrinho</button>
                                    <button class="btn-favorite <?php echo in_array(20, $favoritos_usuario) ? 'favorited' : ''; ?>" 
                                            onclick="adicionarFavorito(20, this)"
                                            <?php echo in_array(20, $favoritos_usuario) ? 'disabled' : ''; ?>>
                                        <i class="mdi mdi-heart<?php echo in_array(20, $favoritos_usuario) ? '' : '-outline'; ?>"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- Fim da row -->
            </div> <!-- Fim da container -->
        </section>

        <!-- Paginação -->
        <section class="pagination-section">
            <div class="container">
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        <span>Mostrando todos os tênis</span>
                    </div>
                    <div class="pagination-controls">
                        <button class="pagination-btn" disabled>« Anterior</button>
                        <button class="pagination-btn active">1</button>
                        <button class="pagination-btn" disabled>Próximo »</button>
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
    </div> <!-- Fim da page -->

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
    <script src="js/tenis.js"></script>
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
        
    // Função para adicionar aos favoritos (AJAX para backend/favoritos_api.php)
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
        // Fazer requisição AJAX para o backend centralizado
        fetch('backend/favoritos_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ acao: 'adicionar', produto_id: produto_id })
        })
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            if (data.sucesso) {
                // Atualizar botão
                button.classList.add('favorited');
                button.querySelector('i').classList.remove('mdi-heart-outline');
                button.querySelector('i').classList.add('mdi-heart');
                // Atualizar contador de favoritos consultando o backend
                fetch('backend/favoritos_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ acao: 'contar' })
                })
                .then(resp => resp.json())
                .then(res => {
                    if (res.sucesso && typeof res.total === 'number') {
                        const badge = document.getElementById('favoritesBadge');
                        if (badge) badge.textContent = res.total;
                    }
                });
                // Mostrar sucesso
                mostrarAlerta('success', data.mensagem);
            } else {
                button.disabled = false;
                // mostrarAlerta('error', data.mensagem);
            }
        })
        .catch(error => {
            loading.style.display = 'none';
            button.disabled = false;
            console.error('Erro:', error);
            // mostrarAlerta('error', 'Erro ao processar solicitação.');
        });
    }
    // Função para mostrar alertas (igual camisas.php)
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
        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }
    </script>
</body>
</html>