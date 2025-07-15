<?php
session_start();
include '../db.php';

// Verificar se o admin está autenticado
if (!isset($_SESSION['user_permission']) || ($_SESSION['user_permission'] !== 'adm' && $_SESSION['user_permission'] !== 'chiefadmin')) {
    header("Location: ../login.php");
    exit;
}

// Buscar utilizadores com sino
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_users'])) {
    $sql = "
        SELECT u.id_utilizador AS id, u.nome,
               EXISTS (
                   SELECT 1 FROM suporte_chat s
                   WHERE s.cliente_id = u.id_utilizador AND s.visto_admin = 0
               ) AS nova_msg
        FROM utilizadores u
        WHERE u.permissao = 'utilizador'
    ";
    $res = $conn->query($sql);
    $utilizadores = $res->fetch_all(MYSQLI_ASSOC);
    echo json_encode($utilizadores);
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

// Marcar como visto
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['marcar_visto'], $_GET['cliente_id'])) {
    $cliente_id = intval($_GET['cliente_id']);
    $conn->query("UPDATE suporte_chat SET visto_admin = 1 WHERE cliente_id = $cliente_id");
    exit;
}

// Enviar mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mensagem'], $_POST['cliente_id'])) {
    $cliente_id = intval($_POST['cliente_id']);
    $mensagem = trim($_POST['mensagem']);

    if (!empty($mensagem)) {
        $stmt = $conn->prepare("INSERT INTO suporte_chat (cliente_id, mensagem, enviado_por, visto_admin) VALUES (?, ?, 'admin', 1)");
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
  <title>Chat Admin</title>
  <link rel="stylesheet" href="./css/admin_chat.css">
</head>
<body>
  <div class="chat-admin-container">
    <aside class="chat-sidebar">
      <h2>Utilizadores</h2>
      <ul id="users"></ul>
    </aside>

    <main class="chat-main">
      <header class="chat-header">
        <h3 id="chat-title">Selecione um utilizador</h3>
      </header>

      <section id="messages" class="chat-messages"></section>

      <form id="message-form" class="chat-form">
        <input type="hidden" id="cliente_id">
        <input type="text" id="message-input" placeholder="Digite a sua mensagem..." required>
        <button type="submit">Enviar</button>
        <a href="index.php" class="btn-voltar">Voltar</a>
      </form>
    </main>
  </div>
  <script src="admin_chat.js"></script>
</body>
</html>
