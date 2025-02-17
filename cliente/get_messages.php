<?php
session_start();
include '../db.php';

$cliente_id = $_SESSION['user_id'];

$msg_stmt = $conn->prepare("SELECT * FROM suporte_chat WHERE cliente_id = ? ORDER BY enviado_em ASC");
$msg_stmt->bind_param("i", $cliente_id);
$msg_stmt->execute();
$msg_result = $msg_stmt->get_result();
$mensagens = $msg_result->fetch_all(MYSQLI_ASSOC);

foreach ($mensagens as $msg) {
    echo "<p><strong>" . ($msg['enviado_por'] == 'cliente' ? 'Você' : 'Admin') . ":</strong> " . 
         htmlspecialchars($msg['mensagem']) . " <small>(" . $msg['enviado_em'] . ")</small></p>";
}
?>
