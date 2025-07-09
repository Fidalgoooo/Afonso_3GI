<?php
session_start();
include '../db.php';

$cliente_id = $_SESSION['user_id'] ?? null;
$html = '';

if ($cliente_id) {
    $stmt = $conn->prepare("SELECT * FROM suporte_chat WHERE cliente_id = ? ORDER BY enviado_em ASC");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $classe = match($row['enviado_por']) {
            'cliente' => 'mensagem-cliente',
            'admin'   => 'mensagem-admin',
            'bot'     => 'mensagem-bot',
            default   => ''
        };

        $autor = match($row['enviado_por']) {
            'cliente' => 'Você',
            'admin'   => 'Admin',
            'bot'     => 'Bot',
            default   => 'Sistema'
        };

        $mensagem = nl2br(htmlspecialchars($row['mensagem'])); // respeitar \n
        $html .= "
            <div class=\"$classe\">
                <div><strong>$autor:</strong></div>
                <div>$mensagem</div>
            </div>
        ";
    }
}

echo $html;
