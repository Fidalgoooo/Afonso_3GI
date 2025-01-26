<?php
session_start();
require 'db.php'; // Inclui a conexão ao banco de dados

// Verificar se os dados da sessão existem
if (!isset($_SESSION['dados_reserva']) || empty($_SESSION['dados_reserva'])) {
    die("<p class='error-message'>Erro: Dados da reserva não encontrados. Volte à página de reserva.</p>");
}

// Obter os dados da reserva
$reserva = $_SESSION['dados_reserva'];

// Gerar um número aleatório de 6 caracteres (identificador único da reserva)
$codigo_reserva = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

// Inserir os dados da reserva na base de dados, incluindo o código da reserva
$sql = "INSERT INTO reservas (id_carro, nome, email, contacto, data_inicio, data_fim, metodo_pagamento, preco_total, codigo_reserva) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param(
        "issssssds", // Tipos dos parâmetros: i (int), s (string), d (double)
        $reserva['id_carro'],
        $reserva['nome'],
        $reserva['email'],
        $reserva['contacto'],
        $reserva['data_inicio'],
        $reserva['data_fim'],
        $reserva['metodo_pagamento'],
        $reserva['preco_total'],
        $codigo_reserva
    );

    if (!$stmt->execute()) {
        die("<p class='error-message'>Erro ao salvar a reserva: " . $stmt->error . "</p>");
    }

    $stmt->close();
} else {
    die("<p class='error-message'>Erro ao preparar a consulta: " . $conn->error . "</p>");
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Pagamento</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #fff;
            color: #333;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #007bff;
        }

        .success-message {
            font-size: 18px;
            color: #28a745;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .details {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 15px 20px;
            text-align: left;
            margin-top: 20px;
        }

        .details p {
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
        }

        .details p span {
            font-weight: 400;
            color: #555;
        }

        .btn {
            margin-top: 20px;
            display: inline-block;
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            background: #007bff;
            color: #fff;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #0056b3;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Confirmação de Pagamento</h1>
        <p class="success-message">O seu pagamento foi processado com sucesso!</p>
        <div class="details">
            <p><strong>Código da Reserva:</strong> <span><?php echo $codigo_reserva; ?></span></p>
            <p><strong>Nome do Cliente:</strong> <span><?php echo htmlspecialchars($reserva['nome']); ?></span></p>
            <p><strong>Email:</strong> <span><?php echo htmlspecialchars($reserva['email']); ?></span></p>
            <p><strong>Total Pago:</strong> <span><?php echo number_format($reserva['preco_total'], 2, ',', '.') . " €"; ?></span></p>
        </div>
        <a href="index.php" class="btn">Voltar à Página Inicial</a>
    </div>
</body>
</html>
