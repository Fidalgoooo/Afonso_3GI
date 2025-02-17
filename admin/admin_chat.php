<?php
session_start();
include '../db.php';

// Verificar se o admin está autenticado
if (!isset($_SESSION['user_permission']) || ($_SESSION['user_permission'] !== 'adm' && $_SESSION['user_permission'] !== 'chiefadmin')) {
    header("Location: ../login.php");
    exit;
}

// Buscar mensagens de todos os clientes
$msg_stmt = $conn->prepare("
    SELECT sc.id, sc.cliente_id, u.nome AS cliente_nome, sc.mensagem, sc.enviado_por, sc.enviado_em 
    FROM suporte_chat sc
    JOIN utilizadores u ON sc.cliente_id = u.id_utilizador
    ORDER BY sc.enviado_em ASC");
$msg_stmt->execute();
$msg_result = $msg_stmt->get_result();
$mensagens = $msg_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat de Suporte - Admin</title>
</head>
<body>
    <h3>Painel de Suporte - Administrador</h3>
    
    <div id="chat-box">
        <?php foreach ($mensagens as $msg): ?>
            <p><strong><?php echo ($msg['enviado_por'] == 'cliente') ? $msg['cliente_nome'] : 'Admin'; ?>:</strong> 
                <?php echo htmlspecialchars($msg['mensagem']); ?> 
                <small>(<?php echo $msg['enviado_em']; ?>)</small>
            </p>
        <?php endforeach; ?>
    </div>

    <h4>Responder a um Cliente</h4>
    <form action="admin_chat.php" method="POST">
        <select name="cliente_id" required>
            <option value="">Escolha o Cliente</option>
            <?php 
                $clientes_stmt = $conn->prepare("SELECT DISTINCT cliente_id, u.nome FROM suporte_chat sc JOIN utilizadores u ON sc.cliente_id = u.id_utilizador");
                $clientes_stmt->execute();
                $clientes_result = $clientes_stmt->get_result();
                while ($cliente = $clientes_result->fetch_assoc()):
            ?>
                <option value="<?php echo $cliente['cliente_id']; ?>"><?php echo $cliente['nome']; ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="mensagem" placeholder="Digite sua resposta..." required>
        <button type="submit">Enviar</button>
    </form>

    <br>
    <a href="../admin/index.php">Voltar</a>
</body>
</html>

<?php
// Processar resposta do admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mensagem'], $_POST['cliente_id'])) {
    $mensagem = trim($_POST['mensagem']);
    $cliente_id = intval($_POST['cliente_id']);

    if (!empty($mensagem)) {
        $stmt = $conn->prepare("INSERT INTO suporte_chat (cliente_id, mensagem, enviado_por) VALUES (?, ?, 'admin')");
        $stmt->bind_param("is", $cliente_id, $mensagem);
        $stmt->execute();
        header("Location: admin_chat.php"); // Atualiza a página após enviar mensagem
        exit();
    }
}
?>
