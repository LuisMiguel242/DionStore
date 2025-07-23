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

// Script para funcionalidade das categorias
document.addEventListener('DOMContentLoaded', function() {
  const categoryBoxes = document.querySelectorAll('.category-box');
  const shopTopBarTitle = document.querySelector('.shop-top-bar h3');
  
  // Dados dos produtos por categoria
  const productsByCategory = {
    camisas: {
      title: 'Camisas',
      products: [
        {
          name: 'Camisa Social Azul Royal',
          price: 'R$ 159,90',
          oldPrice: 'R$ 199,90',
          badge: '-20%',
          rating: 4.5,
          reviews: 42,
          colors: ['#1e3799', '#ffffff', '#2c3e50'],
          sizes: ['P', 'M', 'G', 'GG']
        },
        {
          name: 'Camisa Social Branca Premium',
          price: 'R$ 179,90',
          rating: 5,
          reviews: 56,
          colors: ['#ffffff', '#f8f9fa'],
          sizes: ['P', 'M', 'G', 'GG']
        },
        {
          name: 'Camisa Polo Azul Marinho',
          price: 'R$ 89,90',
          rating: 4,
          reviews: 28,
          colors: ['#2c3e50', '#34495e'],
          sizes: ['P', 'M', 'G', 'GG']
        }
      ]
    },
    tenis: {
      title: 'Tênis',
      products: [
        {
          name: 'Tênis Esportivo Nike Air',
          price: 'R$ 299,90',
          oldPrice: 'R$ 399,90',
          badge: '-25%',
          rating: 4.8,
          reviews: 89,
          colors: ['#000000', '#ffffff', '#ff0000'],
          sizes: ['38', '39', '40', '41', '42', '43']
        },
        {
          name: 'Tênis Casual Adidas',
          price: 'R$ 249,90',
          rating: 4.5,
          reviews: 67,
          colors: ['#ffffff', '#000000', '#1e88e5'],
          sizes: ['38', '39', '40', '41', '42', '43']
        },
        {
          name: 'Tênis Running Puma',
          price: 'R$ 199,90',
          badge: 'Novo',
          rating: 4.3,
          reviews: 34,
          colors: ['#4caf50', '#000000', '#ffc107'],
          sizes: ['38', '39', '40', '41', '42', '43']
        }
      ]
    },
    blusas: {
      title: 'Blusas',
      products: [
        {
          name: 'Blusa Moletom com Capuz',
          price: 'R$ 129,90',
          rating: 4.6,
          reviews: 45,
          colors: ['#2c3e50', '#e74c3c', '#27ae60'],
          sizes: ['P', 'M', 'G', 'GG']
        },
        {
          name: 'Blusa de Frio Tricot',
          price: 'R$ 89,90',
          oldPrice: 'R$ 119,90',
          badge: '-25%',
          rating: 4.2,
          reviews: 23,
          colors: ['#8e44ad', '#34495e', '#e67e22'],
          sizes: ['P', 'M', 'G', 'GG']
        },
        {
          name: 'Blusa Básica Algodão',
          price: 'R$ 59,90',
          rating: 4.4,
          reviews: 78,
          colors: ['#ffffff', '#000000', '#95a5a6'],
          sizes: ['P', 'M', 'G', 'GG']
        }
      ]
    }
  };

  // Função para alternar categoria ativa
  function setActiveCategory(clickedBox) {
    categoryBoxes.forEach(box => box.classList.remove('active'));
    clickedBox.classList.add('active');
  }

  // Função para atualizar o título da seção
  function updateSectionTitle(category) {
    if (shopTopBarTitle) {
      shopTopBarTitle.textContent = productsByCategory[category].title;
    }
  }

  // Função para gerar HTML de um produto
  function generateProductHTML(product) {
    const badgeHTML = product.badge ? `<span class="product-badge">${product.badge}</span>` : '';
    const oldPriceHTML = product.oldPrice ? `<span class="old-price">${product.oldPrice}</span>` : '';
    
    const starsHTML = Array.from({length: 5}, (_, i) => {
      const rating = product.rating;
      if (i < Math.floor(rating)) {
        return '<span class="mdi mdi-star"></span>';
      } else if (i < Math.ceil(rating) && rating % 1 !== 0) {
        return '<span class="mdi mdi-star-half"></span>';
      } else {
        return '<span class="mdi mdi-star-outline"></span>';
      }
    }).join('');

    const colorsHTML = product.colors.map(color => 
      `<span class="product-color" style="background-color: ${color};"></span>`
    ).join('');

    const sizesHTML = product.sizes.map((size, index) => 
      `<span class="size-box ${index === 1 ? 'active' : ''}">${size}</span>`
    ).join('');

    return `
      <div class="col-sm-6 col-md-4 col-lg-3">
        <div class="product-card">
          <div class="product-img-wrap">
            ${badgeHTML}
            <img src="/api/placeholder/400/300" alt="${product.name}">
            <div class="product-overlay">
              <a href="carrinho.html" class="btn btn-primary">Comprar Agora</a>
            </div>
          </div>
          <div class="product-body">
            <h5 class="product-title">${product.name}</h5>
            <div class="product-price">
              ${oldPriceHTML}
              <span>${product.price}</span>
            </div>
            <div class="product-rating">
              ${starsHTML}
              <span class="rating-count">(${product.reviews})</span>
            </div>
            <div class="product-colors mt-2">
              ${colorsHTML}
            </div>
            <div class="product-sizes mt-3">
              ${sizesHTML}
            </div>
            <div class="mt-4">
              <a href="carrinho.html" class="button button-primary button-winona add-to-cart">Adicionar ao Carrinho</a>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  // Função para atualizar produtos na tela
  function updateProducts(category) {
    const productsContainer = document.querySelector('.row-30');
    if (!productsContainer) return;

    const products = productsByCategory[category].products;
    const productsHTML = products.map(product => generateProductHTML(product)).join('');
    
    productsContainer.innerHTML = productsHTML;
  }

  // Event listeners para as categorias
  categoryBoxes.forEach(box => {
    box.addEventListener('click', function() {
      const category = this.getAttribute('data-category');
      
      // Atualizar categoria ativa
      setActiveCategory(this);
      
      // Atualizar título da seção
      updateSectionTitle(category);
      
      // Atualizar produtos
      updateProducts(category);
      
      // Scroll suave para a seção de produtos
      const catalogSection = document.getElementById('catalog');
      if (catalogSection) {
        catalogSection.scrollIntoView({ 
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  // Inicializar com a categoria ativa (camisas)
  const activeCategory = document.querySelector('.category-box.active');
  if (activeCategory) {
    const category = activeCategory.getAttribute('data-category') || 'camisas';
    updateProducts(category);
  }
});
