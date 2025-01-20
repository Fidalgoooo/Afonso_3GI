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

// Obter os detalhes do carro a partir do ID
$id_carro = $_GET['id'] ?? null;

function bool_to_text($value) {
    return $value ? 'Sim' : 'Não';
}

if ($id_carro) {
    // Nova consulta com JOIN para obter todos os dados necessários em uma única linha
    $sql = "SELECT c.marca, c.modelo, c.preco_dia, c.imagem, 
                   i.caixa, i.combustivel, i.portas, i.distancia,
                   car.ar_condicionado, car.assentos,
                   seg.abs, seg.airbags, seg.controle_cruzeiro
            FROM carros c
            JOIN informacoescarro i ON c.id_carro = i.id_carro
            LEFT JOIN caracteristicascarro car ON i.id_carro = car.id_carro
            LEFT JOIN segurancacarro                                                                                                                                                                                                                                                                                                                                           seg ON i.id_carro = seg.id_carro
            WHERE c.id_carro = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_carro);
    $stmt->execute();
    $carro = $stmt->get_result()->fetch_assoc();

    if (!$carro) {
        die("Carro não encontrado.");
    }
} else {
    die("ID do carro não especificado.");
}


// Função para verificar a existência da chave e fornecer um valor padrão
function get_value($array, $key, $default = 'Não especificado') {
    return isset($array[$key]) && $array[$key] !== null ? htmlspecialchars($array[$key]) : $default;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Carro</title>
    <link rel="stylesheet" href="css/detalhe.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="image-container">
                <img src="<?= get_value($carro, 'imagem', 'img/logo.jpg') ?>" alt="Imagem do carro">
            </div>
            <div class="details">
                <h2>
                    <?= get_value($carro, 'marca') ?> 
                    <?php if (!empty(get_value($carro, 'modelo'))) : ?>
                         <?= get_value($carro, 'modelo') ?>
                    <?php endif; ?>
                </h2>
                <p class="price"><?= get_value($carro, 'preco_dia', '0.00') ?>€ / dia</p>
                <div class="spec-grid">
                    <div class="spec-card">
                        <h3>Caixa de Velocidades</h3>
                        <span><?= get_value($carro, 'caixa') ?></span>
                    </div>
                    <div class="spec-card">
                        <h3>Combustível</h3>
                        <span><?= get_value($carro, 'combustivel') ?></span>
                    </div>
                    <div class="spec-card">
                        <h3>Portas</h3>
                        <span><?= get_value($carro, 'portas') ?></span>
                    </div>
                    <div class="spec-card">
                        <h3>Ar Condicionado</h3>
                        <span><?= bool_to_text(get_value($carro, 'ar_condicionado')) ?></span>
                    </div>
                    <div class="spec-card">
                        <h3>Assentos</h3>
                        <span><?= get_value($carro, 'assentos') ?></span>
                    </div>
                    <div class="spec-card">
                        <h3>Kilómetros</h3>
                        <span><?= get_value($carro, 'distancia', '0') ?> km</span>
                    </div>
                </div>
                <!-- Passar datas para reserva -->
                <a href="reserva.php?id=<?= $id_carro ?>&data_retirada=<?= $data_retirada ?>&data_devolucao=<?= $data_devolucao ?>" class="button">Alugar um carro</a>
            </div>
        </div>
        <div class="equipment">
            <h3>Equipamento Automóvel</h3>
            <ul>
                <li>ABS: <?= bool_to_text(get_value($carro, 'abs')) ?></li>
                <li>Airbags: <?= bool_to_text(get_value($carro, 'airbags')) ?></li>
                <li>Cruise Controle: <?= bool_to_text(get_value($carro, 'controle_cruzeiro')) ?></li>
            </ul>
        </div>
    </div>
    <div>
        <?php include './scripts/footer.php'; ?>
    </div>
</body>
</html>
