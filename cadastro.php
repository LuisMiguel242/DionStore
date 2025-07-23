<?php

$servername = "localhost"; 
$username = "root";         
$password = "";          
$dbname = "dion_store";     


$mensagem = "";
$tipo_mensagem = "";


if(isset($_POST['submit'])) {
 
    $conn = new mysqli($servername, $username, $password, $dbname);
    
   
    if ($conn->connect_error) {
        $mensagem = "Falha na conexão com o banco de dados: " . $conn->connect_error;
        $tipo_mensagem = "erro";
    } else {
       
        $nome = $conn->real_escape_string($_POST['nome']);
        $usuario = $conn->real_escape_string($_POST['usuario']);
        $email = $conn->real_escape_string($_POST['email']);
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $telefone = $conn->real_escape_string($_POST['telefone']);
        $endereco = $conn->real_escape_string($_POST['endereco']);
        $cidade = $conn->real_escape_string($_POST['cidade']);
        $estado = $conn->real_escape_string($_POST['estado']);
        $cep = $conn->real_escape_string($_POST['cep']);
        $data_cadastro = date("Y-m-d H:i:s");
        
       
        $verificar = $conn->query("SELECT * FROM usuarios WHERE email = '$email' OR usuario = '$usuario'");
        
        if ($verificar->num_rows > 0) {
           
            $mensagem = "Este usuário ou email já está cadastrado.";
            $tipo_mensagem = "erro";
        } else {
            
            $sql = "INSERT INTO usuarios (nome, usuario, email, senha, telefone, endereco, cidade, estado, cep, data_cadastro) 
                    VALUES ('$nome', '$usuario', '$email', '$senha', '$telefone', '$endereco', '$cidade', '$estado', '$cep', '$data_cadastro')";
            
            
            if ($conn->query($sql) === TRUE) {
                $mensagem = "Cadastro realizado com sucesso! Você pode fazer login agora.";
                $tipo_mensagem = "sucesso";
                
                
                header("refresh:3;url=login.php");
            } else {
                $mensagem = "Erro ao cadastrar: " . $conn->error;
                $tipo_mensagem = "erro";
            }
        }
        
        
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html class="wide wow-animation" lang="pt-br">
  <head>
    <title>Cadastro - Dion</title>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width height=device-height initial-scale=1.0 maximum-scale=1.0 user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <link rel="icon" href="images/dion definitivo branco.png" type="images/dion definitivo branco.png">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Work+Sans:300,400,500,700,800%7CPoppins:300,400,700">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/style.css" id="main-styles-link">
    <link rel="stylesheet" href="css/cadastro.css" id="main-styles-link">
    <script src="js/cadastro.js" defer></script>
  </head>
  <body>
    <div class="ie-panel"><a href="http://windows.microsoft.com/en-US/internet-explorer/"><img src="images/dion definitivo branco.png" height="42" width="820" alt="Você está usando um navegador desatualizado. Para uma experiência de navegação mais rápida e segura, atualize gratuitamente hoje."></a></div>
    <div class="preloader">
      <div class="preloader-logo"><img src="images/dion definitivo branco.png" alt="" width="151" height="44" srcset="images/dion definitivo branco.png"/>
      </div>
      <div class="preloader-body">
        <div id="loadingProgressG">
          <div class="loadingProgressG" id="loadingProgressG_1"></div>
        </div>
      </div>
    </div>
    <div class="page">
      
      <header class="section page-header">
        <div class="rd-navbar-wrap">
          <nav class="rd-navbar rd-navbar-corporate" data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-fixed" data-md-layout="rd-navbar-fixed" data-md-device-layout="rd-navbar-fixed" data-lg-layout="rd-navbar-static" data-lg-device-layout="rd-navbar-static" data-lg-stick-up="true" data-lg-stick-up-offset="118px" data-xl-layout="rd-navbar-static" data-xl-device-layout="rd-navbar-static" data-xl-stick-up="true" data-xl-stick-up-offset="118px" data-xxl-layout="rd-navbar-static" data-xxl-device-layout="rd-navbar-static" data-xxl-stick-up-offset="118px" data-xxl-stick-up="true">
            <div class="rd-navbar-aside-outer">
              <div class="rd-navbar-aside">
                
                <div class="rd-navbar-panel">
                  
                  <button class="rd-navbar-toggle" data-rd-navbar-toggle="#rd-navbar-nav-wrap-1"><span></span></button>
                 <a class="rd-navbar-brand" href="index.html"><img src="images/dion definitivo branco.png" alt="" width="151" height="44" srcset="images/dion definitivo branco.png"/></a>
                </div>
                <div class="rd-navbar-collapse">
                  <button class="rd-navbar-collapse-toggle rd-navbar-fixed-element-1" data-rd-navbar-toggle="#rd-navbar-collapse-content-1"><span></span></button>
                  <div class="rd-navbar-collapse-content" id="rd-navbar-collapse-content-1">
                    <article class="unit align-items-center">
                      <div class="unit-left"><span class="icon novi-icon icon-md icon-modern mdi mdi-phone"></span></div>
                      <div class="unit-body">
                        <ul class="list-0">
                          <li><a class="link-default" href="tel:#">(14) 998364178</a></li>
                        </ul>
                      </div>
                    </article>
                    <article class="unit align-items-center">
                      <div class="unit-left"><span class="icon novi-icon icon-md icon-modern mdi mdi-map-marker"></span></div>
                      <div class="unit-body"><a class="link-default" href="tel:#">Rua Miguel Galleigo Pedroelo <br> Sarutaia, SP 18840-302</a></div>
                    </article>
                    <a class="button button-gray-bordered button-winona" href="login.php">Ligue para nós</a>
                    <a class="button button-gray-bordered button-winona" href="login.php">Entrar</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="rd-navbar-main-outer">
              <div class="rd-navbar-main">
                <div class="rd-navbar-nav-wrap" id="rd-navbar-nav-wrap-1">
                 
                  <ul class="rd-navbar-nav">
                    <li class="rd-nav-item"><a class="rd-nav-link" href="login.php">Menu</a>
                    </li>
                    <li class="rd-nav-item"><a class="rd-nav-link" href="login.php">Nós</a>
                    </li>
                    <li class="rd-nav-item"><a class="rd-nav-link" href="login.php">Produtos</a>
                    </li>
                    <li class="rd-nav-item"><a class="rd-nav-link" href="login.php">Contate-nos</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </nav>
        </div>
      </header>

     
      <section class="section novi-background cadastro-section">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="cadastro-form wow-outer">
                <div class="wow fadeInUp">
                  <h3 class="text-uppercase font-weight-bold">Cadastre-se</h3>
                  
                  <?php if (!empty($mensagem)): ?>
                    <div class="alert <?php echo $tipo_mensagem == 'sucesso' ? 'alert-success' : 'alert-danger'; ?>">
                      <?php echo $mensagem; ?>
                    </div>
                  <?php endif; ?>
                  
                  <form class="rd-form" id="cadastro-form" method="post" action="">
                    <div class="form-row">
                      <div class="form-col">
                        <div class="form-group">
                          <label class="form-label" for="nome"></label>
                          <input class="form-control" id="nome" type="text" placeholder="Seu Nome Completo" name="nome" required>
                        </div>
                      </div>
                      <div class="form-col">
                        <div class="form-group">
                          <label class="form-label" for="usuario"></label>
                          <input class="form-control" id="usuario" type="text" placeholder="Seu Usuario" name="usuario" required>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label class="form-label" for="email"></label>
                      <input class="form-control" id="email" type="email" placeholder="Seu email" name="email" required>
                    </div>
                    
                    <div class="form-row">
                      <div class="form-col">
                        <div class="form-group">
                          <label class="form-label" for="senha"></label>
                          <input class="form-control" id="senha" type="password" placeholder="Sua senha" name="senha" required>
                        </div>
                      </div>
                      <div class="form-col">
                        <div class="form-group">
                          <label class="form-label" for="confirmar-senha"></label>
                          <input class="form-control" id="confirmar-senha" type="password" placeholder="Confirma sua senha" name="confirmar-senha" required>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label class="form-label" for="telefone"></label>
                      <input class="form-control" id="telefone" type="tel" placeholder="(XX) XXXXX-XXXX" name="telefone" required>
                    </div>
                    
                    <div class="form-group">
                      <label class="form-label" for="endereco"></label>
                      <input class="form-control" id="endereco" type="text" placeholder="Seu endereço" name="endereco" required>
                    </div>
                    
                    <div class="form-row">
                      <div class="form-col">
                        <div class="form-group">
                          <label class="form-label" for="cidade"></label>
                          <input class="form-control" id="cidade" type="text" placeholder="Sua Cidade" name="cidade" required>
                        </div>
                      </div>
                      <div class="form-col">
                        <div class="form-group">
                          <label class="form-label" for="estado"></label>
                          <select class="form-control" id="estado" name="estado" required>
                            <option value="" selected disabled>Selecione o estado</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                          </select>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label class="form-label" for="cep"></label>
                      <input class="form-control" id="cep" type="text" name="cep" placeholder="XXXXX-XXX" required>
                    </div>
                    
                    <div class="form-group form-check">
                      <input class="form-check-input" type="checkbox" id="aceito-termos" name="aceito-termos" required>
                      <label class="form-check-label" for="aceito-termos">
                        Concordo com os <a href="#">Termos de Serviço</a> e <a href="#">Política de Privacidade</a>
                      </label>
                    </div>
                    
                  
                    <button class="cadastro-btn" type="submit" name="submit">Cadastrar</button>
                    
                    <div class="alternative-link">
                      <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      
      <footer class="section novi-background footer-advanced bg-gray-700">
        <div class="footer-advanced-main">
          <div class="container">
            <div class="row row-50">
              <div class="col-lg-4">
                <h5 class="font-weight-bold text-uppercase text-white">Sobre nós</h5>
                <p class="footer-advanced-text">Dion é uma loja que está se estruturando agora, mas veio para trazer a arte do Street e da nossa moda. Dion promete ter a sua confiança para poder te trazer satisfação, uma loja que quer evoluir e mostrar para o mundo o que realmente é o estilo de rua em seu corpo.</p>
              </div>
              <div class="col-sm-7 col-md-5 col-lg-4">
                <h5 class="font-weight-bold text-uppercase text-white">Siga a gente nas redes sociais</h5>
                
                <article class="post-inline">
                  <p class="post-inline-title"><a href="#">luismiguel_ns, pedrohenriquezzs, Guizhwxy_, patrickgarrote
                  </a></p>
                  <ul class="post-inline-meta">
                    <li>Dion</li>
                    <li><a href="#">2 dias atrás</a></li>
                  </ul>
                </article>
              </div>
              <div class="col-sm-5 col-md-7 col-lg-4">
                <h5 class="font-weight-bold text-uppercase text-white">Galeria</h5>
                <div class="row row-x-10" data-lightgallery="group">
                  <div class="col-3 col-sm-4 col-md-3"><a class="thumbnail-minimal" href="images/shapes.png" data-lightgallery="item"><img class="thumbnail-minimal-image" src="images/shapes.png" alt=""/>
                      <div class="thumbnail-minimal-caption"></div></a></div>
                  <div class="col-3 col-sm-4 col-md-3"><a class="thumbnail-minimal" href="images/trucks1.png" data-lightgallery="item"><img class="thumbnail-minimal-image" src="images/trucks1.png" alt=""/>
                      <div class="thumbnail-minimal-caption"></div></a></div>
                  <div class="col-3 col-sm-4 col-md-3"><a class="thumbnail-minimal" href="images/skate.jpg" data-lightgallery="item"><img class="thumbnail-minimal-image" src="images/skate.jpg" alt=""/>
                      <div class="thumbnail-minimal-caption"></div></a></div>
                  <div class="col-3 col-sm-4 col-md-3"><a class="thumbnail-minimal" href="images/dion camisa brasil.png" data-lightgallery="item"><img class="thumbnail-minimal-image" src="images/dion camisa brasil.png" alt=""/>
                      <div class="thumbnail-minimal-caption"></div></a></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="footer-advanced-aside">
          <div class="container">
            <div class="footer-advanced-layout">
              <div>
                <ul class="list-nav">
                  <li><a href="login.php">Menu</a></li>
                  <li><a href="login.php">Sobre</a></li>
                  <li><a href="login.php">Produtos</a></li>
                  <li><a href="login.php">Contatos</a></li>
                </ul>
              </div>
              <div>
                <ul class="foter-social-links list-inline list-inline-md">
                  <li><a class="icon novi-icon icon-sm link-default mdi mdi-facebook" href="#"></a></li>
                  <li><a class="icon novi-icon icon-sm link-default mdi mdi-twitter" href="#"></a></li>
                  <li><a class="icon novi-icon icon-sm link-default mdi mdi-instagram" href="#"></a></li>
                  <li><a class="icon novi-icon icon-sm link-default mdi mdi-google" href="#"></a></li>
                  <li><a class="icon novi-icon icon-sm link-default mdi mdi-linkedin" href="#"></a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="container">
          <hr/>
        </div>
        <div class="footer-advanced-aside">
          <div class="container">
            <div class="footer-advanced-layout">
              <a class="brand" href="login.php"><img src="images/dion definitivo branco.png" alt="" width="115" height="34" srcset="images/dion definitivo branco.png"/></a>
             
              <p class="rights"><span>&copy;&nbsp;</span><span class="copyright-year"></span>. Todos os direitos reservados</p>
            </div>
          </div>
        </div>
      </footer>
    </div>
    
    <div class="snackbars" id="form-output-global"></div>
    
    <script src="js/core.min.js"></script>
    <script src="js/script.js"></script>
    <script src="js/cadastro.js" defer></script>
    
   </body>
</html>