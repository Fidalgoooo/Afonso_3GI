<?php
session_start();
include '../db.php';

// Verificar se o admin está autenticado
if (!isset($_SESSION['user_permission']) || ($_SESSION['user_permission'] !== 'adm' && $_SESSION['user_permission'] !== 'chiefadmin')) {
    header("Location: ../login.php");
    exit;
}

// Buscar TODOS os utilizadores com permissão 'utilizador'
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_users'])) {
    $stmt = $conn->query("SELECT id_utilizador AS id, nome FROM utilizadores WHERE permissao = 'utilizador'");

    if (!$stmt) {
        echo json_encode(["error" => $conn->error]); // Mostra erro no SQL se existir
        exit;
    }

    $result = $stmt->fetch_all(MYSQLI_ASSOC);

    if (empty($result)) {
        echo json_encode(["error" => "Nenhum utilizador encontrado."]); // Caso não existam utilizadores
        exit;
    }

    echo json_encode($result);
    exit;
}

// Procurar mensagens do utilizador selecionado
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_messages']) && isset($_GET['cliente_id'])) {
    $cliente_id = intval($_GET['cliente_id']);
    $stmt = $conn->prepare("SELECT mensagem, enviado_por, enviado_em FROM suporte_chat WHERE cliente_id = ? ORDER BY enviado_em ASC");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    exit;
}

// Enviar mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'], $_POST['cliente_id'])) {
    $cliente_id = intval($_POST['cliente_id']);
    $mensagem = trim($_POST['mensagem']);

    if (!empty($mensagem)) {
        $stmt = $conn->prepare("INSERT INTO suporte_chat (cliente_id, mensagem, enviado_por) VALUES (?, ?, 'admin')");
        $stmt->bind_param("is", $cliente_id, $mensagem);
        $stmt->execute();
    }
    exit;
}
?>


<!DOCTYPE html> 
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de Suporte - Admin</title>
    <link rel="stylesheet" href="./css/admin_chat.css">
</head>
<body>
    <div class="chat-container">
        <!-- Barra lateral com lista de utilizadores -->
        <div class="user-list">
            <h3>Utilizadores</h3>
            <ul id="users"></ul>
        </div>

        <!-- Área do chat -->
        <div class="chat-box">
            <h3 id="chat-title">Selecione um utilizador</h3>
            <div id="messages"></div>
            <form id="message-form">
                <input type="hidden" id="cliente_id">
                <input type="text" id="message-input" placeholder="Digite sua mensagem..." required>
                <button type="submit">Enviar</button>
            </form>
        <a href="index.php" class="back-link"> Voltar</a>
        </div>
    </div>

    <script src="admin_chat.js"></script>
</body>
</html>

<?php
// Buscar utilizadores
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_users'])) {
    $stmt = $conn->query("SELECT id_utilizador AS id, nome FROM utilizadores WHERE permissao = 'utilizador'");
    echo json_encode($stmt->fetch_all(MYSQLI_ASSOC));
    exit;
}

// Buscar mensagens
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_messages']) && isset($_GET['cliente_id'])) {
    $cliente_id = intval($_GET['cliente_id']);
    $stmt = $conn->prepare("SELECT mensagem, enviado_por, enviado_em FROM suporte_chat WHERE cliente_id = ? ORDER BY enviado_em ASC");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    exit;
}

// Enviar mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'], $_POST['cliente_id'])) {
    $cliente_id = intval($_POST['cliente_id']);
    $mensagem = trim($_POST['mensagem']);

    if (!empty($mensagem)) {
        $stmt = $conn->prepare("INSERT INTO suporte_chat (cliente_id, mensagem, enviado_por) VALUES (?, ?, 'admin')");
        $stmt->bind_param("is", $cliente_id, $mensagem);
        $stmt->execute();
    }
    exit;
}
?>
