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

  // ==========================================
  // CLASSE PARA GERENCIAR FAVORITOS (movida para o topo)
  // ==========================================

  class FavoritosManager {
    constructor(apiUrl = 'backend/favoritos_api.php') {
        this.apiUrl = apiUrl;
    }
    
    // Método privado para fazer requisições
    async _request(acao, dados = {}) {
      try {
        const payload = { acao, ...dados };
    
        const response = await fetch(this.apiUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(payload)
        });
    
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
    
        const result = await response.json();
        return result;
    
      } catch (error) {
        console.error('Erro na requisição:', error);
        return {
          sucesso: false,
          mensagem: 'Erro de conexão. Tente novamente mais tarde.',
          erro: error.message
        };
      }
    }
    
    // Listar favoritos
    async listar(limite = null, offset = 0) {
        return await this._request('listar', { limite, offset });
    }

    // Contar favoritos
    async contar(userId) {
        return await this._request('contar', { usuario_id: userId });
    }

    // Remover produto dos favoritos
    async remover(produtoId) {
        return await this._request('remover', { produto_id: produtoId });
    }

    // Limpar todos os favoritos
    async limpar() {
        return await this._request('limpar');
    }
  }

  // Instanciar o gerenciador globalmente
  window.favoritos = new FavoritosManager();

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

  // Função para mostrar favoritos vazios
  function showEmptyFavorites() {
    hideLoadingScreen();
    if (emptyFavorites) {
      emptyFavorites.style.display = 'block';
    }
    if (favoritesGrid) {
      favoritesGrid.style.display = 'none';
    }
    if (favoritesActions) {
      favoritesActions.style.display = 'none';
    }
    // Atualizar contadores
    updateFavoritesCounters(0, 0, 0);
  }

  // Função para mostrar lista de favoritos
  function showFavoritesList() {
    hideLoadingScreen();
    if (favoritesGrid) {
      favoritesGrid.style.display = 'grid';
    }
    if (favoritesActions) {
      favoritesActions.style.display = 'block';
    }
    if (emptyFavorites) {
      emptyFavorites.style.display = 'none';
    }
  }

  // Função para atualizar contadores
  function updateFavoritesCounters(total, valorTotal, economia) {
    if (totalFavorites) {
      totalFavorites.textContent = total;
    }
    if (totalValue) {
      totalValue.textContent = `R$ ${valorTotal.toFixed(2).replace('.', ',')}`;
    }
    if (totalSavings) {
      totalSavings.textContent = `R$ ${economia.toFixed(2).replace('.', ',')}`;
    }
    if (favoritesBadge) {
      if (total > 0) {
        favoritesBadge.textContent = total;
        favoritesBadge.style.display = 'inline';
      } else {
        favoritesBadge.style.display = 'none';
      }
    }
  }

  // Função para abrir/fechar menu lateral
  function toggleSideMenu() {
    if (hamburgerMenu && sideMenu && menuOverlay) {
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
  }

  // Função para fechar menu lateral
  function closeSideMenu() {
    if (hamburgerMenu && sideMenu && menuOverlay) {
      hamburgerMenu.classList.remove('active');
      sideMenu.classList.remove('active');
      menuOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  // Event listeners para menu hambúrguer
  if (hamburgerMenu) {
    hamburgerMenu.addEventListener('click', toggleSideMenu);
  }
  if (menuOverlay) {
    menuOverlay.addEventListener('click', closeSideMenu);
  }

  // Fechar menu ao clicar em um link
  if (sideMenu) {
    const sideMenuLinks = sideMenu.querySelectorAll('a');
    sideMenuLinks.forEach(link => {
      link.addEventListener('click', closeSideMenu);
    });
  }
  
  // Função para verificar login do usuário
  function checkUserLogin() {
    try {
      const userDataStr = localStorage.getItem('userData');

      if (!userDataStr) {
        hideLoadingScreen();
        alert('Você precisa estar logado para acessar esta página.');
        window.location.href = 'login.php';
        return false;
      }

      let userData;
      try {
        userData = JSON.parse(userDataStr);
      } catch (e) {
        console.error('Erro ao ler dados do usuário:', e);
        localStorage.removeItem('userData');
        hideLoadingScreen();
        alert('Sessão inválida. Faça login novamente.');
        window.location.href = 'login.php';
        return false;
      }

      // Se chegou até aqui, o usuário está logado corretamente
      showUserProfile(userData);
      loadFavoritesFromDatabase(userData.id);
      return true;

    } catch (error) {
      console.error('Erro na verificação de login:', error);
      hideLoadingScreen();
      alert('Erro interno. Tente fazer login novamente.');
      window.location.href = 'login.php';
      return false;
    }
  }

  // Função para mostrar perfil do usuário
  function showUserProfile(userData) {
    if (userProfileIcon) {
      userProfileIcon.style.display = 'block';

      // Criar iniciais do nome
      const nameParts = userData.nome.trim().split(' ');
      if (userInitials) {
        userInitials.textContent = nameParts.length > 1
          ? nameParts[0][0] + nameParts[nameParts.length - 1][0]
          : userData.nome[0];
      }

      // Preencher dados do dropdown
      if (userName) userName.textContent = userData.nome || 'Usuário';
      if (userEmail) userEmail.textContent = userData.email || 'email@exemplo.com';

      if (userData.cidade && userData.estado && userLocation) {
        userLocation.textContent = `${userData.cidade}, ${userData.estado}`;
      }
    }
  }

  // Carregar favoritos do banco
  async function loadFavoritesFromDatabase(userId) {
    try {
      console.log('Carregando favoritos para usuário:', userId);
      
      const resultado = await window.favoritos.listar();
      
      // Adicionando logs para debug
      console.log('Resposta da API de favoritos:', resultado);
      if (resultado.favoritos) {
        console.log('Favoritos recebidos:', resultado.favoritos);
      }
      
      if (resultado.sucesso) {
        console.log('Favoritos carregados:', resultado);
        
        if (resultado.favoritos && resultado.favoritos.length > 0) {
          // Tem favoritos - mostrar lista
          renderFavoritesList(resultado.favoritos);
          showFavoritesList();
          
          // Calcular valores
          let valorTotal = 0;
          let economia = 0;
          resultado.favoritos.forEach(produto => {
            valorTotal += parseFloat(produto.preco) || 0;
            if (produto.preco_original) {
              economia += (parseFloat(produto.preco_original) - parseFloat(produto.preco)) || 0;
            }
          });
          
          updateFavoritesCounters(resultado.favoritos.length, valorTotal, economia);
        } else {
          // Não tem favoritos
          showEmptyFavorites();
        }
      } else {
        console.error('Erro ao carregar favoritos:', resultado.mensagem);
        showErrorMessage();
      }
    } catch (error) {
      console.error('Erro ao carregar favoritos:', error);
      showErrorMessage();
    }
  }

  // Função para renderizar lista de favoritos
  function renderFavoritesList(favoritos) {
    if (!favoritesGrid) return;

    favoritesGrid.innerHTML = '';

    favoritos.forEach(produto => {
      const produtoElement = document.createElement('div');
      produtoElement.className = 'favorite-item';
      produtoElement.dataset.produtoId = produto.id;
      produtoElement.dataset.categoria = produto.categoria_id || '';
      
      const precoOriginal = produto.preco_original ? parseFloat(produto.preco_original) : 0;
      const precoAtual = parseFloat(produto.preco) || 0;
      const temDesconto = precoOriginal > precoAtual;
      
      produtoElement.innerHTML = `
        <div class="product-card">
          <div class="product-image">
            <img src="${produto.imagem || 'images/placeholder.jpg'}" alt="${produto.nome}">
            <div class="product-actions">
              <button class="btn-remove-favorite" onclick="removerFavorito(${produto.id})" title="Remover dos favoritos">
                <i class="mdi mdi-heart"></i>
              </button>
            </div>
            ${temDesconto ? '<div class="product-badge sale">Promoção</div>' : ''}
          </div>
          <div class="product-info">
            <h3 class="product-title">${produto.nome}</h3>
            <p class="product-description">${produto.descricao || ''}</p>
            <div class="product-price">
              ${temDesconto ? `<span class="price-original">R$ ${precoOriginal.toFixed(2).replace('.', ',')}</span>` : ''}
              <span class="price-current">R$ ${precoAtual.toFixed(2).replace('.', ',')}</span>
            </div>
            <button class="btn-add-cart" onclick="adicionarAoCarrinho(${produto.id})">
              <i class="mdi mdi-cart-plus"></i> Adicionar ao Carrinho
            </button>
          </div>
        </div>
      `;
      
      favoritesGrid.appendChild(produtoElement);
    });
  }

  // Funções globais para uso nos botões
  window.removerFavorito = async function(produtoId) {
    try {
      const resultado = await window.favoritos.remover(produtoId);
      
      if (resultado.sucesso) {
        // Recarregar a página de favoritos
        const userData = JSON.parse(localStorage.getItem('userData'));
        if (userData && userData.id) {
          await loadFavoritesFromDatabase(userData.id);
        }
        
        // Mostrar notificação
        alert(resultado.mensagem);
      } else {
        alert('Erro: ' + resultado.mensagem);
      }
    } catch (error) {
      console.error('Erro ao remover favorito:', error);
      alert('Erro interno. Tente novamente.');
    }
  };

  window.adicionarAoCarrinho = function(produtoId) {
    // Implementar lógica do carrinho aqui
    alert(`Produto ${produtoId} adicionado ao carrinho!`);
  };

  // Event listeners para filtros
  const filterButtons = document.querySelectorAll('.filter-btn');
  filterButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      // Remover active de todos
      filterButtons.forEach(b => b.classList.remove('active'));
      // Adicionar active no clicado
      this.classList.add('active');
      
      const filter = this.dataset.filter;
      filterFavorites(filter);
    });
  });

  // Função de filtro
  function filterFavorites(filter) {
    if (!favoritesGrid) return;
    
    const items = favoritesGrid.querySelectorAll('.favorite-item');

    items.forEach(item => {
      const categoria = item.dataset.categoria;
      const temPromocao = item.querySelector('.product-badge.sale');

      if (filter === 'todos') {
        item.style.display = 'block';
      } else if (filter === 'promocao') {
        item.style.display = temPromocao ? 'block' : 'none';
      } else {
        item.style.display = categoria === filter ? 'block' : 'none';
      }
    });
  }

  // Event listeners para ações em massa
  const clearAllFavoritesBtn = document.getElementById('clearAllFavorites');
  if (clearAllFavoritesBtn) {
    clearAllFavoritesBtn.addEventListener('click', async function() {
      if (confirm('Tem certeza que deseja remover todos os favoritos?')) {
        try {
          const resultado = await window.favoritos.limpar();
          
          if (resultado.sucesso) {
            showEmptyFavorites();
            alert(resultado.mensagem);
          } else {
            alert('Erro: ' + resultado.mensagem);
          }
        } catch (error) {
          console.error('Erro ao limpar favoritos:', error);
          alert('Erro interno. Tente novamente.');
        }
      }
    });
  }

  // Inicialização da página
  checkUserLogin();

});