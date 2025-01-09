<?php
session_start();
include 'db.php'; // Inclui a ligação à base de dados

// Verifica se a reserva foi completada com sucesso
if (!isset($_SESSION['reserva_sucesso']) || $_SESSION['reserva_sucesso'] !== true) {
    // Redireciona para a página inicial ou outra página relevante se não houver reserva concluída
    header("Location: index.php");
    exit;
}

// Obtém os detalhes da reserva da sessão
$reserva = $_SESSION['dados_reserva'] ?? [];

// Regista o log
if (!empty($reserva)) {
    $id_utilizador = $_SESSION['user_id']; // Obtém o ID do utilizador autenticado
    $acao = "Reserva confirmada";
    $descricao = "Reserva confirmada para o carro {$reserva['carro']} de {$reserva['data_inicio']} a {$reserva['data_fim']}. Preço total: {$reserva['preco_total']}€.";
    
    // Função para registrar o log
    registarLog($conn, $id_utilizador, $acao, $descricao);
}

// Limpar variáveis de sessão relacionadas à reserva
unset(
    $_SESSION['etapa'],
    $_SESSION['id_carro'],
    $_SESSION['data_inicio'],
    $_SESSION['data_fim'],
    $_SESSION['nome'],
    $_SESSION['email'],
    $_SESSION['contacto'],
    $_SESSION['metodo_pagamento'],
    $_SESSION['dados_reserva'],
    $_SESSION['reserva_sucesso']
);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Confirmada</title>
    <link rel="stylesheet" href="css/confirmacao.css">
</head>
<body>
    <div class="container">
        <h1>Reserva Confirmada!</h1>
        <p>Obrigado por fazer a sua reserva. Aqui estão os detalhes:</p>

        <div class="reserva-detalhes">
            <p><strong>Nome:</strong> <?= htmlspecialchars($reserva['nome'] ?? 'N/A') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($reserva['email'] ?? 'N/A') ?></p>
            <p><strong>Contacto:</strong> <?= htmlspecialchars($reserva['contacto'] ?? 'N/A') ?></p>
            <p><strong>Carro:</strong> <?= htmlspecialchars($reserva['carro'] ?? 'N/A') ?></p>
            <p><strong>Data de Início:</strong> <?= htmlspecialchars($reserva['data_inicio'] ?? 'N/A') ?></p>
            <p><strong>Data de Fim:</strong> <?= htmlspecialchars($reserva['data_fim'] ?? 'N/A') ?></p>
            <p><strong>Método de Pagamento:</strong> <?= htmlspecialchars($reserva['metodo_pagamento'] ?? 'N/A') ?></p>
            <p><strong>Preço Total:</strong> €<?= number_format($reserva['preco_total'] ?? 0, 2) ?></p>
        </div>

        <a href="index.php" class="btn">Voltar ao Início</a>
    </div>
</body>
</html>
