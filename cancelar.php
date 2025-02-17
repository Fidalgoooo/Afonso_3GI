<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Cancelado</title>
    <script>
        // Aguarda 5 segundos e redireciona para index.php
        setTimeout(function() {
            window.location.href = "index.php";
        }, 5000);
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
        }
    </style>
</head>
<body>

    <h2>O pagamento foi cancelado.</h2>
    <p>Volte à página de reserva para tentar novamente.</p>
    <p>Redirecionando para a página inicial em 5 segundos...</p>
    <?php
// Limpa variáveis de sessão relacionadas à reserva
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
</body>
</html>
