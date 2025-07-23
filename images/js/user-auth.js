// Função para verificar o estado de login do usuário
function checkLoginStatus() {
  const userData = JSON.parse(localStorage.getItem('userData'));
  return !!userData; // Retorna true se o usuário estiver logado, false caso contrário
}

// Função para redirecionar com base no status de login
function redirectBasedOnLogin(targetPage, fallbackPage, message) {
  if (checkLoginStatus()) {
    window.location.href = targetPage;
  } else {
    if (message) {
      alert(message);
    }
    window.location.href = fallbackPage;
  }
}

// Configurar eventos assim que o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
  // Configurar o comportamento do link "Meus Dados"
  const myDataLinks = document.querySelectorAll('[data-requires-login="true"]');
  
  myDataLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const targetPage = this.getAttribute('href');
      const fallbackPage = this.getAttribute('data-login-page') || 'login.html';
      const message = this.getAttribute('data-login-message') || 'Você precisa estar logado para acessar esta página.';
      
      redirectBasedOnLogin(targetPage, fallbackPage, message);
    });
  });
  
  // Configurar a exibição ou ocultação do ícone de perfil do usuário
  const userProfileIcon = document.getElementById('userProfileIcon');
  if (userProfileIcon) {
    userProfileIcon.style.display = checkLoginStatus() ? 'block' : 'none';
  }
  
  // Configurar o dropdown do perfil de usuário
  if (userProfileIcon) {
    const userDropdown = document.getElementById('userDropdown');
    
    // Toggles the dropdown when user icon is clicked
    userProfileIcon.addEventListener('click', function(e) {
      e.stopPropagation();
      userDropdown.classList.toggle('active');
    });
    
    // Closes dropdown when clicking elsewhere
    document.addEventListener('click', function(event) {
      if (userDropdown.classList.contains('active') && !userProfileIcon.contains(event.target)) {
        userDropdown.classList.remove('active');
      }
    });
    
    // Preencher dados do usuário no dropdown
    if (checkLoginStatus()) {
      const userData = JSON.parse(localStorage.getItem('userData'));
      const userInitials = document.getElementById('userInitials');
      const userName = document.getElementById('userName');
      const userEmail = document.getElementById('userEmail');
      const userLocation = document.getElementById('userLocation');
      
      if (userInitials && userData.nome) {
        const nameParts = userData.nome.trim().split(' ');
        userInitials.textContent = nameParts.length > 1
          ? nameParts[0][0] + nameParts[nameParts.length - 1][0]
          : userData.nome[0];
      }
      
      if (userName) userName.textContent = userData.nome || 'Usuário';
      if (userEmail) userEmail.textContent = userData.email || 'email@exemplo.com';
      if (userLocation) {
        userLocation.textContent = userData.cidade && userData.estado
          ? `${userData.cidade}, ${userData.estado}`
          : 'Cidade, Estado';
      }
    }
    
    // Configurar botão de logout
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', function() {
        localStorage.removeItem('userData');
        window.location.href = 'login.html';
      });
    }
  }
});