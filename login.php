<?php
session_start();
require_once 'backend/login.php';
?>
<!DOCTYPE html>
<html class="wide wow-animation" lang="pt-br">
  <head>
    <title>Login - DION</title>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <link rel="icon" href="images/dion definitivo branco.png" type="image/dion definitivo branco.png">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Work+Sans:300,400,500,700,800%7CPoppins:300,400,700">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/style.css" id="main-styles-link">
    <link rel="stylesheet" href="css/login.css" id="main-styles-link">
  </head>
  <body>
    <div class="preloader">
      <div class="preloader-logo"><img src="images/dion definitivo branco.png" alt="" width="151" height="44" srcset="images/dion definitivo branco.png 2x"/>
      </div>
      <div class="preloader-body">
        <div id="loadingProgressG">
          <div class="loadingProgressG" id="loadingProgressG_1"></div>
        </div>
      </div>
    </div>
    <div class="page">
      <header class="section novi-background page-header">
        <!-- RD Navbar-->
        <div class="rd-navbar-wrap">
          <div class="rd-navbar-aside-outer">
            <div class="rd-navbar-aside">
              <!-- RD Navbar Panel-->
              <div class="rd-navbar-panel">
                <!-- RD Navbar Toggle-->
                <button class="rd-navbar-toggle" data-rd-navbar-toggle="#rd-navbar-nav-wrap-1"><span></span></button>
              </div>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Seção de Login -->
      <section class="auth-wrapper section novi-background section-sm">
        <div class="container">
          <div class="auth-container">
            <!-- Imagem lateral decorativa -->
            <div class="auth-image">
              <div class="auth-image-content">
                <img src="images/" alt="Imagem decorativa" class="auth-decorative-image" style="max-width: 100%; height: auto; margin-bottom: 105px;">
                <h2>Bem-vindo à DION</h2>
                <p>Entre para uma comunidade de skatistas e tenha acesso a produtos exclusivos.</p>
                <div class="wow-outer button-outer">
                  <a class="button button-md button-white-outline button-winona restricted-link" href="#">Saiba mais</a>
                </div>
              </div>
            </div>
            
            <!-- Formulário de Login -->
            <div class="auth-forms">
              <div class="auth-header">
                <h3>Acesse sua conta</h3>
                <p>Entre para continuar</p>
              </div>
              
              <!-- Exibindo mensagens de erro -->
              <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                  <?php echo $error; ?>
                </div>
              <?php endif; ?>
              
              <!-- Formulário de Login -->
              <form class="auth-form active" id="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                  <label for="login-email">Email</label>
                  <input class="form-control" id="login-email" name="email" type="email" placeholder="Seu email" required value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="form-group">
                  <label for="login-password">Senha</label>
                  <input class="form-control" id="login-password" name="senha" type="password" placeholder="Sua senha" required>
                </div>
                
                <div class="checkbox-container">
                  <input type="checkbox" id="remember-me" name="remember">
                  <label for="remember-me">Lembrar-me</label>
                </div>
                
                <a href="recuperar-senha.php" class="forgot-password">Esqueceu a senha?</a>
                
                <button type="submit" class="btn-auth" name="submit">Entrar</button>
                
                <div class="or-divider">ou continue com</div>
                
                <div class="social-login">
                  <a href="#" class="social-btn btn-facebook">
                    <i class="mdi mdi-facebook"></i> Facebook
                  </a>
                  <a href="#" class="social-btn btn-google">
                    <i class="mdi mdi-google"></i> Google
                  </a>
                </div>
                
                <div class="auth-footer">
                  Não tem uma conta? <a href="cadastro.php">Cadastre-se</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>
      
      <!-- Footer Section -->
      <footer class="section footer-classic context-dark bg-image" style="background: #2c3e50;">
        <div class="container">
          <div class="row row-30">
            <div class="col-md-4 col-xl-5">
              <div class="pr-xl-4">
                <a class="brand" href="index.html"><img class="brand-logo-light" src="images/dion definitivo branco.png" alt="" width="140" height="37"></a>
                <p>Somos uma loja especializada em produtos para skatistas, oferecendo as melhores marcas e equipamentos de qualidade.</p>
                <p class="rights"><span>©  </span><span class="copyright-year">2025</span><span> </span><span>DION</span><span>. </span><span>Todos os direitos reservados.</span></p>
              </div>
            </div>
            <div class="col-md-4">
              <h5>Contatos</h5>
              <dl class="contact-list">
                <dt>Endereço:</dt>
                <dd>Rua Miguel Galleigo Pedroelo, Sarutaia, SP 18840-302</dd>
              </dl>
              <dl class="contact-list">
                <dt>Email:</dt>
                <dd><a href="mailto:#">contato@dionskateshop.com</a></dd>
              </dl>
              <dl class="contact-list">
                <dt>Telefone:</dt>
                <dd><a href="tel:#">(14) 998364178</a>
                </dd>
              </dl>
            </div>
            <div class="col-md-4 col-xl-3">
              <h5>Links</h5>
              <ul class="nav-list">
                <li><a href="#" class="restricted-link">Sobre nós</a></li>
                <li><a href="#" class="restricted-link">Produtos</a></li>
                <li><a href="#" class="restricted-link">Blog</a></li>
                <li><a href="#" class="restricted-link">Contato</a></li>
                <li><a href="#" class="restricted-link">FAQ</a></li>
              </ul>
            </div>
          </div>
        </div>
        <div class="snackbars" id="form-output-global"></div>
      </footer>
    </div>
    
    <script src="js/core.min.js"></script>
    <script src="js/script.js"></script>
    
    <!-- Modal de acesso restrito -->
    <div id="access-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); z-index: 10000;">
      <div style="position: relative; background: white; width: 90%; max-width: 400px; margin: 100px auto; padding: 20px; border-radius: 5px; text-align: center;">
        <span id="close-modal" style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer;">&times;</span>
        <h4 style="margin-bottom: 15px;">Acesso Restrito</h4>
        <p style="margin-bottom: 20px;">Você precisa estar logado para acessar esta página.</p>
        <div>
          <button id="modal-login" style="background: #333; color: white; border: none; padding: 10px 20px; margin-right: 10px; cursor: pointer; border-radius: 4px;">Entrar</button>
          <button id="modal-register" style="background: #f1f1f1; color: #333; border: 1px solid #ddd; padding: 10px 20px; cursor: pointer; border-radius: 4px;">Cadastrar</button>
        </div>
      </div>
    </div>
    
    <script>
      document.addEventListener('DOMContentLoaded', function() {
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
            window.location.href = 'login.php';
          });
        }
        
        if (modalRegister) {
          modalRegister.addEventListener('click', function() {
            accessModal.style.display = 'none';
            window.location.href = 'cadastro.php';
          });
        }
        
        // Lógica para botões no modal principal
        const modalLoginBtn = document.getElementById('modal-login-btn');
        const modalRegisterBtn = document.getElementById('modal-register-btn');
        
        if (modalLoginBtn) {
          modalLoginBtn.addEventListener('click', function() {
            document.getElementById('auth-modal').classList.remove('active');
            window.location.href = 'login.php';
          });
        }
        
        if (modalRegisterBtn) {
          modalRegisterBtn.addEventListener('click', function() {
            document.getElementById('auth-modal').classList.remove('active');
            window.location.href = 'cadastro.php';
          });
        }
      });
    
// Verificar se o login foi bem-sucedido e redirecionar
      <?php if ($login_success): ?>
      document.addEventListener('DOMContentLoaded', () => {
        const userData = {
          id: "<?php echo isset($_SESSION['usuario_id']) ? addslashes($_SESSION['usuario_id']) : ''; ?>",
          nome: "<?php echo isset($_SESSION['user_name']) ? addslashes($_SESSION['user_name']) : ''; ?>",
          email: "<?php echo isset($_SESSION['user_email']) ? addslashes($_SESSION['user_email']) : ''; ?>",
          usuario: "<?php echo isset($_SESSION['username']) ? addslashes($_SESSION['username']) : ''; ?>"
        };

        localStorage.setItem('userData', JSON.stringify(userData));
        
        window.location.href = 'index.html';
      });
      
      
      <?php endif; ?>
    </script>
  </body>
</html>