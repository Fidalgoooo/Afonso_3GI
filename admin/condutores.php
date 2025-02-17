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
            $id = intval($_POST['id']);
            $sql = "DELETE FROM condutores WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Condutor com ID {$id} foi eliminado.";
            registarLog($conn, $id_utilizador, 'Eliminar', $descricao);
        } elseif ($action === 'adicionar') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $contacto = $_POST['contacto'];

            $sql = "INSERT INTO condutores (nome, email, contacto) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nome, $email, $contacto);
            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Condutor adicionado: Nome: {$nome}, Email: {$email}, Contacto: {$contacto}.";
            registarLog($conn, $id_utilizador, 'Adicionar', $descricao);
        } elseif ($action === 'editar') {
            $id = intval($_POST['id']);
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $contacto = $_POST['contacto'];

            $sql = "UPDATE condutores SET nome = ?, email = ?, contacto = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nome, $email, $contacto, $id);
            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Condutor com ID {$id} foi editado: Nome: {$nome}, Email: {$email}, Contacto: {$contacto}.";
            registarLog($conn, $id_utilizador, 'Editar', $descricao);
        }
    }
}


// Consulta os condutores
$sql = "SELECT id, nome, email, contacto FROM condutores";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gerir Condutores</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/adicionar.css">

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
                <li><a href="admin_chat.php"><i class="fas fa-phone"></i> Chat</a></li>


                <!-- Mostrar apenas para ChiefAdmin -->
                <?php if (isset($_SESSION['user_permission']) && $_SESSION['user_permission'] === 'chiefadmin'): ?>
                    <li><a href="resets.php"><i class="fas fa-lock"></i> Password Resets</a></li>
                    <li><a href="logs.php"><i class="fas fa-cogs"></i> Logs</a></li>
                <?php endif; ?>

                <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
    </aside>

        <main class="dashboard">
            <h1>Condutores</h1>
            <h2>Adicionar Condutor</h2>
            <form method="post" class="adicionar">
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="contacto" placeholder="Contacto" required>
                <button type="submit" name="action" value="adicionar">Adicionar</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Contacto</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form method="post">
                            <td><input type="text" name="nome" value="<?php echo htmlspecialchars($row['nome']); ?>"></td>
                            <td><input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>"></td>
                            <td><input type="text" name="contacto" value="<?php echo htmlspecialchars($row['contacto']); ?>"></td>
                            <td>
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
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
