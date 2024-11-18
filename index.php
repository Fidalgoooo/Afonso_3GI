<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SprintCar</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans bg-gray-50">
  <!-- Header -->
  <header class="bg-white shadow-md">
  <div class="container mx-auto px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold text-gray-800"><a href="index.php">SprintCar</a></h1>
    <nav class="flex space-x-6">
      <a href="index.php" class="text-gray-600 hover:text-gray-800">Início</a>
      <a href="veiculos.php" class="text-gray-600 hover:text-gray-800">Veículos</a>
      <a href="detalhes.php" class="text-gray-600 hover:text-gray-800">Detalhes</a>
      <a href="sobre.php" class="text-gray-600 hover:text-gray-800">Sobre Nós</a>
      <a href="contacto.php" class="text-gray-600 hover:text-gray-800">Contactos</a>
    </nav>
    <p class="text-gray-500 text-sm">Precisa de ajuda??? <span class="font-semibold">+351 919 565 232</span></p>
  </div>
</header>


  <!-- Hero Section -->
  <section class="bg-purple-600 text-white py-16">
    <div class="container mx-auto px-6 flex flex-col md:flex-row items-center">
      <!-- Left Content -->
      <div class="md:w-1/2">
        <h2 class="text-4xl font-bold mb-4">Experimente como nunca antes</h2>
        <p class="mb-6">Aproveite momentos únicos com o Sprint Car. Quem experimenta volta sempre.</p>
        <button class="bg-orange-500 px-6 py-3 rounded-lg font-semibold hover:bg-orange-600">Ver todos os carros</button>
      </div>
      <!-- Right Form -->
      <div class="md:w-1/2 bg-white text-gray-800 p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold mb-4">Reserve agora</h3>
        <form>
          <div class="mb-4">
            <label for="car-type" class="block text-sm font-medium">Tipo de carro</label>
            <select id="car-type" class="w-full border-gray-300 rounded-lg p-2">
              <option>SUV</option>
              <option>Sedan</option>
              <option>Compacto</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="pickup-location" class="block text-sm font-medium">Local de retirada</label>
            <select id="pickup-location" class="w-full border-gray-300 rounded-lg p-2">
              <option>Lisboa</option>
              <option>Porto</option>
              <option>Faro</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="dropoff-location" class="block text-sm font-medium">Local de devolução</label>
            <select id="dropoff-location" class="w-full border-gray-300 rounded-lg p-2">
              <option>Lisboa</option>
              <option>Porto</option>
              <option>Faro</option>
            </select>
          </div>
          <button type="submit" class="bg-orange-500 w-full py-3 rounded-lg font-semibold text-white hover:bg-orange-600">Reserve agora</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="py-12 bg-white">
    <div class="container mx-auto px-6 text-center">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <div class="text-4xl text-purple-600 mb-4">🚗</div>
          <h4 class="text-lg font-semibold mb-2">Disponibilidade</h4>
          <p>Encontre um carro quando você precisar.</p>
        </div>
        <div>
          <div class="text-4xl text-purple-600 mb-4">🛋️</div>
          <h4 class="text-lg font-semibold mb-2">Conforto</h4>
          <p>A garantia de um veículo de alta qualidade.</p>
        </div>
        <div>
          <div class="text-4xl text-purple-600 mb-4">💰</div>
          <h4 class="text-lg font-semibold mb-2">Poupança</h4>
          <p>Viaje com opções acessíveis e econômicas.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-900 text-white py-12">
    <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-8">
      <div>
        <h5 class="font-bold text-lg mb-4">SprintCar</h5>
        <p>Dirija-se às gargantas das estradas sem falhas.</p>
      </div>
      <div>
        <h5 class="font-bold text-lg mb-4">Endereço</h5>
        <p>Avenida Oxford, Cary</p>
      </div>
      <div>
        <h5 class="font-bold text-lg mb-4">Contactos</h5>
        <p>Email: <a href="mailto:mail@site.com" class="underline">mail@site.com</a></p>
        <p>Telefone: +351 295 987 332</p>
      </div>
      <div>
        <h5 class="font-bold text-lg mb-4">Baixar aplicativo</h5>
        <div class="flex space-x-4">
          <img src="https://via.placeholder.com/150x50" alt="App Store">
          <img src="https://via.placeholder.com/150x50" alt="Google Play">
        </div>
      </div>
    </div>
    <div class="mt-8 text-center text-sm text-gray-400">
      &copy; Copyright Car Rental 2024. Design by FigmaToCode.
    </div>
  </footer>
</body>
</html>
