  document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('cadastro-form');
  const telefoneInput = document.getElementById('telefone');
  const cepInput = document.getElementById('cep');
  const senhaInput = document.getElementById('senha');
  const confirmarSenhaInput = document.getElementById('confirmar-senha');
  const emailInput = document.getElementById('email');
  
  
  function validatePassword() {
    if (senhaInput.value !== confirmarSenhaInput.value) {
      confirmarSenhaInput.setCustomValidity('As senhas não correspondem');
      confirmarSenhaInput.classList.add('is-invalid');
      confirmarSenhaInput.classList.remove('is-valid');
      
      
      let errorMsg = confirmarSenhaInput.parentElement.querySelector('.error-message');
      if (!errorMsg) {
        errorMsg = document.createElement('div');
        errorMsg.className = 'error-message';
        errorMsg.textContent = 'As senhas não correspondem';
        confirmarSenhaInput.parentElement.appendChild(errorMsg);
      }
      return false;
    } else {
      confirmarSenhaInput.setCustomValidity('');
      confirmarSenhaInput.classList.remove('is-invalid');
      confirmarSenhaInput.classList.add('is-valid');
      
      
      const errorMsg = confirmarSenhaInput.parentElement.querySelector('.error-message');
      if (errorMsg) {
        errorMsg.remove();
      }
      return true;
    }
  }
  
  
  function validatePhone() {
    const phoneValue = telefoneInput.value.replace(/\D/g, '');
    
    if (phoneValue.length > 11) {
      telefoneInput.setCustomValidity('O telefone deve ter no máximo 11 números');
      telefoneInput.classList.add('is-invalid');
      telefoneInput.classList.remove('is-valid');
      
    
      let errorMsg = telefoneInput.parentElement.querySelector('.error-message');
      if (!errorMsg) {
        errorMsg = document.createElement('div');
        errorMsg.className = 'error-message';
        errorMsg.textContent = 'O telefone deve ter no máximo 11 números';
        telefoneInput.parentElement.appendChild(errorMsg);
      }
      return false;
    } else if (phoneValue.length < 10) {
      telefoneInput.setCustomValidity('O telefone deve ter pelo menos 10 números');
      telefoneInput.classList.add('is-invalid');
      telefoneInput.classList.remove('is-valid');
      
      
      let errorMsg = telefoneInput.parentElement.querySelector('.error-message');
      if (!errorMsg) {
        errorMsg = document.createElement('div');
        errorMsg.className = 'error-message';
        errorMsg.textContent = 'O telefone deve ter pelo menos 10 números';
        telefoneInput.parentElement.appendChild(errorMsg);
      }
      return false;
    } else {
      telefoneInput.setCustomValidity('');
      telefoneInput.classList.remove('is-invalid');
      telefoneInput.classList.add('is-valid');
      
      
      const errorMsg = telefoneInput.parentElement.querySelector('.error-message');
      if (errorMsg) {
        errorMsg.remove();
      }
      return true;
    }
  }
  

  if (telefoneInput) {
    telefoneInput.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      
      
      if (value.length > 11) {
        value = value.slice(0, 11);
      }
      
      
      if (value.length <= 10) {
        
        e.target.value = value.replace(/^(\d{2})(\d{4})(\d{4}).*/, function(match, p1, p2, p3) {
          if (p3) return `(${p1}) ${p2}-${p3}`;
          if (p2) return `(${p1}) ${p2}`;
          if (p1) return `(${p1}`;
          return value;
        });
      } else {
        
        e.target.value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, function(match, p1, p2, p3) {
          if (p3) return `(${p1}) ${p2}-${p3}`;
          if (p2) return `(${p1}) ${p2}`;
          if (p1) return `(${p1}`;
          return value;
        });
      }
      
      validatePhone();
    });
    
    telefoneInput.addEventListener('blur', validatePhone);
  }
  
 
  if (cepInput) {
    cepInput.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 8) value = value.slice(0, 8);
      
      if (value.length > 5) {
        value = value.substring(0, 5) + '-' + value.substring(5);
      }
      
      e.target.value = value;
    });
  }
  
  if (senhaInput && confirmarSenhaInput) {
    senhaInput.addEventListener('input', validatePassword);
    confirmarSenhaInput.addEventListener('input', validatePassword);
    senhaInput.addEventListener('blur', validatePassword);
    confirmarSenhaInput.addEventListener('blur', validatePassword);
  }
  

  if (emailInput) {
    emailInput.addEventListener('input', function(e) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(e.target.value) && e.target.value.length > 0) {
        emailInput.setCustomValidity('Por favor, insira um email válido');
        emailInput.classList.add('is-invalid');
        emailInput.classList.remove('is-valid');
        
       
        let errorMsg = emailInput.parentElement.querySelector('.error-message');
        if (!errorMsg) {
          errorMsg = document.createElement('div');
          errorMsg.className = 'error-message';
          errorMsg.textContent = 'Por favor, insira um email válido';
          emailInput.parentElement.appendChild(errorMsg);
        }
      } else {
        emailInput.setCustomValidity('');
        emailInput.classList.remove('is-invalid');
        if (e.target.value.length > 0) {
          emailInput.classList.add('is-valid');
        }
        
       
        const errorMsg = emailInput.parentElement.querySelector('.error-message');
        if (errorMsg) {
          errorMsg.remove();
        }
      }
    });
  }
  
  
  if (form) {
    form.addEventListener('submit', function(e) {
      let hasError = false;
      
    
      if (!validatePassword()) {
        hasError = true;
      }
      
    
      if (!validatePhone()) {
        hasError = true;
      }
      
      
      const allInputs = form.querySelectorAll('input[required], select[required]');
      allInputs.forEach(input => {
        if (input.value.trim() === '') {
          input.classList.add('is-invalid');
          input.classList.remove('is-valid');
          hasError = true;
          
         
          let errorMsg = input.parentElement.querySelector('.error-message');
          if (!errorMsg) {
            errorMsg = document.createElement('div');
            errorMsg.className = 'error-message';
            errorMsg.textContent = 'Este campo é obrigatório';
            input.parentElement.appendChild(errorMsg);
          }
        } else {
          input.classList.remove('is-invalid');
          input.classList.add('is-valid');
          
        
          const errorMsg = input.parentElement.querySelector('.error-message');
          if (errorMsg) {
            errorMsg.remove();
          }
        }
      });
      
      if (hasError) {
        e.preventDefault();
        alert('Por favor, corrija os erros no formulário antes de enviá-lo.');
      } else {
        
      }
    });
  }
  
 
  const style = document.createElement('style');
  style.textContent = `
    .form-control.is-invalid {
      border-color: #dc3545;
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .form-control.is-valid {
      border-color: #28a745;
      box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .error-message {
      color: #dc3545;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
  `;
  document.head.appendChild(style);
});