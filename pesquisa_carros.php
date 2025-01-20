<?php
session_start();
include 'db.php';
include './scripts/menu.php';

// Recuperar as datas do URL ou da sessão
$data_retirada = $_GET['data_retirada'] ?? $_SESSION['pesquisa']['data_retirada'] ?? '';
$data_devolucao = $_GET['data_devolucao'] ?? $_SESSION['pesquisa']['data_devolucao'] ?? '';

// Se as datas existirem no URL, guardá-las na sessão
if (!empty($data_retirada) && !empty($data_devolucao)) {
    $_SESSION['pesquisa']['data_retirada'] = $data_retirada;
    $_SESSION['pesquisa']['data_devolucao'] = $data_devolucao;
}

// Limpar dados relacionados com reservas ao fazer nova pesquisa
unset($_SESSION['data_inicio'], $_SESSION['data_fim'], $_SESSION['id_carro'], $_SESSION['nome'], $_SESSION['email'], $_SESSION['contacto'], $_SESSION['metodo_pagamento'], $_SESSION['dados_reserva'], $_SESSION['reserva_sucesso']);

// Obter os dados do formulário
$local_retirada = $_GET['local_retirada'];
$local_devolucao = $_GET['local_devolucao'];
$data_retirada = $_GET['data_retirada'];
$data_devolucao = $_GET['data_devolucao'];

// Consulta SQL para buscar carros disponíveis
$sql = "SELECT c.id_carro, c.marca, c.modelo, c.preco_dia, c.imagem, 
               i.combustivel, i.caixa, car.assentos
        FROM carros c
        JOIN informacoescarro i ON c.id_carro = i.id_carro
        LEFT JOIN caracteristicascarro car ON c.id_carro = car.id_carro
        WHERE c.id_carro NOT IN (
            SELECT id_carro 
            FROM reservas 
            WHERE 
                (data_inicio <= ? AND data_fim >= ?) OR
                (data_inicio <= ? AND data_fim >= ?) OR
                (? BETWEEN data_inicio AND data_fim) OR
                (? BETWEEN data_inicio AND data_fim)
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssss", 
    $data_devolucao, $data_retirada, 
    $data_retirada, $data_devolucao, 
    $data_retirada, $data_devolucao
);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carros Disponíveis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"> <!-- CSS Geral -->
    <link rel="stylesheet" href="css/pesquisaCarros.css"> <!-- CSS dos Carros -->
</head>
<body>

    <div class="container">
        <h1 class="my-4">Carros Disponíveis</h1>
        <p>Local de retirada: <?= htmlspecialchars($local_retirada ?? 'Não especificado') ?></p>
        <p>Local de devolução: <?= htmlspecialchars($local_devolucao ?? 'Não especificado') ?></p>
        <p>Datas: <?= htmlspecialchars($data_retirada ?? 'Não especificada') ?> a <?= htmlspecialchars($data_devolucao ?? 'Não especificada') ?></p>
        
        <div>
            <?php while ($carro = $result->fetch_assoc()): ?>
            <div class="car-card">
                <img src="<?php echo $carro['imagem']; ?>" alt="<?php echo $carro['marca'] . ' ' . $carro['modelo']; ?>" class="car-image">
                <div class="car-details">
                    <h3><?php echo $carro['marca'] . ' ' . $carro['modelo']; ?></h3>
                    <div class="car-info">
                        <ul>
                            <li><strong>Assentos:</strong> <?php echo $carro['assentos']; ?></li>
                            <li><strong>Caixa:</strong> <?php echo $carro['caixa']; ?></li>
                            <li><strong>Combustível:</strong> <?php echo $carro['combustivel']; ?></li>
                            <ul class="features">
                                <li class="feature-green">✔ Quilometragem ilimitada</li>
                                <li class="feature-green">✔ Cancelamento gratuito até 48h antes do levantamento</li>
                            </ul>
                        </ul>
                    </div>
                </div>
                <div class="price-section">
                    <p class="price-label">DESDE</p>
                    <h2 class="price-per-day"><?php echo number_format($carro['preco_dia'], 2, ',', ' '); ?> € / dia</h2>
                    <small class="total-price">TOTAL <?php echo number_format($carro['preco_dia'] * 6, 2, ',', ' '); ?> €</small>
                    <a 
                        href="detalhes_carro.php?id=<?= $carro['id_carro'] ?>&data_retirada=<?= htmlspecialchars($data_retirada) ?>&data_devolucao=<?= htmlspecialchars($data_devolucao) ?>" 
                        class="btn-reserve"
                        aria-label="Reservar <?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?>">
                        Selecione
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    
</body>
</html>

