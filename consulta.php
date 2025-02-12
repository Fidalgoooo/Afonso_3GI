<?php
require 'db.php'; // Inclui a conexão ao banco de dados
include './scripts/menu.php';

$reserva = null; // Inicializa a variável da reserva
$error = null; // Inicializa a variável para mensagens de erro

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_reserva = trim($_POST['codigo_reserva']); // Obtém e limpa o código da reserva

    if (!empty($codigo_reserva)) {
        // Consulta a reserva no banco de dados pelo código
        $sql = "SELECT * FROM reservas WHERE codigo_reserva = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $codigo_reserva);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $reserva = $result->fetch_assoc(); // Obter os dados da reserva
            } else {
                $error = "Código de reserva não encontrado.";
            }

            $stmt->close();
        } else {
            $error = "Erro ao preparar a consulta: " . $conn->error;
        }
    } else {
        $error = "Por favor, insira um código de reserva.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Reserva</title>
    <link rel="stylesheet" href="css/consulta.css">

</head>
<body>
<div class="consulta-reserva-wrapper">
    <div class="reserva-container">
        <h2>Consultar Reserva</h2>
        <form method="POST">
            <input type="text" name="codigo_reserva" placeholder="Insira o código da reserva" required>
            <button type="submit">Verificar</button>
        </form>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if ($reserva): ?>
            <div class="success">
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($reserva['nome']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($reserva['email']); ?></p>
                <p><strong>Contacto:</strong> <?php echo htmlspecialchars($reserva['contacto']); ?></p>
                <p><strong>Data de Início:</strong> <?php echo htmlspecialchars($reserva['data_inicio']); ?></p>
                <p><strong>Data de Fim:</strong> <?php echo htmlspecialchars($reserva['data_fim']); ?></p>
                <p><strong>Total Pago:</strong> <?php echo number_format($reserva['preco_total'], 2, ',', '.') . " €"; ?></p>
                <p><strong>Método de Pagamento:</strong> <?php echo htmlspecialchars($reserva['metodo_pagamento']); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
    <div>
        <?php include './scripts/footer.php'; ?>
    </div>
</body>
</html>

