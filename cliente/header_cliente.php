<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$foto = isset($_SESSION['foto_perfil']) && $_SESSION['foto_perfil'] != ''
    ? '../uploads/' . $_SESSION['foto_perfil']
    : '../img/user.jpg';
?>
<div class="header-cliente">
  <div class="header-left">
    <a href="../index.php">
      <img src="../img/logo.jpg" alt="Logotipo" class="logo-cliente">
    </a>
  </div>

  <div class="header-right">
    <div class="user-menu">
      <img src="<?php echo $foto; ?>" alt="Utilizador" class="user-icon" onclick="toggleDropdown()">
      <div class="dropdown-menu-cliente" id="dropdownMenu">
        <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
        <a href="../index.php">Ver site</a>
        <a href="../logout.php">Logout</a>
      </div>
    </div>
  </div>
</div>

<script>
function toggleDropdown() {
  const menu = document.getElementById("dropdownMenu");
  menu.style.display = menu.style.display === "block" ? "none" : "block";
}
window.onclick = function(event) {
  if (!event.target.matches('.user-icon')) {
    const menu = document.getElementById("dropdownMenu");
    if (menu && menu.style.display === "block") {
      menu.style.display = "none";
    }
  }
};
</script>
