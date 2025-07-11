<?php
session_start();
include 'db.php';
include './scripts/menu.php';

$data_retirada = $_GET['data_retirada'] ?? $_SESSION['pesquisa']['data_retirada'] ?? '';
$data_devolucao = $_GET['data_devolucao'] ?? $_SESSION['pesquisa']['data_devolucao'] ?? '';

if (!empty($data_retirada) && !empty($data_devolucao)) {
  $_SESSION['pesquisa']['data_retirada'] = $data_retirada;
  $_SESSION['pesquisa']['data_devolucao'] = $data_devolucao;
}

unset(
  $_SESSION['data_inicio'],
  $_SESSION['data_fim'],
  $_SESSION['id_carro'],
  $_SESSION['nome'],
  $_SESSION['email'],
  $_SESSION['contacto'],
  $_SESSION['metodo_pagamento'],
  $_SESSION['dados_reserva'],
  $_SESSION['reserva_sucesso']
);

$local_retirada = $_GET['local_retirada'] ?? '';
$local_devolucao = $_GET['local_devolucao'] ?? '';

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
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aluguer de Carros - Resultados</title>
<!-- <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet"> -->
<link rel="stylesheet" href="css/pesquisaCarros.css">

</head>
<body>
  <div class="container">
    <div class="reserva-info">
      <div class="item">
        <label>Origem</label>
        <p><?= htmlspecialchars($local_retirada) ?></p>
      </div>
      <div class="item">
        <label>Destino</label>
        <p><?= htmlspecialchars($local_devolucao) ?></p>
      </div>
<div class="item">
  <label>Data de Recolha</label>
  <p><?= (new DateTime($data_retirada))->format('d/m/Y') ?></p>
</div>
<div class="item">
  <label>Data de Entrega</label>
  <p><?= (new DateTime($data_devolucao))->format('d/m/Y') ?></p>
</div>

    </div>

    <?php
    $data_retirada_obj = new DateTime($data_retirada ?: 'now');
    $data_devolucao_obj = new DateTime($data_devolucao ?: 'now');
    $diferenca_dias = $data_retirada_obj->diff($data_devolucao_obj)->days;
    $diferenca_dias = max($diferenca_dias, 1);

    while ($carro = $result->fetch_assoc()): ?>
    <div class="car-card">
      <img src="admin/<?= htmlspecialchars($carro['imagem']) ?>" alt="<?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?>" class="car-image">
      <div class="car-details">
        <h3><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?></h3>
        <div class="car-info">
          <ul>
            <li><strong>Assentos:</strong> <?= htmlspecialchars($carro['assentos']) ?></li>
            <li><strong>Caixa:</strong> <?= htmlspecialchars($carro['caixa']) ?></li>
            <li><strong>Combustível:</strong> <?= htmlspecialchars($carro['combustivel']) ?></li>
            <ul class="features">
              <li>✔ Quilometragem ilimitada</li>
              <li>✔ Cancelamento gratuito até 48h antes do levantamento</li>
            </ul>
          </ul>
        </div>
      </div>
      <div class="price-section">
        <p class="price-label">DESDE</p>
        <h2><?= number_format($carro['preco_dia'], 2, ',', ' ') ?> € / dia</h2>
        <small>TOTAL <?= number_format($carro['preco_dia'] * $diferenca_dias, 2, ',', ' ') ?> €</small>
        <a href="detalhes_carro.php?id=<?= $carro['id_carro'] ?>&data_retirada=<?= urlencode($data_retirada) ?>&data_devolucao=<?= urlencode($data_devolucao) ?>" class="btn-reserve">Selecionar</a>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</body>
</html>
