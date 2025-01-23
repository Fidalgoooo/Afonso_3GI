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
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'adm') {
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
            $id_admin = intval($_POST['id_admin']);
            $sql = "DELETE FROM administradores WHERE id_admin = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_admin);
            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Administrador com ID {$id_admin} foi eliminado.";
            registarLog($conn, $id_utilizador, 'Eliminar', $descricao);
        } elseif ($action === 'adicionar') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO administradores (nome, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nome, $email, $password);
            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Administrador adicionado: Nome: {$nome}, Email: {$email}.";
            registarLog($conn, $id_utilizador, 'Adicionar', $descricao);
        } elseif ($action === 'editar') {
            $id_admin = intval($_POST['id_admin']);
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

            if ($password) {
                $sql = "UPDATE administradores SET nome = ?, email = ?, password = ? WHERE id_admin = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $nome, $email, $password, $id_admin);
            } else {
                $sql = "UPDATE administradores SET nome = ?, email = ? WHERE id_admin = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $nome, $email, $id_admin);
            }

            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Administrador com ID {$id_admin} foi editado: Nome: {$nome}, Email: {$email}.";
            registarLog($conn, $id_utilizador, 'Editar', $descricao);
        }
    }
}

// Consulta os administradores
$sql = "SELECT id_admin, nome, email FROM administradores";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gerir Administradores</title>
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
                <li><a href="resets.php"><i class="fas fa-lock"></i> Password Resets</a></li>
                <li><a href="logs.php"><i class="fas fa-cogs"></i> Logs</a></li>
                <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="dashboard">
            <h1>Administradores</h1>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form method="post">
                            <td><input type="text" name="nome" value="<?php echo htmlspecialchars($row['nome']); ?>"></td>
                            <td><input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>"></td>
                            <td><input type="password" name="password" placeholder="Nova senha"></td>
                            <td>
                                <input type="hidden" name="id_admin" value="<?php echo $row['id_admin']; ?>">
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
