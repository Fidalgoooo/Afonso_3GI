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

// Verifica se o utilizador está logado e se é ChiefAdmin
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'chiefadmin') {
    // Redireciona para o login ou outra página
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
            $id_utilizador_alvo = intval($_POST['id_utilizador']);
            $sql = "DELETE FROM utilizadores WHERE id_utilizador = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_utilizador_alvo);
            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Utilizador com ID {$id_utilizador_alvo} foi eliminado.";
            registarLog($conn, $id_utilizador, 'Eliminar', $descricao);
        } elseif ($action === 'adicionar') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $permissao = $_POST['permissao'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO utilizadores (nome, email, permissao, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nome, $email, $permissao, $password);
            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Utilizador adicionado: Nome: {$nome}, Email: {$email}, Permissão: {$permissao}.";
            registarLog($conn, $id_utilizador, 'Adicionar', $descricao);
        } elseif ($action === 'editar') {
            $id_utilizador_alvo = intval($_POST['id_utilizador']);
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $permissao = $_POST['permissao'];
            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

            if ($password) {
                $sql = "UPDATE utilizadores SET nome = ?, email = ?, permissao = ?, password = ? WHERE id_utilizador = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $nome, $email, $permissao, $password, $id_utilizador_alvo);
            } else {
                $sql = "UPDATE utilizadores SET nome = ?, email = ?, permissao = ? WHERE id_utilizador = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $nome, $email, $permissao, $id_utilizador_alvo);
            }

            $stmt->execute();
            $stmt->close();

            // Registar log
            $descricao = "Utilizador com ID {$id_utilizador_alvo} foi editado: Nome: {$nome}, Email: {$email}, Permissão: {$permissao}.";
            registarLog($conn, $id_utilizador, 'Editar', $descricao);
        }
    }
}

// Consulta os utilizadores
$sql = "SELECT id_utilizador, nome, email, permissao FROM utilizadores";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gerir Utilizadores</title>
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
            <h1>Password Resets</h1>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Permissão</th>
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
                                <select name="permissao">
                                    <option value="chiefadmin" <?php echo $row['permissao'] === 'chiefadmin' ? 'selected' : ''; ?>>Chief Admin</option>
                                    <option value="adm" <?php echo $row['permissao'] === 'adm' ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="utilizador" <?php echo $row['permissao'] === 'utilizador' ? 'selected' : ''; ?>>Utilizador</option>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="id_utilizador" value="<?php echo $row['id_utilizador']; ?>">
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
