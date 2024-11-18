<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Selecione Veículos - SprintCar</title>
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


  <!-- Main Section -->
  <section class="container mx-auto px-6 py-12">
    <h2 class="text-2xl md:text-4xl font-bold text-center mb-8">Selecione um grupo de veículos</h2>
    
    <!-- Filter Tabs -->
    <div class="flex justify-center space-x-4 mb-8">
      <button class="bg-purple-600 text-white py-2 px-4 rounded-lg font-semibold">SUVs</button>
      <button class="bg-gray-200 text-gray-800 py-2 px-4 rounded-lg font-semibold">Sedans</button>
      <button class="bg-gray-200 text-gray-800 py-2 px-4 rounded-lg font-semibold">Compactos</button>
    </div>

    <!-- Vehicle Grid -->
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

      <!-- Repeat Vehicle Card (adjust content as needed) -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <img src="https://via.placeholder.com/300x150" alt="Car" class="w-full h-32 object-cover">
        <div class="p-4">
          <h3 class="text-lg font-semibold mb-2">BMW</h3>
          <p class="text-gray-500 mb-2">€60/dia</p>
          <button class="bg-purple-600 text-white py-2 px-4 w-full rounded-lg font-semibold hover:bg-purple-700">Reserve agora</button>
        </div>
      </div>

      <!-- Add more cards as needed -->
    </div>
  </section>

  <!-- Promotional Section -->
  <section class="bg-purple-600 text-white py-16">
    <div class="container mx-auto px-6 text-center">
      <h3 class="text-2xl md:text-3xl font-bold mb-4">Aproveite cada quilômetro com uma companhia adorável.</h3>
      <p class="mb-6">Reserve hoje mesmo para aproveitar as melhores ofertas.</p>
      <button class="bg-orange-500 px-6 py-3 rounded-lg font-semibold hover:bg-orange-600">Reserve agora</button>
    </div>
  </section>

  <!-- Brand Logos Section -->
  <section class="py-12">
    <div class="container mx-auto px-6 flex justify-center space-x-8">
      <img src="https://via.placeholder.com/100x50" alt="Toyota">
      <img src="https://via.placeholder.com/100x50" alt="Ford">
      <img src="https://via.placeholder.com/100x50" alt="Jeep">
      <img src="https://via.placeholder.com/100x50" alt="Audi">
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
