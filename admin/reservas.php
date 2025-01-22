<?php
session_start();
include '../db.php';

// Verifica se o utilizador está logado e é administrador
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'adm') {
    header("Location: ../login.php");
    exit;
}

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
        } elseif ($action === 'adicionar') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $contacto = $_POST['contacto'];
            $data_inicio = $_POST['data_inicio'];
            $data_fim = $_POST['data_fim'];
            $metodo_pagamento = $_POST['metodo_pagamento'];
            $preco_total = floatval($_POST['preco_total']);
            $data_registo = date('Y-m-d H:i:s');

            $sql = "INSERT INTO reservas (nome, email, contacto, data_inicio, data_fim, metodo_pagamento, preco_total, data_registo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssds", $nome, $email, $contacto, $data_inicio, $data_fim, $metodo_pagamento, $preco_total, $data_registo);
            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'editar') {
            $id_reserva = intval($_POST['id_reserva']);
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $contacto = $_POST['contacto'];
            $data_inicio = $_POST['data_inicio'];
            $data_fim = $_POST['data_fim'];

            $sql = "UPDATE reservas SET nome = ?, email = ?, contacto = ?, data_inicio = ?, data_fim = ? WHERE id_reserva = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $nome, $email, $contacto, $data_inicio, $data_fim, $id_reserva);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Consulta as reservas
$sql = "SELECT id_reserva, nome, email, contacto, data_inicio, data_fim, metodo_pagamento, preco_total, data_registo FROM reservas";
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
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="utilizadores.php"><i class="fas fa-users"></i> Utilizadores</a></li>
                <li><a href="condutores.php"><i class="fas fa-id-card"></i> Condutores</a></li>
                <li><a href="veiculos.php"><i class="fas fa-car"></i> Veículos</a></li>
                <li><a href="reservas.php"><i class="fas fa-book"></i> Reservas</a></li>
                <li><a href="password_resets.php"><i class="fas fa-lock"></i> Password Resets</a></li>
                <li><a href="logs.php"><i class="fas fa-cogs"></i> Logs</a></li>
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
                        <th>Método Pagamento</th>
                        <th>Preço Total</th>
                        <th>Data Registo</th>
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
                            <td><input type="date" name="data_inicio" value="<?php echo htmlspecialchars($row['data_inicio']); ?>" required></td>
                            <td><input type="date" name="data_fim" value="<?php echo htmlspecialchars($row['data_fim']); ?>" required></td>
                            <td><?php echo htmlspecialchars($row['metodo_pagamento']); ?></td>
                            <td><?php echo htmlspecialchars($row['preco_total']); ?></td>
                            <td><?php echo htmlspecialchars($row['data_registo']); ?></td>
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
