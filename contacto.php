<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contate-nos - SprintCar</title>
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

  <!-- Contact Form Section -->
  <section class="container mx-auto px-6 py-16">
    <div class="bg-white shadow-xl rounded-lg p-12 max-w-4xl mx-auto">
      <h3 class="text-4xl font-bold text-gray-800 text-center mb-8">Entre em contacto conosco</h3>
      <p class="text-center text-gray-500 mb-12">Preencha o formulário abaixo e nossa equipe entrará em contacto com você o mais breve possível.</p>
      <form action="process_form.php" method="POST" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Name -->
          <div>
            <label for="name" class="block text-lg font-semibold text-gray-700 mb-2">Nome Completo</label>
            <input type="text" id="name" name="name" placeholder="Digite seu nome" class="w-full border border-gray-300 rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
          </div>
          <!-- Email -->
          <div>
            <label for="email" class="block text-lg font-semibold text-gray-700 mb-2">Email</label>
            <input type="email" id="email" name="email" placeholder="Digite seu email" class="w-full border border-gray-300 rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
          </div>
        </div>
        <!-- Subject -->
        <div>
          <label for="subject" class="block text-lg font-semibold text-gray-700 mb-2">Assunto</label>
          <input type="text" id="subject" name="subject" placeholder="Digite o assunto da mensagem" class="w-full border border-gray-300 rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-purple-600" required>
        </div>
        <!-- Message -->
        <div>
          <label for="message" class="block text-lg font-semibold text-gray-700 mb-2">Mensagem</label>
          <textarea id="message" name="message" rows="6" placeholder="Digite sua mensagem" class="w-full border border-gray-300 rounded-lg p-4 focus:outline-none focus:ring-2 focus:ring-purple-600" required></textarea>
        </div>
        <!-- Submit Button -->
        <div class="text-center">
          <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 px-8 rounded-lg shadow-lg transition duration-200">
            Enviar Mensagem
          </button>
        </div>
      </form>
    </div>
  </section>
<!-- Blog Section -->
<section class="container mx-auto px-6 py-16">
    <h3 class="text-2xl font-bold text-center mb-12">Últimas postagens e notícias do blog</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Blog Card -->
      <div class="bg-white shadow-lg p-6 rounded-lg">
        <img src="https://via.placeholder.com/300x150" alt="Blog Post" class="w-full rounded-lg mb-4">
        <h4 class="text-lg font-semibold mb-2">Como Escolher o Carro Certo</h4>
        <p class="text-sm text-gray-500">Publicado em 15 de Novembro, 2023</p>
      </div>
      <!-- Repeat Blog Cards -->
      <div class="bg-white shadow-lg p-6 rounded-lg">
        <img src="https://via.placeholder.com/300x150" alt="Blog Post" class="w-full rounded-lg mb-4">
        <h4 class="text-lg font-semibold mb-2">Qual plano é certo para mim?</h4>
        <p class="text-sm text-gray-500">Publicado em 10 de Novembro, 2023</p>
      </div>
      <div class="bg-white shadow-lg p-6 rounded-lg">
        <img src="https://via.placeholder.com/300x150" alt="Blog Post" class="w-full rounded-lg mb-4">
        <h4 class="text-lg font-semibold mb-2">Dúvidas de veículos, escolha o melhor</h4>
        <p class="text-sm text-gray-500">Publicado em 5 de Novembro, 2023</p>
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
