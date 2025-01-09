// Ficheiro: dropdown.js

// Função para mostrar/esconder o dropdown ao clicar no ícone do utilizador
function toggleDropdown() {
  var dropdownMenu = document.getElementById('dropdownMenu');
  if (dropdownMenu.style.display === 'none' || dropdownMenu.style.display === '') {
    dropdownMenu.style.display = 'block';
  } else {
    dropdownMenu.style.display = 'none';
  }
}

// Fecha o menu dropdown ao clicar fora dele
window.onclick = function(event) {
  if (!event.target.matches('.user-icon')) {
    var dropdowns = document.getElementsByClassName('dropdown-menu');
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.style.display === 'block') {
        openDropdown.style.display = 'none';
      }
    }
  }
}
