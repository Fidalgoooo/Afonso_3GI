<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalhes do Veículo - SprintCar</title>
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
    <p class="text-gray-500 text-sm">Precisa de ajuda? <span class="font-semibold">+351 919 565 232</span></p>
  </div>
</header>


  <!-- Vehicle Details Section -->
  <section class="container mx-auto px-6 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
      <!-- Left Content -->
      <div>
        <h2 class="text-3xl font-bold mb-4">BMW</h2>
        <p class="text-lg text-gray-600 mb-6">US$ 25/dia</p>
        <img src="https://via.placeholder.com/600x300" alt="BMW" class="w-full rounded-lg shadow-lg mb-6">
        <!-- Gallery -->
        <div class="flex space-x-4">
          <img src="https://via.placeholder.com/100x60" alt="Gallery" class="w-20 h-12 rounded-lg shadow-sm">
          <img src="https://via.placeholder.com/100x60" alt="Gallery" class="w-20 h-12 rounded-lg shadow-sm">
          <img src="https://via.placeholder.com/100x60" alt="Gallery" class="w-20 h-12 rounded-lg shadow-sm">
          <img src="https://via.placeholder.com/100x60" alt="Gallery" class="w-20 h-12 rounded-lg shadow-sm">
        </div>
      </div>
      <!-- Right Content -->
      <div>
        <h3 class="text-xl font-bold mb-4">Especificações Técnicas</h3>
        <div class="grid grid-cols-2 gap-6 mb-6">
          <div class="bg-gray-100 p-4 rounded-lg text-center">
            <p class="text-sm font-medium">Assentos</p>
            <p class="text-lg font-bold">5</p>
          </div>
          <div class="bg-gray-100 p-4 rounded-lg text-center">
            <p class="text-sm font-medium">Portas</p>
            <p class="text-lg font-bold">4</p>
          </div>
          <div class="bg-gray-100 p-4 rounded-lg text-center">
            <p class="text-sm font-medium">Transmissão</p>
            <p class="text-lg font-bold">Automática</p>
          </div>
          <div class="bg-gray-100 p-4 rounded-lg text-center">
            <p class="text-sm font-medium">Combustível</p>
            <p class="text-lg font-bold">Gasolina</p>
          </div>
        </div>
        <h3 class="text-xl font-bold mb-4">Equipamentos Adicionais</h3>
        <ul class="list-disc pl-6 text-gray-600 mb-6">
          <li>Ar-condicionado</li>
          <li>GPS integrado</li>
          <li>Bluetooth</li>
        </ul>
        <button class="bg-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-purple-700">Reserve agora</button>
      </div>
    </div>
  </section>

  <!-- Other Cars Section -->
  <section class="container mx-auto px-6 py-12">
    <h3 class="text-2xl font-bold mb-8">Outros carros</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
      <!-- Vehicle Card -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <img src="https://via.placeholder.com/300x150" alt="Car" class="w-full h-32 object-cover">
        <div class="p-4">
          <h3 class="text-lg font-semibold mb-2">Mercedes</h3>
          <p class="text-gray-500 mb-2">€50/dia</p>
          <button class="bg-purple-600 text-white py-2 px-4 w-full rounded-lg font-semibold hover:bg-purple-700">Reserve agora</button>
        </div>
      </div>
      <!-- Repeat Cards -->
      <!-- Add more vehicle cards as needed -->
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
