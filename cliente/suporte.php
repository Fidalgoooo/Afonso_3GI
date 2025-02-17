<?php
session_start();
include '../db.php';

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cliente_id = $_SESSION['user_id']; // Corrigido para garantir o ID correto do utilizador

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $assunto = trim($_POST['assunto']);
    $mensagem = trim($_POST['mensagem']);

    if (empty($assunto) || empty($mensagem)) {
        echo "Erro: Todos os campos são obrigatórios.";
    } else {
        // Inserir ticket na base de dados
        $stmt = $conn->prepare("INSERT INTO suporte_tickets (cliente_id, assunto, mensagem, status) VALUES (?, ?, ?, 'aberto')");
        $stmt->bind_param("iss", $cliente_id, $assunto, $mensagem);

        if ($stmt->execute()) {
            echo "Ticket aberto com sucesso!";
        } else {
            echo "Erro ao abrir ticket.";
        }
    }
}
?>

<form action="suporte.php" method="POST">
    <input type="text" name="assunto" placeholder="Assunto" required>
    <textarea name="mensagem" placeholder="Descreva seu problema" required></textarea>
    <button type="submit">Enviar</button>
</form>
