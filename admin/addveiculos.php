<?php
session_start();
include '../db.php';

// Função para registar logs
if (!function_exists('registarLog')) {
    function registarLog($conn, $id_utilizador, $acao, $descricao) {
        $sql = "INSERT INTO logs (id_utilizador, acao, descricao, data_hora) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Erro na preparação da query de log: " . $conn->error);
        }

        $stmt->bind_param("iss", $id_utilizador, $acao, $descricao);

        if (!$stmt->execute()) {
            error_log("Erro ao registar log: " . $stmt->error);
        }

        $stmt->close();
    }
}

// Verifica se o utilizador está logado e é administrador
if (!isset($_SESSION['user_permission']) || ($_SESSION['user_permission'] !== 'adm' && $_SESSION['user_permission'] !== 'chiefadmin')) {
    header("Location: ../login.php");
    exit;
}

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'adicionar') {
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $ano = intval($_POST['ano']);
    $preco_dia = floatval($_POST['preco_dia']);
// Diretório onde vais guardar as imagens
$diretorio_upload = "uploads/";
if (!is_dir($diretorio_upload)) {
    mkdir($diretorio_upload, 0755, true);
}

$imagem = "";

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
    $nome_ficheiro = basename($_FILES['imagem']['name']);
    $caminho_final = $diretorio_upload . time() . "_" . $nome_ficheiro;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho_final)) {
        $imagem = $caminho_final;
    } else {
        $error_message = "Erro ao fazer upload da imagem.";
    }
} else {
    $error_message = "Nenhuma imagem foi enviada.";
}

    $combustivel = $_POST['combustivel'];
    $portas = intval($_POST['portas']);
    $distancia = intval($_POST['distancia']);
    $caixa = $_POST['caixa'];

    $ar_condicionado = isset($_POST['ar_condicionado']) ? 1 : 0;
    $assentos = intval($_POST['assentos']);

    $abs = isset($_POST['abs']) ? 1 : 0;
    $airbags = isset($_POST['airbags']) ? 1 : 0;
    $controle_cruzeiro = isset($_POST['controle_cruzeiro']) ? 1 : 0;

    $conn->begin_transaction();

    try {
        // Inserir veículo na tabela carros
        $sql = "INSERT INTO carros (marca, modelo, ano, preco_dia, imagem) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssids", $marca, $modelo, $ano, $preco_dia, $imagem);
        $stmt->execute();
        $id_carro = $stmt->insert_id;

        // Inserir informações adicionais na tabela informacoescarro
        $sql = "INSERT INTO informacoescarro (id_carro, combustivel, portas, distancia, caixa) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isiss", $id_carro, $combustivel, $portas, $distancia, $caixa);
        $stmt->execute();

        // Inserir características na tabela caracteristicascarro
        $sql = "INSERT INTO caracteristicascarro (id_carro, ar_condicionado, assentos) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $id_carro, $ar_condicionado, $assentos);
        $stmt->execute();

        // Inserir segurança na tabela segurancacarro
        $sql = "INSERT INTO segurancacarro (id_carro, abs, airbags, controle_cruzeiro) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiii", $id_carro, $abs, $airbags, $controle_cruzeiro);
        $stmt->execute();

        $conn->commit();

        // Registar log
        $id_utilizador = $_SESSION['user_id']; // ID do administrador logado
        $descricao = "Novo veículo adicionado: Marca: $marca, Modelo: $modelo, Ano: $ano, Preço/Dia: $preco_dia.";
        registarLog($conn, $id_utilizador, 'Adicionar', $descricao);

        $success_message = "Veículo adicionado com sucesso!";

        echo "<script>
            setTimeout(function() {
                window.location.href = 'veiculos.php';
            }, 1000); // Redireciona após 1 segundo (1000ms)
        </script>";

    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Erro ao adicionar o veículo: " . $e->getMessage();
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Veículo</title>
    <link rel="stylesheet" href="./css/addveiculos.css">
    <link rel="stylesheet" href="./css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="utilizadores.php"><i class="fas fa-users"></i> Utilizadores</a></li>
            <li><a href="condutores.php"><i class="fas fa-id-card"></i> Condutores</a></li>
            <li><a href="veiculos.php"><i class="fas fa-car"></i> Veículos</a></li>
            <li><a href="reservas.php"><i class="fas fa-book"></i> Reservas</a></li>
            <li><a href="admin_chat.php"><i class="fas fa-phone"></i> Chat</a></li>
            <?php if ($_SESSION['user_permission'] === 'chiefadmin'): ?>
                <li><a href="logs.php"><i class="fas fa-cogs"></i> Logs</a></li>
            <?php endif; ?>
        </ul>
    </aside>

    <!-- Conteúdo e header à direita -->
    <div style="flex: 1; display: flex; flex-direction: column;">

      <?php include 'header_admin.php'; ?>

        <main class="dashboard">
            <h1>Adicionar Novo Veículo</h1>
            
            <?php if (isset($success_message)): ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>

<form method="post" enctype="multipart/form-data">
            <!-- Dados básicos -->
            <h3>Dados do Veículo</h3>
            <div class="form-group">
                <label for="marca">Marca:</label>
                <input type="text" id="marca" name="marca" placeholder="Marca" required>
            </div>

            <div class="form-group">    
                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo" placeholder="Modelo" required>
            </div>

            <div class="form-group">
                <label for="ano">Ano:</label>
                <input type="number" id="ano" name="ano" placeholder="Ano" required>
            </div>

            <div class="form-group">
                <label for="preco_dia">Preço/Dia:</label>
                <input type="number" id="preco_dia" name="preco_dia" placeholder="Preço/Dia" step="0.01" required>
            </div>

<div class="form-group">
    <label for="imagem">Selecionar Imagem:</label>
    <input type="file" id="imagem" name="imagem" accept="image/*" required>
</div>


            <!-- Informações do carro -->
            <h3>Informações do Carro</h3>
            <div class="form-group">
                <label for="combustivel">Combustível:</label>
                <input type="text" id="combustivel" name="combustivel" placeholder="Combustível" required>
            </div>

            <div class="form-group">
                <label for="portas">Portas:</label>
                <input type="number" id="portas" name="portas" placeholder="Portas" required>
            </div>

            <div class="form-group">
                <label for="distancia">Quilómetros (km):</label>
                <input type="number" id="distancia" name="distancia" placeholder="Distância" required>
            </div>

            <div class="form-group">
                <label for="caixa">Caixa:</label>
                <input type="text" id="caixa" name="caixa" placeholder="Caixa" required>
            </div>

            <!-- Características -->
            <h3>Características</h3>
            <div class="checkbox-group">
                <label for="assentos">Assentos:</label>
                <input type="number" id="assentos" name="assentos" placeholder="Assentos" required>
                <label for="ar_condicionado"><input type="checkbox" id="ar_condicionado" name="ar_condicionado"> Ar Condicionado</label>
            </div>

            <!-- Segurança -->
            <h3>Segurança</h3>
            <div class="checkbox-group">
                <label for="abs"><input type="checkbox" id="abs" name="abs"> ABS</label>
                <label for="airbags"><input type="checkbox" id="airbags" name="airbags"> Airbags</label>
                <label for="controle_cruzeiro"><input type="checkbox" id="controle_cruzeiro" name="controle_cruzeiro"> Controle de Cruzeiro</label>
            </div>

            <button type="submit" name="action" value="adicionar">Adicionar</button>
        </form>
        </main>
    </div>
</body>
</html>
