
document.addEventListener('DOMContentLoaded', function() {
  // Elementos das abas de login e cadastro
  const loginTab = document.getElementById('login-tab');
  const registerTab = document.getElementById('register-tab');
  const loginForm = document.getElementById('login-form');
  const registerForm = document.getElementById('register-form');
  
  // Links para alternar entre formulários
  const showRegisterLink = document.getElementById('show-register');
  const showLoginLink = document.getElementById('show-login');

  // Elementos do formulário de login
  const loginEmail = document.getElementById('login-email');
  const loginPassword = document.getElementById('login-password');
  const rememberMe = document.getElementById('remember-me');
  
  // Elementos do formulário de cadastro
  const registerName = document.getElementById('register-name');
  const registerEmail = document.getElementById('register-email');
  const registerPassword = document.getElementById('register-password');
  const registerConfirm = document.getElementById('register-confirm');
  const termsCheckbox = document.getElementById('terms');
  
  // Função para alternar entre abas
  function switchTab(activeTab, activeForm, inactiveTab, inactiveForm) {
    // Ativar a aba e o formulário
    activeTab.classList.add('active');
    activeForm.classList.add('active');
    
    // Desativar a outra aba e formulário
    inactiveTab.classList.remove('active');
    inactiveForm.classList.remove('active');
  }
  
  // Adicionar evento de clique na aba de login
  loginTab.addEventListener('click', function() {
    switchTab(loginTab, loginForm, registerTab, registerForm);
  });
  
  // Adicionar evento de clique na aba de cadastro
  registerTab.addEventListener('click', function() {
    switchTab(registerTab, registerForm, loginTab, loginForm);
  });
  
  // Adicionar evento de clique no link "Cadastre-se"
  if (showRegisterLink) {
    showRegisterLink.addEventListener('click', function(e) {
      e.preventDefault();
      switchTab(registerTab, registerForm, loginTab, loginForm);
    });
  }
  
  // Adicionar evento de clique no link "Faça login"
  if (showLoginLink) {
    showLoginLink.addEventListener('click', function(e) {
      e.preventDefault();
      switchTab(loginTab, loginForm, registerTab, registerForm);
    });
  }
  
  // Função para exibir mensagens de alerta
  function showAlert(message, isSuccess) {
    const alertClass = isSuccess ? 'alert-success' : 'alert-danger';
    
    // Criar elemento de alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass}`;
    alertDiv.style.padding = '10px 15px';
    alertDiv.style.marginBottom = '20px';
    alertDiv.style.borderRadius = '4px';
    alertDiv.style.backgroundColor = isSuccess ? '#d4edda' : '#f8d7da';
    alertDiv.style.color = isSuccess ? '#155724' : '#721c24';
    alertDiv.style.border = isSuccess ? '1px solid #c3e6cb' : '1px solid #f5c6cb';
    alertDiv.textContent = message;
    
    // Inserir alerta no formulário apropriado
    const activeForm = document.querySelector('.auth-form.active');
    activeForm.insertBefore(alertDiv, activeForm.firstChild);
    
    // Remover alerta após 5 segundos
    setTimeout(() => {
      alertDiv.remove();
    }, 5000);
  }
  
  // Função para validar o formulário de cadastro
  function validateRegisterForm() {
    if (!registerName.value.trim()) {
      showAlert('Por favor, informe seu nome completo.', false);
      return false;
    }
    
    if (!registerEmail.value.trim()) {
      showAlert('Por favor, informe seu email.', false);
      return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(registerEmail.value.trim())) {
      showAlert('Por favor, informe um email válido.', false);
      return false;
    }
    
    if (!registerPassword.value) {
      showAlert('Por favor, crie uma senha.', false);
      return false;
    }
    
    if (registerPassword.value.length < 8) {
      showAlert('A senha deve ter pelo menos 8 caracteres.', false);
      return false;
    }
    
    if (registerPassword.value !== registerConfirm.value) {
      showAlert('As senhas não coincidem.', false);
      return false;
    }
    
    if (!termsCheckbox.checked) {
      showAlert('Você deve concordar com os Termos de Serviço e Política de Privacidade.', false);
      return false;
    }
    
    return true;
  }
  
  // Função para validar o formulário de login
  function validateLoginForm() {
    if (!loginEmail.value.trim()) {
      showAlert('Por favor, informe seu email.', false);
      return false;
    }
    
    if (!loginPassword.value) {
      showAlert('Por favor, informe sua senha.', false);
      return false;
    }
    
    return true;
  }
  
  // Adicionar evento de envio do formulário de cadastro
  registerForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (validateRegisterForm()) {
      // Preparar dados para envio
      const formData = new FormData();
      formData.append('nome', registerName.value.trim());
      formData.append('email', registerEmail.value.trim());
      formData.append('senha', registerPassword.value);
      formData.append('confirmar_senha', registerConfirm.value);
      
      // Enviar dados para o servidor
      fetch('cadastrar.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status) {
          // Cadastro bem-sucedido
          showAlert(data.message, true);
          
          // Limpar formulário
          registerForm.reset();
          
          // Redirecionar para página de login após 2 segundos
          setTimeout(() => {
            switchTab(loginTab, loginForm, registerTab, registerForm);
          }, 2000);
        } else {
          // Erro no cadastro
          showAlert(data.message, false);
        }
      })
      .catch(error => {
        showAlert('Erro ao processar o cadastro. Tente novamente mais tarde.', false);
        console.error('Erro:', error);
      });
    }
  });
  
  // Adicionar evento de envio do formulário de login
  loginForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (validateLoginForm()) {
      // Preparar dados para envio
      const formData = new FormData();
      formData.append('email', loginEmail.value.trim());
      formData.append('senha', loginPassword.value);
      formData.append('lembrar', rememberMe.checked ? '1' : '0');
      
      // Enviar dados para o servidor
      fetch('login.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status) {
          // Login bem-sucedido
          showAlert(data.message, true);
          
          // Redirecionar para a página principal após 1 segundo
          setTimeout(() => {
            window.location.href = data.redirect || 'index.html';
          }, 1000);
        } else {
          // Erro no login
          showAlert(data.message, false);
        }
      })
      .catch(error => {
        showAlert('Erro ao processar o login. Tente novamente mais tarde.', false);
        console.error('Erro:', error);
      });
    }
  });
  
  // Modal de acesso restrito
  const accessModal = document.getElementById('access-modal');
  const closeModal = document.getElementById('close-modal');
  const modalLogin = document.getElementById('modal-login');
  const modalRegister = document.getElementById('modal-register');
  const restrictedLinks = document.querySelectorAll('.restricted-link');
  
  // Mostrar modal ao clicar em links restritos
  restrictedLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      accessModal.style.display = 'block';
    });
  });
  
  // Fechar modal
  if (closeModal) {
    closeModal.addEventListener('click', function() {
      accessModal.style.display = 'none';
    });
  }
  
  // Clicar fora do modal para fechar
  window.addEventListener('click', function(e) {
    if (e.target === accessModal) {
      accessModal.style.display = 'none';
    }
  });
  
  // Ações dos botões do modal
  if (modalLogin) {
    modalLogin.addEventListener('click', function() {
      accessModal.style.display = 'none';
      switchTab(loginTab, loginForm, registerTab, registerForm);
      scrollToForm();
    });
  }
  
  if (modalRegister) {
    modalRegister.addEventListener('click', function() {
      accessModal.style.display = 'none';
      switchTab(registerTab, registerForm, loginTab, loginForm);
      scrollToForm();
    });
  }
  
  // Função para rolar até o formulário
  function scrollToForm() {
    const authForm = document.querySelector('.auth-forms');
    if (authForm) {
      setTimeout(function() {
        window.scrollTo({
          top: authForm.offsetTop - 50,
          behavior: 'smooth'
        });
      }, 100);
    }
  }
  

  const modalLoginBtn = document.getElementById('modal-login-btn');
  const modalRegisterBtn = document.getElementById('modal-register-btn');
  
  if (modalLoginBtn) {
    modalLoginBtn.addEventListener('click', function() {
      document.getElementById('auth-modal').classList.remove('active');
      switchTab(loginTab, loginForm, registerTab, registerForm);
      scrollToForm();
    });
  }
  
  if (modalRegisterBtn) {
    modalRegisterBtn.addEventListener('click', function() {
      document.getElementById('auth-modal').classList.remove('active');
      switchTab(registerTab, registerForm, loginTab, loginForm);
      scrollToForm();
    });
  }
  

  if (registerPassword) {
    // Adicionar elementos para mostrar a força da senha se não existirem
    if (!document.getElementById('password-strength')) {
      const passwordStrength = document.createElement('div');
      passwordStrength.id = 'password-strength';
      passwordStrength.className = 'password-strength';
      
      const passwordMeter = document.createElement('div');
      passwordMeter.id = 'password-meter';
      passwordMeter.className = 'password-strength-meter';
      
      const passwordText = document.createElement('div');
      passwordText.id = 'password-text';
      passwordText.className = 'password-strength-text';
      passwordText.textContent = 'Força da senha: Digite uma senha';
      
      passwordStrength.appendChild(passwordMeter);
      registerPassword.parentNode.appendChild(passwordStrength);
      registerPassword.parentNode.appendChild(passwordText);
    }
    
  
    registerPassword.addEventListener('input', function() {
      const password = this.value;
      const passwordMeter = document.getElementById('password-meter');
      const passwordText = document.getElementById('password-text');
      
      let strength = 0;
      let feedback = '';
      
    
      if (password.length >= 8) strength += 1;
      if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
      if (password.match(/\d/)) strength += 1;
      if (password.match(/[^a-zA-Z\d]/)) strength += 1;
      
      
      passwordMeter.className = 'password-strength-meter';
      
      if (password.length === 0) {
        passwordMeter.style.width = '0';
        feedback = 'Digite uma senha';
      } else {
        switch (strength) {
          case 0:
          case 1:
            passwordMeter.classList.add('strength-weak');
            feedback = 'Fraca - Adicione letras maiúsculas, números e símbolos';
            break;
          case 2:
            passwordMeter.classList.add('strength-medium');
            feedback = 'Média - Tente adicionar mais tipos de caracteres';
            break;
          case 3:
            passwordMeter.classList.add('strength-good');
            feedback = 'Boa - Sua senha está segura';
            break;
          case 4:
            passwordMeter.classList.add('strength-strong');
            feedback = 'Forte - Excelente escolha de senha!';
            break;
        }
      }
      
      passwordText.textContent = 'Força da senha: ' + feedback;
    });
  }
  
 
  function checkRememberMeToken() {
    const cookieString = document.cookie;
    const cookies = cookieString.split(';').map(cookie => cookie.trim());
    
    const rememberTokenCookie = cookies.find(cookie => cookie.startsWith('remember_token='));
    const rememberUserCookie = cookies.find(cookie => cookie.startsWith('remember_user='));
    
    if (rememberTokenCookie && rememberUserCookie) {
    
      
      const token = rememberTokenCookie.split('=')[1];
      const userId = rememberUserCookie.split('=')[1];
      
      
    }
  }
  
  
});