<?php
session_start();
require_once 'backend\login.php';
?>
<!DOCTYPE html>
<html class="wide wow-animation" lang="en">

<head>
  <title>Favoritos - Dion</title>
  <meta name="format-detection" content="telephone=no">
  <meta name="viewport"
    content="width=device-width height=device-height initial-scale=1.0 maximum-scale=1.0 user-scalable=0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="utf-8">
  <link rel="icon" href="images/diondefinitivobranco.png" type="images/diondefinitivobranco.png">
  <link rel="stylesheet" type="text/css"
    href="//fonts.googleapis.com/css?family=Work+Sans:300,400,500,700,800%7CPoppins:300,400,700">
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/fonts.css">
  <link rel="stylesheet" href="css/style.css" id="main-styles-link">
  <link rel="stylesheet" href="css/index.css" id="main-styles-link">
  <link rel="stylesheet" href="css/favoritos.css">
  <script src="js/favoritos.js" defer></script>
</head>

<body>
  <!-- Loading screen -->
  <div id="loadingScreen"
    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); display: flex; justify-content: center; align-items: center; z-index: 9999; color: white;">
    <div style="text-align: center;">
      <div
        style="border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 50px; height: 50px; animation: spin 2s linear infinite; margin: 0 auto 20px;">
      </div>
      <p>Carregando seus favoritos...</p>
    </div>
  </div>

  <style>
    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }

      100% {
        transform: rotate(360deg);
      }
    }
  </style>

  <!-- Painel superior atualizado -->
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
      <a href="favoritos.html" class="icon-button active" id="favoritesIcon" title="Favoritos">
        <i class="mdi mdi-heart"></i>
        <span class="icon-badge" id="favoritesBadge" style="display: none;">0</span>
      </a>

      <!-- Ícone do Carrinho -->
      <a href="carrinho.html" class="icon-button" id="cartIcon" title="Carrinho">
        <i class="mdi mdi-shopping-cart"></i>
        <span class="icon-badge" id="cartBadge">0</span>
      </a>

      <!-- Avatar do usuário -->
      <div class="user-avatar" id="userProfileIcon" style="display: none;">
        <span id="userInitials">U</span>
      </div>
    </div>

    <!-- Dropdown do usuário -->
    <div class="user-dropdown" id="userDropdown">
      <div class="user-info">
        <div class="user-name" id="userName">Nome do Usuário</div>
        <div class="user-email" id="userEmail">email@exemplo.com</div>
        <div class="user-location">
          <span class="icon novi-icon mdi mdi-map-marker"></span>
          <span id="userLocation">Sarutaia, SP</span>
        </div>
      </div>

      <ul class="menu-options">
        <li>
          <a href="index.html">
            <span class="icon novi-icon mdi mdi-home"></span>
            Menu Principal
          </a>
        </li>
        <li>
          <a href="produtos.html">
            <span class="icon novi-icon mdi mdi-shopping"></span>
            Produtos
          </a>
        </li>
        <li>
          <a href="meus-dados.php" id="myDataLink">
            <span class="icon novi-icon mdi mdi-account"></span>
            Meus Dados
          </a>
        </li>
        <li>
          <a href="pedidos.html">
            <span class="icon novi-icon mdi mdi-package-variant"></span>
            Meus Pedidos
          </a>
        </li>
        <li>
          <a href="favoritos.html" class="active">
            <span class="icon novi-icon mdi mdi-heart"></span>
            Favoritos
          </a>
        </li>
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
        <li><a href="index.html">Início</a></li>
        <li><a href="about-us.html">Sobre Nós</a></li>
        <li><a href="produtos.html">Produtos</a></li>
        <li><a href="contacts.html">Contato</a></li>
        <li><a href="pedidos.html">Meus Pedidos</a></li>
        <li><a href="favoritos.html" class="active">Favoritos</a></li>
        <li><a href="carrinho.html">Carrinho</a></li>
      </ul>
    </div>
  </div>

  <div class="page">
    <!-- Header da página de favoritos -->
    <section class="favorites-header">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h1 class="text-white font-weight-bold mb-3">
              <i class="mdi mdi-heart text-danger"></i> Meus Favoritos
            </h1>
            <p class="text-white-50 mb-0">Aqui estão todos os produtos que você marcou como favoritos</p>
          </div>
          <div class="col-md-4">
            <div class="favorites-stats text-white">
              <div class="stat-item">
                <span>Total de Favoritos:</span>
                <span class="favorites-count" id="totalFavorites">0</span>
              </div>
              <div class="stat-item">
                <span>Valor Total:</span>
                <span class="favorites-count" id="totalValue">R$ 0,00</span>
              </div>
              <div class="stat-item">
                <span>Economia Possível:</span>
                <span class="favorites-count text-success" id="totalSavings">R$ 0,00</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Conteúdo principal -->
    <section class="favorites-container">
      <div class="container">
        <!-- Filtros -->
        <div class="filter-options">
          <button class="filter-btn active" data-filter="todos">Todos</button>
          <button class="filter-btn" data-filter="1">Camisetas</button>
          <button class="filter-btn" data-filter="2">Calças</button>
          <button class="filter-btn" data-filter="3">Vestidos</button>
          <button class="filter-btn" data-filter="4">Casacos</button>
          <button class="filter-btn" data-filter="5">Acessórios</button>
          <button class="filter-btn" data-filter="promocao">Em Promoção</button>
        </div>

        <!-- Ações dos favoritos -->
        <div class="favorites-actions" id="favoritesActions" style="display: none;">
          <button class="btn-add-all-cart" id="addAllToCart">
            <i class="mdi mdi-cart-plus"></i> Adicionar Todos ao Carrinho
          </button>
          <button class="btn-clear-all" id="clearAllFavorites">
            <i class="mdi mdi-delete"></i> Limpar Todos os Favoritos
          </button>
        </div>

        <!-- Grid de produtos favoritos -->
        <div class="favorites-grid" id="favoritesGrid" style="display: none;">
          <!-- Os produtos serão carregados dinamicamente via JavaScript -->
        </div>

        <!-- Estado vazio -->
        <div class="empty-favorites" id="emptyFavorites" style="display: none;">
          <div class="empty-favorites-icon">
            <i class="mdi mdi-heart-outline"></i>
          </div>
          <h3>Nenhum produto nos favoritos</h3>
          <p>Você ainda não adicionou produtos aos seus favoritos.</p>
          <p>Explore nossa coleção e adicione os produtos que mais gostar!</p>
          <a href="produtos.html" class="button button-primary button-winona button-md">
            Explorar Produtos
          </a>
        </div>

        <!-- Mensagem de erro -->
        <div class="error-message" id="errorMessage" style="display: none;">
          <div class="error-icon">
            <i class="mdi mdi-alert-circle"></i>
          </div>
          <h3>Erro ao carregar favoritos</h3>
          <p>Ocorreu um erro ao carregar seus produtos favoritos. Tente novamente.</p>
          <button class="button button-primary button-winona button-md" onclick="location.reload()">
            Tentar Novamente
          </button>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="section novi-background footer-advanced bg-gray-700">
      <div class="footer-advanced-main">
        <div class="container">
          <div class="row row-50">
            <div class="col-lg-4">
              <h5 class="font-weight-bold text-uppercase text-white">Sobre nós</h5>
              <p class="footer-advanced-text">Dion é uma loja que está se estruturando agora, mas veio para trazer a
                arte do Street e da nossa moda. Dion promete ter a sua confiança para poder te trazer satisfação.</p>
            </div>
            <div class="col-sm-7 col-md-5 col-lg-4">
              <h5 class="font-weight-bold text-uppercase text-white">Siga a gente nas redes sociais</h5>
              <article class="post-inline">
                <p class="post-inline-title"><a href="#">@dionoficial - Acompanhe nossas novidades</a></p>
                <ul class="post-inline-meta">
                  <li>Dion</li>
                  <li><a href="#">2 dias atrás</a></li>
                </ul>
              </article>
            </div>
            <div class="col-sm-5 col-md-7 col-lg-4">
              <h5 class="font-weight-bold text-uppercase text-white">Galeria</h5>
              <div class="row row-x-10">
                <div class="col-3 col-sm-4 col-md-3">
                  <img class="thumbnail-minimal-image" src="images/shapes.png" alt="Imagem da galeria" />
                </div>
                <div class="col-3 col-sm-4 col-md-3">
                  <img class="thumbnail-minimal-image" src="images/product-1.jpg" alt="Produto em destaque" />
                </div>
                <div class="col-3 col-sm-4 col-md-3">
                  <images/product-2.jpg" alt="Produto em destaque" />
                </div>
                <div class="col-3 col-sm-4 col-md-3">
                  <img class="thumbnail-minimal-image" src="images/product-3.jpg" alt="Produto em destaque" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-advanced-aside">
        <div class="container">
          <div class="footer-advanced-layout">
            <div>
              <ul class="footer-advanced-list">
                <li><a href="index.html">Início</a></li>
                <li><a href="about-us.html">Sobre</a></li>
                <li><a href="produtos.html">Produtos</a></li>
                <li><a href="contacts.html">Contato</a></li>
              </ul>
            </div>
            <div>
              <ul class="footer-advanced-list">
                <li><a href="pedidos.html">Meus Pedidos</a></li>
                <li><a href="favoritos.html">Favoritos</a></li>
                <li><a href="carrinho.html">Carrinho</a></li>
                <li><a href="meus-dados.php">Minha Conta</a></li>
              </ul>
            </div>
            <div>
              <ul class="footer-advanced-list">
                <li><a href="#">Política de Privacidade</a></li>
                <li><a href="#">Termos de Uso</a></li>
                <li><a href="#">Trocas e Devoluções</a></li>
                <li><a href="#">FAQ</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <hr />
        <div class="footer-advanced-bottom">
          <p class="rights">
            <span>&copy;&nbsp;</span>
            <span class="copyright-year"></span>
            <span>&nbsp;</span>
            <span>Dion</span>
            <span>. Todos os direitos reservados. Desenvolvido por&nbsp;</span>
            <a href="#">Dion Team</a>
          </p>
        </div>
      </div>
    </footer>
  </div>

  <!-- Scripts básicos -->
  <script src="js/core.min.js"></script>
  <script src="js/script.js"></script>
  
  <!-- Scripts específicos da página -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
  // Elementos do menu hambúrguer
  const hamburgerMenu = document.getElementById('hamburgerMenu');
  const sideMenu = document.getElementById('sideMenu');
  const menuOverlay = document.getElementById('menuOverlay');

  // Elementos do dropdown do usuário
  const userProfileIcon = document.getElementById('userProfileIcon');
  const userDropdown = document.getElementById('userDropdown');
  const userInitials = document.getElementById('userInitials');
  const userName = document.getElementById('userName');
  const userEmail = document.getElementById('userEmail');
  const userLocation = document.getElementById('userLocation');
  const logoutBtn = document.getElementById('logoutBtn');
  const myDataLink = document.getElementById('myDataLink');

  // Elementos específicos dos favoritos
  const loadingScreen = document.getElementById('loadingScreen');
  const favoritesGrid = document.getElementById('favoritesGrid');
  const emptyFavorites = document.getElementById('emptyFavorites');
  const favoritesActions = document.getElementById('favoritesActions');
  const errorMessage = document.getElementById('errorMessage');
  const totalFavorites = document.getElementById('totalFavorites');
  const totalValue = document.getElementById('totalValue');
  const totalSavings = document.getElementById('totalSavings');
  const favoritesBadge = document.getElementById('favoritesBadge');

  // Função para remover a tela de loading
  function hideLoadingScreen() {
    if (loadingScreen) {
      loadingScreen.style.display = 'none';
    }
  }

  // Função para mostrar mensagem de erro
  function showErrorMessage() {
    hideLoadingScreen();
    if (errorMessage) {
      errorMessage.style.display = 'block';
    }
    if (favoritesGrid) {
      favoritesGrid.style.display = 'none';
    }
    if (emptyFavorites) {
      emptyFavorites.style.display = 'none';
    }
    if (favoritesActions) {
      favoritesActions.style.display = 'none';
    }
  }

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
  hamburgerMenu?.addEventListener('click', toggleSideMenu);
  menuOverlay?.addEventListener('click', closeSideMenu);

  // Fechar menu ao clicar em um link
  const sideMenuLinks = sideMenu?.querySelectorAll('a');
  sideMenuLinks?.forEach(link => {
    link.addEventListener('click', closeSideMenu);
  })
   // Remover loading screen após carregar
    window.addEventListener('load', function() {
      setTimeout(function() {
        document.getElementById('loadingScreen').style.display = 'none';
      }, 500);
    });
  });

   
  </script>
</body>
</html>