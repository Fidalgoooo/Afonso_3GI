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
  <!-- Header -->
  <header class="header">
<a href="../Afonso_3GI/index.php">
    <img src="img/logo.jpg" alt="Logotipo" class="logo" height="40px" width="auto">
</a>
  <nav class="nav-links">
    <a href="index.php">Início</a>
    <a href="informacoes/sobre.php">Sobre Nós</a>
    <a href="informacoes/contacto.php">Contactos</a>
  </nav>

  <!-- Verifica se o utilizador está logado -->
  <div class="auth-links">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-menu">
            <img src="img/user.jpg" alt="Perfil" class="user-icon" onclick="toggleDropdown()">
            <div id="dropdownMenu" class="dropdown-menu">
                <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <!-- Mostrar o botão do painel de administração apenas se for admin -->
                <?php if (isset($_SESSION['user_permission']) && $_SESSION['user_permission'] === 'adm'): ?>
                    <a href="../Afonso_3GI/admin/index.php" class="admin-btn">Admin</a>
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
  <section class="informative-section">
  <div class="informative-content">
    <h1>Aluguer de Carros - Pesquise, Compare e Poupe</h1>
    <ul class="benefits-list">
      <li><i class="fas fa-check-circle"></i> Cancelamento gratuito na maioria das reservas</li>
      <li><i class="fas fa-check-circle"></i> Mais de 60 000 localizações</li>
      <li><i class="fas fa-check-circle"></i> Apoio ao cliente em mais de 30 idiomas</li>
    </ul>
  </div>
  </section>

  <!-- Hero Section -->
  <form action="pesquisa_carros.php" method="GET" id="reservation-form">
    <div class="hero-form">
      <div class="form-group">
        <label for="pickup-location">Local de Levantamento</label>
        <select name="local_retirada" id="pickup-location" required>
          <option value="Lisboa">Lisboa</option>
          <option value="Porto">Porto</option>
          <option value="Faro">Faro</option>
        </select>
      </div>
      <div class="form-group">
        <label for="dropoff-location">Local de Devolução</label>
        <select name="local_devolucao" id="dropoff-location" required>
          <option value="Lisboa">Lisboa</option>
          <option value="Porto">Porto</option>
          <option value="Faro">Faro</option>
        </select>
      </div>
      <div class="form-group">
        <label for="pickup-date">Data do Levantamento</label>
        <input type="date" name="data_retirada" id="pickup-date" required>
      </div>
      <div class="form-group">
        <label for="return-date">Data de Devolução</label>
        <input type="date" name="data_devolucao" id="return-date" required>
      </div>
      <button type="submit" class="btn-secondary">Reserve Agora</button>
  </div>

<script>
  // Validação de data: a data de devolução não pode ser antes da data do Levantamento
  const pickupDate = document.getElementById('pickup-date');
  const returnDate = document.getElementById('return-date');

  returnDate.addEventListener('change', function() {
    if (new Date(returnDate.value) < new Date(pickupDate.value)) {
      alert("A data de devolução não pode ser antes da data do Levantamento.");
      returnDate.setCustomValidity("A data de devolução não pode ser antes da data do Levantamento.");
    } else {
      returnDate.setCustomValidity("");
    }
  });
</script>

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

<!-- Testimonials Section 
  <section class="testimonials" data-aos="fade-up">
    <h2>O que os nossos clientes dizem</h2>
    <div class="testimonial">
      <p>"Serviço excelente! O carro estava impecável e o atendimento foi super atencioso."</p>
      <h4>- João Silva</h4>
    </div>
    <div class="testimonial">
      <p>"Recomendo vivamente! Aluguei um carro em Lisboa e foi uma experiência tranquila."</p>
      <h4>- Maria Oliveira</h4>
    </div>
  </section> -->

  <div>
    <?php include 'footer.php'; ?>
  </div>

</body>
</html>