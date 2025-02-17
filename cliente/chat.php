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
    <script>
        function atualizarChat() {
            fetch('get_messages.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('chat-box').innerHTML = data;
            });
        }
        setInterval(atualizarChat, 3000); // Atualiza a cada 3 segundos
        window.onload = atualizarChat; // Carrega mensagens ao abrir
    </script>
</head>
<body>
    <h3>Chat de Suporte</h3>
    <div id="chat-box"></div>

    <form action="chat.php" method="POST">
        <input type="text" name="mensagem" placeholder="Digite sua mensagem..." required>
        <button type="submit">Enviar</button>
    </form>

    <br>
    <a href="index.php">Voltar</a>
</body>
</html>
