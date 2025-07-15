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
            <?php
            $imagem = isset($carro['imagem']) && !empty($carro['imagem']) ? "admin/" . $carro['imagem'] : "imagens/default-car.png";
            ?>
            <img src="<?= htmlspecialchars($imagem) ?>" alt="Imagem do carro">
            <h2><?= htmlspecialchars($carro['marca']) ?> - <?= htmlspecialchars($carro['modelo']) ?></h2>
            <p>Preço por dia: €<?= number_format($carro['preco_dia'], 2) ?></p>
        </div>

        <?php
        // Buscar nome e email do utilizador logado
        $nome_autenticado = '';
        $email_autenticado = '';

        if (isset($_SESSION['user_id'])) {
            if (!isset($_SESSION['nome_cliente']) || !isset($_SESSION['email'])) {
                $id = $_SESSION['user_id'];
                $stmt = $conn->prepare("SELECT nome, email FROM utilizadores WHERE id_utilizador = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $_SESSION['nome_cliente'] = $row['nome'];
                    $_SESSION['email'] = $row['email'];
                }
                $stmt->close();
            }

            $nome_autenticado = $_SESSION['nome_cliente'] ?? '';
            $email_autenticado = $_SESSION['email'] ?? '';
        }
        ?>

        <?php if ($etapa === 1): ?>
            <form method="POST">
                <h3>Dados da Reserva</h3>

                <!-- Nome e Contacto -->
                <div class="row">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($nome_autenticado) ?>" required>
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
                        <input type="email" name="email" id="email" value="<?= htmlspecialchars($email_autenticado) ?>" required>
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

            <div class="resumo-pagamento">
                <h3>Confirmação da Reserva</h3>
                    <div class="resumo-grid">
                        <div><span>Nome:</span><p><?= htmlspecialchars($_SESSION['nome'] ?? 'N/A') ?></p></div>
                        <div><span>Email:</span><p><?= htmlspecialchars($_SESSION['email'] ?? 'N/A') ?></p></div>
                        <div><span>Contacto:</span><p><?= htmlspecialchars($_SESSION['contacto'] ?? 'N/A') ?></p></div>
                        <div><span>Método de Pagamento:</span><p>PayPal</p></div>
                        <div><span>Data de Início:</span>
                            <p><?= !empty($_SESSION['data_inicio']) ? htmlspecialchars(date('d/m/Y', strtotime($_SESSION['data_inicio']))) : 'N/A' ?></p>
                        </div>
                        <div><span>Data de Fim:</span>
                            <p><?= !empty($_SESSION['data_fim']) ? htmlspecialchars(date('d/m/Y', strtotime($_SESSION['data_fim']))) : 'N/A' ?></p>
                        </div>
                    </div>
                <div class="row" style="width: 100%;">
                    <div class="total">
                        <span>Preço Total:</span>
                        <p>€<?= number_format($preco_total, 2, ',', '.') ?></p>
                    </div>

                    <style>
                        .total {
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            width: 100%;
                            background: #f2f2f2;
                            padding: 12px 20px;
                            border-radius: 8px;
                            font-weight: bold;
                            color: #007bff;
                            box-sizing: border-box;
                            font-size: 16px;
                        }
                    </style>
                </div>

                <form method="POST">
                    <button type="submit" class="btn-pagamento">Ir para o Pagamento</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div>
        <?php include './scripts/footer.php'; ?>
    </div>
            <link rel="stylesheet" href="global.css">

</body>
</html>
