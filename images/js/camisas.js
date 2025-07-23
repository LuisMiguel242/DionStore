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
  
    // Função para exibir perfil do usuário
    function showUserProfile(userData) {
      if (!userData || !userInitials || !userName || !userEmail || !userLocation) return;
      
      const nameParts = userData.nome.trim().split(' ');
      userInitials.textContent = nameParts.length > 1
        ? nameParts[0][0] + nameParts[nameParts.length - 1][0]
        : userData.nome[0];
      userName.textContent = userData.nome;
      userEmail.textContent = userData.email;
      userLocation.textContent = `${userData.cidade}, ${userData.estado}`;
    }
  
    // Função para verificar login do usuário
    function checkUserLogin() {
      // Primeiro tenta buscar dados do localStorage
      const userData = JSON.parse(localStorage.getItem('userData')) || {
        nome: 'João Silva',
        email: 'joao@exemplo.com',
        cidade: 'Sarutaia',
        estado: 'SP'
      };
  
      if (userData) {
        showUserProfile(userData);
      } else if (userProfileIcon) {
        userProfileIcon.style.display = 'none';
      }
    }
  
    // Função para alternar dropdown
    function toggleDropdown() {
      if (userDropdown) {
        userDropdown.classList.toggle('active');
      }
    }
  
    // Função de logout
    function logout() {
      localStorage.removeItem('userData');
      window.location.href = 'login.php';
    }
  
    // Event listeners para dropdown do usuário
    if (userProfileIcon) {
      userProfileIcon.addEventListener('click', function (e) {
        e.stopPropagation();
        toggleDropdown();
      });
    }
  
    // Fechar dropdown ao clicar fora
    document.addEventListener('click', function (e) {
      if (userProfileIcon && userDropdown && !userProfileIcon.contains(e.target)) {
        userDropdown.classList.remove('active');
      }
    });
  
    // Event listener para botão de logout
    if (logoutBtn) {
      logoutBtn.addEventListener('click', logout);
    }
  
    // --- BOTÕES DE FAVORITO ---
    document.addEventListener('click', async (e) => {
      const favoriteBtn = e.target.closest('.btn-favorite, .favorite-btn');
      if (!favoriteBtn) return;
    
      e.preventDefault();
    
      const card = favoriteBtn.closest('.product-card');
      if (!card) {
        alert('Produto não encontrado!');
        return;
      }
    
      const userData = JSON.parse(localStorage.getItem('userData'));
      if (!userData || !userData.id) {
        alert('Você precisa estar logado para favoritar produtos.');
        return;
      }
    
      const productId = card.getAttribute('data-id');
      const productName = card.querySelector('.product-title')?.textContent || '';
      const productPriceText = card.querySelector('.product-price')?.textContent || '0';
      const productPrice = parseFloat(productPriceText.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
      const productImage = card.querySelector('img')?.src || '';
      const productCategory = card.parentElement.getAttribute('data-category') || '';
      const productDescription = card.querySelector('.product-description')?.textContent || '';
    
      const payload = {
        usuario_id: userData.id,
        produto_id: productId,
        produto_nome: productName,
        produto_preco: productPrice,
        produto_imagem: productImage,
        produto_categoria: productCategory,
        produto_descricao: productDescription
      };
    
      try {
        const response = await fetch('backend/favoritos_api.php?action=adicionar', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
    
        const data = await response.json();
    
        if (data.success) {
          // alert('Produto adicionado aos favoritos!');
        } else {
          // alert(data.error || 'Erro ao adicionar aos favoritos.');
        }
      } catch (e) {
        // alert('Erro de conexão com o servidor.');
      }
    });
        
   
    // --- BOTÕES DE CARRINHO ---
    document.querySelectorAll('.btn-add-cart').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const card = btn.closest('.product-card');
        const productId = card ? card.getAttribute('data-id') : null;
        if (productId) {
          alert('Produto ' + productId + ' adicionado ao carrinho!');
        } else {
          alert('Produto não encontrado!');
        }
      });
    });
  
    // Inicializar a aplicação
    checkUserLogin();
  });