<?php
session_start();
include '../db.php';

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$cliente_id = $_SESSION['user_id'];

// Processar mensagem enviada
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mensagem'])) {
    $mensagem = trim($_POST['mensagem']);
    if (!empty($mensagem)) {
        $stmt = $conn->prepare("INSERT INTO suporte_chat (cliente_id, mensagem, enviado_por) VALUES (?, ?, 'cliente')");
        $stmt->bind_param("is", $cliente_id, $mensagem);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de Suporte</title>
    <link rel="stylesheet" href="./css/cliente_chat.css">
    <script>
        function atualizarChat() {
            fetch('get_messages.php')
            .then(response => response.text())
            .then(data => {
                let chatBox = document.getElementById('chat-box');
                if (data.trim()) { // Apenas substitui se houver mensagens
                    chatBox.innerHTML = data;
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            });
        }
        setInterval(atualizarChat, 3000); // Atualiza a cada 3 segundos
        window.onload = atualizarChat; // Carrega mensagens ao abrir
    </script>
</head>
<body>
    <div class="chat-container">
        <h3>Chat de Suporte</h3>
        <div id="chat-box" class="chat-box">
            <p class="chat-message admin"><strong>Admin:</strong> Bem-vindo ao suporte. Como posso ajudar? </p>
        </div>

        <form action="chat.php" method="POST" class="chat-form">
            <input type="text" name="mensagem" class="chat-input" placeholder="Digite sua mensagem..." required>
            <button type="submit" class="chat-button">Enviar</button>
        </form>

        <a href="index.php" class="back-link"> Voltar</a>
    </div>
</body>
</html>

