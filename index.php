<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SprintCar - Aluguer de Carros</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/index.css">
</head>

<body>
  <div id="main">
    <!-- Header -->
    <header class="header">
      <a href="../Afonso_3GI/index.php">
        <img src="img/logo.jpg" alt="Logotipo" class="logo" height="40px" width="auto">
      </a>
      <nav class="nav-links">
        <a href="index.php">Início</a>
        <a href="sobre.php">Sobre Nós</a>
        <a href="contacto.php">Contactos</a>
      </nav>
      <div class="auth-links">
        <?php if (isset($_SESSION['user_id'])): ?>
          <div class="user-menu">
            <img src="img/user.jpg" alt="Perfil" class="user-icon" onclick="toggleDropdown()">
            <div id="dropdownMenu" class="dropdown-menu">
              <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
              <?php if (isset($_SESSION['user_permission']) && ($_SESSION['user_permission'] === 'adm' || $_SESSION['user_permission'] === 'chiefadmin')): ?>
                <a href="admin/index.php" class="admin-btn">Dashboard</a>
              <?php endif; ?>
              <?php if (isset($_SESSION['user_permission']) && ($_SESSION['user_permission'] === 'utilizador')): ?>
                <a href="cliente/index.php" class="admin-btn">Área de Cliente</a>
              <?php endif; ?>
              <a href="logout.php" class="logout-btn">Logout</a>
            </div>
          </div>
        <?php else: ?>
          <a href="login.php" class="login-btn">Login</a>
        <?php endif; ?>
      </div>
    </header>
    <script src="scripts/dropdown.js"></script>

    <!-- Informative Section -->
    <section class="slideshow-container">
      <div class="slide fade">
        <img src="img/slideshow/slide1.jpg" alt="Slide 1">
      </div>
      <div class="slide fade">
        <img src="img/slideshow/slide2.jpg" alt="Slide 2">
      </div>
      <div class="slide fade">
        <img src="img/slideshow/slide3.jpg" alt="Slide 3">
      </div>
      <div class="informative-overlay">
        <h1>Aluguer de Carros - Pesquise, Compare e Poupe</h1>
        <p>Cancelamento gratuito na maioria das reservas</p>
      </div>
    </section>

    <!-- Hero Form Section (NOVO) -->
<div class="formulario-reserva-wrapper">
  <form action="pesquisa_carros.php" method="GET" class="formulario-reserva">

    <div class="campo">
      <select name="local_retirada" id="origem" required>
        <option value="" disabled selected hidden>Levantamento</option>
        <option value="Lisboa">Lisboa</option>
        <option value="Porto">Porto</option>
        <option value="Faro">Faro</option>
      </select>
    </div>

    <div class="seta">⇄</div>

    <div class="campo">
      <select name="local_devolucao" id="destino" required>
        <option value="" disabled selected hidden>Devolução</option>
        <option value="Lisboa">Lisboa</option>
        <option value="Porto">Porto</option>
        <option value="Faro">Faro</option>
      </select>
    </div>

    <div class="divisor"></div>

    <div class="campo">
      <input type="date" name="data_retirada" id="data_retirada" required>
    </div>

    <div class="seta">→</div>

    <div class="campo">
      <input type="date" name="data_devolucao" id="data_devolucao" required placeholder="+ Data de regresso">
    </div>

    <button type="submit" class="btn-pesquisar">PESQUISAR</button>
  </form>
</div>

    <script src="scripts/date.js"></script>

    <!-- Features Section -->
    <section class="features">
      <div class="feature">
        <img src="img/loc.jpg" alt="Disponibilidade">
        <h3>Disponibilidade</h3>
        <p>Carros disponíveis nas principais cidades.</p>
      </div>
      <div class="feature">
        <img src="img/carro.jpg" alt="Conforto">
        <h3>Conforto</h3>
        <p>Carros novos e com o máximo conforto.</p>
      </div>
      <div class="feature">
        <img src="img/poupanca.jpg" alt="Poupança">
        <h3>Poupança</h3>
        <p>Alugue ao melhor preço com descontos especiais.</p>
      </div>
    </section>
  </div>

  <?php include './scripts/footer.php'; ?>
  <script src="./scripts/slideshow.js"></script>
</body>

</html>
