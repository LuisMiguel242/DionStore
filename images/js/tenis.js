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
    // (Removido para não conflitar com a função inline do tenis.php)
        
    
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