<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$cliente_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mensagem'])) {
    $mensagem = trim($_POST['mensagem']);
    if (!empty($mensagem)) {
        $stmt = $conn->prepare("INSERT INTO suporte_chat (cliente_id, mensagem, enviado_por) VALUES (?, ?, 'cliente')");
        $stmt->bind_param("is", $cliente_id, $mensagem);
        $stmt->execute();

        // Disparar IA
        include 'chat_bot.php';
        responderAutomaticamente($mensagem, $cliente_id, $conn);
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Chat de Suporte</title>
    <link rel="stylesheet" href="./css/cliente_chat.css">
    <style>
        .quick-replies {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .quick-replies button {
            background-color: #0044ff;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .quick-replies button:hover {
            background-color: #0033cc;
        }
    </style>
    <script>
function atualizarChat() {
    fetch('get_messages.php')
    .then(response => response.text())
    .then(data => {
        let chatBox = document.getElementById('chat-box');
        let respostas = document.getElementById('respostas-rapidas');

        if (data.trim()) {
            chatBox.innerHTML = data;
            chatBox.scrollTop = chatBox.scrollHeight;

            // Verifica se o cliente escreveu "menu"
            if (data.toLowerCase().includes("menu")) {
                respostas.style.display = 'flex';
            }

            // Verifica se entrou noutros fluxos (pagamento, dúvida, etc.) e esconde os botões
            if (
                data.toLowerCase().includes("aceitamos os seguintes métodos de pagamento") ||
                data.toLowerCase().includes("temos 3 locais disponíveis para levantamento") ||
                data.toLowerCase().includes("pode explicar melhor a sua dúvida") ||
                data.toLowerCase().includes("a devolução deve ser feita")
            ) {
                respostas.style.display = 'none';
            }
        }
    });
}
        function respostaRapida(texto) {
            const input = document.querySelector('.chat-input');
            input.value = texto;
            document.getElementById('chat-form').submit();
        }

        setInterval(atualizarChat, 15000);
        window.onload = atualizarChat;
    </script>
</head>
<body style="background: transparent;">
    <div class="chat-container">
        <div class="chat-header">
            <span></span>
        </div>

        <div id="chat-box" class="chat-box">
            <p class="chat-message admin"><strong>Admin:</strong> Bem-vindo ao suporte. Como posso ajudar?</p>
        </div>

        <form id="chat-form" action="chat.php" method="POST" class="chat-form">
            <input type="text" name="mensagem" class="chat-input" placeholder="Digite sua mensagem..." required>
            <button type="submit" class="chat-button">Enviar</button>
        </form>
    </div>
</body>

</html>
