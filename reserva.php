<?php
session_start();
include 'db.php';
include './scripts/menu.php'; 

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = "reserva.php?id=" . ($_GET['id'] ?? null) . "&data_retirada=" . ($_GET['data_retirada'] ?? '') . "&data_devolucao=" . ($_GET['data_devolucao'] ?? '');
    header("Location: login.php");
    exit;
}

// Inicializar nova reserva automaticamente
if (!isset($_SESSION['etapa'])) {
    unset(
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
    $_SESSION['etapa'] = 1; // Define a primeira etapa
}

// Obter ID do carro e detalhes das datas
$id_carro = $_GET['id'] ?? $_SESSION['id_carro'] ?? null;
$data_retirada = $_GET['data_retirada'] ?? $_SESSION['pesquisa']['data_retirada'] ?? '';
$data_devolucao = $_GET['data_devolucao'] ?? $_SESSION['pesquisa']['data_devolucao'] ?? '';

// Salvar datas na sessão
$_SESSION['data_inicio'] = $data_retirada;
$_SESSION['data_fim'] = $data_devolucao;

if (!$id_carro) {
    die("Carro não especificado.");
}
$_SESSION['id_carro'] = $id_carro;

// Obter detalhes do carro
$sql = "SELECT marca, modelo, preco_dia, imagem FROM carros WHERE id_carro = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_carro);
$stmt->execute();
$carro = $stmt->get_result()->fetch_assoc();

// Determina a etapa atual
$etapa = $_SESSION['etapa'];

// Processar formulário de cada etapa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($etapa === 1) {
        // Processar dados da etapa 1
        $_SESSION['nome'] = $_POST['nome'];
        $_SESSION['email'] = $_POST['email'];
        $_SESSION['contacto'] = $_POST['contacto'];
        
        // Inserir dados na tabela condutores
        $sql = "INSERT INTO condutores (nome, email, contacto) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Erro na preparação da query: " . $conn->error);
        }

        $stmt->bind_param(
            "sss", // Tipos dos parâmetros: s (string)
            $_POST['nome'],
            $_POST['email'],
            $_POST['contacto']
        );

        if (!$stmt->execute()) {
            die("Erro ao inserir condutor: " . $stmt->error);
        }

        $stmt->close();

        // Avançar para a próxima etapa
        $_SESSION['etapa'] = 3;
        header("Location: reserva.php");
        exit;
    } elseif ($etapa === 3) {
        // Confirmar a reserva e calcular o preço total
        $data_inicio = $_SESSION['data_inicio'];
        $data_fim = $_SESSION['data_fim'];
        $dias = (strtotime($data_fim) - strtotime($data_inicio)) / (60 * 60 * 24);
        $preco_total = $dias * $carro['preco_dia'];
    
        // Inserir a reserva diretamente na base de dados
                
    
        // Salvar os detalhes da reserva na sessão para passar ao PayPal
        $_SESSION['dados_reserva'] = [
            'id_carro' => $id_carro, // Substitui por uma variável válida
            'nome' => $_SESSION['nome'] ?? 'N/A',
            'email' => $_SESSION['email'] ?? 'N/A',
            'contacto' => $_SESSION['contacto'] ?? 'N/A',
            'data_inicio' => $_SESSION['data_inicio'] ?? 'N/A',
            'data_fim' => $_SESSION['data_fim'] ?? 'N/A',
            'metodo_pagamento' => $_SESSION['metodo_pagamento'] ?? 'PayPal',
            'preco_total' => $preco_total ?? 0 // Certifica-te de que esta variável está calculada
        ];
        
        // Redirecionar para o PayPal
        
        header("Location: criar-pagamento.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva de Carro</title>
    <link rel="stylesheet" href="css/reserva.css">
</head>
<body>
    <div class="container">
        <div class="car-details">
            <!-- Detalhes do carro -->
            <img src="<?= htmlspecialchars($carro['imagem'] ?? 'imagens/default-car.png') ?>" alt="Imagem do carro">
            <h2><?= htmlspecialchars($carro['marca']) ?> - <?= htmlspecialchars($carro['modelo']) ?></h2>
            <p>Preço por dia: €<?= number_format($carro['preco_dia'], 2) ?></p>
        </div>

        <!-- Etapas do processo -->
        <?php if ($etapa === 1): ?>
            <form method="POST">
                <h3>Dados da Reserva</h3>

                <!-- Nome e Contacto -->
                <div class="row">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" name="nome" id="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="contacto">Contacto</label>
                        <input type="text" name="contacto" id="contacto" required>
                    </div>
                </div>

                <!-- Email e Datas -->
                <div class="row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="data_inicio">Data de Início</label>
                        <input type="date" id="data_inicio" value="<?= htmlspecialchars($_SESSION['data_inicio'] ?? '') ?>" readonly>
                        <input type="hidden" name="data_inicio" value="<?= htmlspecialchars($_SESSION['data_inicio'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="data_fim">Data de Fim</label>
                        <input type="date" id="data_fim" value="<?= htmlspecialchars($_SESSION['data_fim'] ?? '') ?>" readonly>
                        <input type="hidden" name="data_fim" value="<?= htmlspecialchars($_SESSION['data_fim'] ?? '') ?>">
                    </div>
                </div>

                <button type="submit">Próximo</button>
            </form>

        <?php elseif ($etapa === 3): ?>
            <?php
            $data_inicio = $_SESSION['data_inicio'];
            $data_fim = $_SESSION['data_fim'];
            $dias = (strtotime($data_fim) - strtotime($data_inicio)) / (60 * 60 * 24);
            $preco_total = $dias * $carro['preco_dia'];
            ?>
            <h3>Confirmação da Reserva</h3>
            <p><strong>Nome:</strong> <?= htmlspecialchars($_SESSION['nome'] ?? 'N/A') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email'] ?? 'N/A') ?></p>
            <p><strong>Contacto:</strong> <?= htmlspecialchars($_SESSION['contacto'] ?? 'N/A') ?></p>
            <p><strong>Data de Início:</strong> <?= htmlspecialchars($_SESSION['data_inicio'] ?? 'N/A') ?></p>
            <p><strong>Data de Fim:</strong> <?= htmlspecialchars($_SESSION['data_fim'] ?? 'N/A') ?></p>
            <p><strong>Método de Pagamento:</strong> PayPal</p>
            <p><strong>Preço Total:</strong> €<?= number_format($preco_total, 2) ?></p>

            <form method="POST">
                <button type="submit">Ir para o Pagamento</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div>
        <?php include './scripts/footer.php'; ?>
    </div>
</body>
</html>