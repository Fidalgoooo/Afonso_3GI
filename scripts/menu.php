<header class="header">
<a href="../Afonso_3GI/index.php">
    <img src="img/logo.jpg" alt="Logotipo" class="logo" height="40px" width="auto">
</a>
  <nav class="nav-links">
    <a href="index.php">Início</a>
    <a href="./about/sobre.php">Sobre Nós</a>
    <a href="./about/contacto.php">Contactos</a>
  </nav>

  <!-- Verifica se o utilizador está logado -->
  <div class="auth-links">
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-menu">
            <img src="img/user.jpg" alt="Perfil" class="user-icon" onclick="toggleDropdown()">
            <div id="dropdownMenu" class="dropdown-menu">
                <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                <!-- Mostrar o botão do painel de administração apenas se for admin -->
                <?php if (isset($_SESSION['user_permission']) && ($_SESSION['user_permission'] === 'adm' || $_SESSION['user_permission'] === 'chiefadmin')): ?>
                    <a href="admin/index.php" class="admin-btn">Dashboard</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_permission']) && ($_SESSION['user_permission'] === 'utilizador')): ?>
                    <a href="cliente/index.php" class="admin-btn">Painel</a>
                <?php endif; ?>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    <?php else: ?>
        <a href="login.php" class="login-btn">Login</a>
    <?php endif; ?>
</div>
</header>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById("dropdownMenu");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    }

    window.onclick = function(event) {
        if (!event.target.matches('.user-icon')) {
            const dropdown = document.getElementById("dropdownMenu");
            if (dropdown && dropdown.style.display === "block") {
                dropdown.style.display = "none";
            }
        }
    };
</script>
<link rel="stylesheet" href="css/style.css">