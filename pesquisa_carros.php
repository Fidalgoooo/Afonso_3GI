<?php
session_start();
include 'db.php';
include 'user_menu.php';

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
$sql = "SELECT * 
        FROM carros 
        WHERE id_carro NOT IN (
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
    <title>Carros Disponíveis</title>
    <link rel="stylesheet" href="css/pesquisaCarros.css">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <h1>Carros Disponíveis</h1>
    <p>Local de retirada: <?= htmlspecialchars($local_retirada ?? 'Não especificado') ?></p>
    <p>Local de devolução: <?= htmlspecialchars($local_devolucao ?? 'Não especificado') ?></p>
    <p>Datas: <?= htmlspecialchars($data_retirada ?? 'Não especificada') ?> a <?= htmlspecialchars($data_devolucao ?? 'Não especificada') ?></p>

    <div class="car-list">
        <?php if (!empty($result) && $result->num_rows > 0): ?>
            <?php while ($carro = $result->fetch_assoc()): ?>
                <div class="car-card">
                    <img 
                        src="<?= htmlspecialchars($carro['imagem']) ?>" 
                        alt="<?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?>" 
                        class="car-image">
                    <h2><?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?></h2>
                    <p class="price">Preço por dia: €<?= number_format($carro['preco_dia'], 2) ?></p>
                    <a 
                        href="detalhes_carro.php?id=<?= $carro['id_carro'] ?>&data_retirada=<?= htmlspecialchars($data_retirada) ?>&data_devolucao=<?= htmlspecialchars($data_devolucao) ?>" 
                        class="btn-reserve"
                        aria-label="Reservar <?= htmlspecialchars($carro['marca'] . ' ' . $carro['modelo']) ?>">
                        Reservar
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Não há carros disponíveis para as datas selecionadas.</p>
            <p>Tente alterar as datas ou contacte-nos para obter mais informações.</p>
        <?php endif; ?>
    </div>
</body>
</html>