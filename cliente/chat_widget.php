<?php

if (!isset($_SESSION['user_id'])) exit;
?>

<!-- BOTÃO PARA ABRIR O CHAT -->
<button id="chat-widget-button" onclick="toggleChat()">💬 Ajuda</button>

<!-- CONTAINER DO CHAT -->
<div id="chat-widget-container">
    <div id="chat-header">Suporte</div>
    <div id="chat-box">Carregando mensagens...</div>

    <form id="chat-form" method="POST" class="chat-form">
        <input type="text" name="mensagem" class="chat-input" placeholder="Digite sua mensagem..." required>
        <button type="submit" class="chat-button">Enviar</button>
    </form>
</div>

<link rel="stylesheet" href="css/chat_widget.css">

<script>
function toggleChat() {
    const container = document.getElementById('chat-widget-container');
    container.style.display = container.style.display === 'flex' ? 'none' : 'flex';
}

function atualizarChat() {
    fetch('get_messages.php')
    .then(res => res.text())
    .then(data => {
        const box = document.getElementById('chat-box');

        if (data.trim() === "") {
            const mensagemBoasVindas = `
                <p class="chat-message admin"><strong>Admin:</strong> Bem-vindo ao suporte!<br><br>
                Pode escrever uma das opções abaixo:<br>
                - <strong>pagamento</strong><br>
                - <strong>levantamento</strong><br>
                - <strong>dúvida</strong><br><br>
                A qualquer momento pode escrever <strong>menu</strong> para voltar aqui.
                </p>
            `;
            box.innerHTML = mensagemBoasVindas;
        } else {
            box.innerHTML = data;
        }

        box.scrollTop = box.scrollHeight;
    });
}
function enviarRapido(msg) {
    const input = document.querySelector('.chat-input');
    input.value = msg;
    document.getElementById('chat-form').requestSubmit();
}

setInterval(atualizarChat, 15000);
window.onload = atualizarChat;

document.getElementById('chat-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const dados = new FormData(this);
    fetch('chat.php', {
        method: 'POST',
        body: dados
    }).then(() => {
        this.reset();
        atualizarChat();
    });
});
</script>
