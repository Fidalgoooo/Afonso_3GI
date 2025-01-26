<?php
session_start();
include '../db.php';

// Função para registar logs
if (!function_exists('registarLog')) {
    function registarLog($conn, $id_utilizador, $acao, $descricao) {
        $sql = "INSERT INTO logs (id_utilizador, acao, descricao) VALUES (?, ?, ?)";
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

// Obtém o ID do utilizador logado
$id_utilizador = $_SESSION['user_id'];

// Verifica ações enviadas pelo formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'eliminar') {
            $id_reserva = intval($_POST['id_reserva']);
            $sql = "DELETE FROM reservas WHERE id_reserva = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_reserva);
            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Reserva com ID {$id_reserva} foi eliminada.";
            registarLog($conn, $id_utilizador, 'Eliminar', $descricao);
        } elseif ($action === 'adicionar') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $contacto = $_POST['contacto'];
            $data_inicio = $_POST['data_inicio'];
            $data_fim = $_POST['data_fim'];
            $metodo_pagamento = $_POST['metodo_pagamento'];
            $preco_total = floatval($_POST['preco_total']);
            $id_carro = intval($_POST['id_carro']);
            $data_registo = date('Y-m-d H:i:s');

            $sql = "INSERT INTO reservas (nome, email, contacto, data_inicio, data_fim, metodo_pagamento, preco_total, id_carro, data_registo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssdis", $nome, $email, $contacto, $data_inicio, $data_fim, $metodo_pagamento, $preco_total, $id_carro, $data_registo);
            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Nova reserva adicionada: Nome: {$nome}, Email: {$email}, Contacto: {$contacto}, Data Início: {$data_inicio}, Data Fim: {$data_fim}, Preço Total: {$preco_total}€, Veículo ID: {$id_carro}.";
            registarLog($conn, $id_utilizador, 'Adicionar', $descricao);
        } elseif ($action === 'editar') {
            $id_reserva = intval($_POST['id_reserva']);
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $contacto = $_POST['contacto'];

            $sql = "UPDATE reservas SET nome = ?, email = ?, contacto = ? WHERE id_reserva = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nome, $email, $contacto, $id_reserva);
            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Reserva com ID {$id_reserva} foi editada: Nome: {$nome}, Email: {$email}, Contacto: {$contacto}.";
            registarLog($conn, $id_utilizador, 'Editar', $descricao);
        }
    }
}

// Consulta as reservas
$sql = "SELECT r.id_reserva, r.nome, r.email, r.contacto, r.data_inicio, r.data_fim, 
           r.metodo_pagamento, r.preco_total, r.data_registo, c.marca, c.modelo
    FROM reservas r
    LEFT JOIN carros c ON r.id_carro = c.id_carro
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gerir Reservas</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
    <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="utilizadores.php"><i class="fas fa-users"></i> Utilizadores</a></li>
                <li><a href="condutores.php"><i class="fas fa-id-card"></i> Condutores</a></li>
                <li><a href="veiculos.php"><i class="fas fa-car"></i> Veículos</a></li>
                <li><a href="reservas.php"><i class="fas fa-book"></i> Reservas</a></li>
                <li><a href="resets.php"><i class="fas fa-lock"></i> Password Resets</a></li>
                <li><a href="logs.php"><i class="fas fa-cogs"></i> Logs</a></li>
                <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="dashboard">
            <h1>Reservas</h1>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Contacto</th>
                        <th>Data Início</th>
                        <th>Data Fim</th>
                        <th>Veículo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                    <form method="post">
                            <td><input type="text" name="nome" value="<?php echo htmlspecialchars($row['nome']); ?>" required></td>
                            <td><input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required></td>
                            <td><input type="text" name="contacto" value="<?php echo htmlspecialchars($row['contacto']); ?>" required></td>
                            <td><?php echo htmlspecialchars($row['data_inicio']); ?></td>
                            <td><?php echo htmlspecialchars($row['data_fim']); ?></td>
                            <td>
                                <?php 
                                if (isset($row['marca']) && isset($row['modelo'])) {
                                    echo htmlspecialchars($row['marca'] . ' ' . $row['modelo']);
                                } else {
                                    echo "Veículo não associado";
                                }
                                ?>
                            </td>
                            <td>
                                <input type="hidden" name="id_reserva" value="<?php echo $row['id_reserva']; ?>">
                                <button type="submit" name="action" value="editar">Guardar</button>
                                <button type="submit" name="action" value="eliminar">Eliminar</button>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
