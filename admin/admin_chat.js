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
            userList.innerHTML = ""; // Limpa a lista antes de adicionar novos utilizadores

            if (!Array.isArray(users) || users.length === 0) {
                userList.innerHTML = "<p>Nenhum utilizador encontrado.</p>";
                return;
            }

            users.forEach(user => {
                let li = document.createElement("li");
                li.textContent = user.nome;
                li.dataset.id = user.id;
                li.onclick = () => loadMessages(user.id, user.nome);
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
            messageBox.innerHTML = ""; // Limpa a área de mensagens antes de carregar novas

            if (!messages.length) {
                messageBox.innerHTML = "<p>Sem mensagens anteriores.</p>";
                return;
            }

            messages.forEach(msg => {
                let p = document.createElement("p");
                p.textContent = msg.enviado_por + ": " + msg.mensagem + " (" + msg.enviado_em + ")";
                messageBox.appendChild(p);
            });
        });
}

function sendMessage() {
    const cliente_id = document.getElementById("cliente_id").value;
    const mensagem = document.getElementById("message-input").value;

    fetch("admin_chat.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `cliente_id=${cliente_id}&mensagem=${mensagem}`
    })
    .then(() => {
        document.getElementById("message-input").value = "";
        loadMessages(cliente_id, document.getElementById("chat-title").textContent.replace("Chat com ", ""));
    });
}
