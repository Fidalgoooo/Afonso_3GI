document.addEventListener("DOMContentLoaded", function () {
    loadUsers();

    document.getElementById("message-form").addEventListener("submit", function (event) {
        event.preventDefault();
        sendMessage();
    });
});

function loadUsers() {
    fetch("admin_chat.php?fetch_users=true")
        .then(response => response.json())
        .then(users => {
            const userList = document.getElementById("users");
            userList.innerHTML = "";

            if (!Array.isArray(users) || users.length === 0) {
                userList.innerHTML = "<p>Nenhum utilizador encontrado.</p>";
                return;
            }

            users.forEach(user => {
                let li = document.createElement("li");
                li.dataset.id = user.id;
                li.innerHTML = user.nova_msg == 1
                    ? `${user.nome} <span class="badge-piscar"></span>`
                    : user.nome;
                li.onclick = () => {
                    removerSino(li);
                    marcarComoVisto(user.id);
                    loadMessages(user.id, user.nome);
                };

                userList.appendChild(li);
            });
        })
        .catch(error => console.error("Erro ao buscar utilizadores:", error));
}

function loadMessages(cliente_id, nome) {
    document.getElementById("chat-title").textContent = "Chat com " + nome;
    document.getElementById("cliente_id").value = cliente_id;

    fetch(`admin_chat.php?fetch_messages=true&cliente_id=${cliente_id}`)
        .then(response => response.json())
        .then(messages => {
            const messageBox = document.getElementById("messages");
            messageBox.innerHTML = "";

            if (!messages.length) {
                messageBox.innerHTML = "<p>Sem mensagens anteriores.</p>";
                return;
            }

            messages.forEach(msg => {
                const div = document.createElement("div");
                const tipo = msg.enviado_por.toLowerCase(); // 'cliente', 'bot' ou 'admin'
                div.className = `message ${tipo}`;
                div.innerHTML = `<strong>${msg.enviado_por}</strong>${msg.mensagem}`;
                messageBox.appendChild(div);
            });
            messageBox.scrollTop = messageBox.scrollHeight;

        });
}

function sendMessage() {
    const cliente_id = document.getElementById("cliente_id").value;
    const mensagem = document.getElementById("message-input").value;

    fetch("admin_chat.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `cliente_id=${cliente_id}&mensagem=${encodeURIComponent(mensagem)}`
    })
    .then(() => {
        document.getElementById("message-input").value = "";
        loadMessages(cliente_id, document.getElementById("chat-title").textContent.replace("Chat com ", ""));
    });
}

function marcarComoVisto(cliente_id) {
    fetch(`admin_chat.php?marcar_visto=true&cliente_id=${cliente_id}`);
}

function removerSino(elemento) {
    const span = elemento.querySelector('.badge-piscar');
    if (span) span.remove();
}
