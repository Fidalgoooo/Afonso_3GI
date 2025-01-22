<?php
session_start();
include '../db.php';

// Verifica se o utilizador está logado e é administrador
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'adm') {
    header("Location: ../login.php");
    exit;
}

// Verifica ações enviadas pelo formulário
// Ações para a tabela utilizadores
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $id_utilizador = $_SESSION['user_id'];

        if ($action === 'eliminar') {
            $id_user = intval($_POST['id_utilizador']);
            $sql = "DELETE FROM utilizadores WHERE id_utilizador = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_user);
            if ($stmt->execute()) {
                $message = "Utilizador eliminado com sucesso.";
                registarLog($conn, $id_utilizador, 'eliminar', "Utilizador ID $id_user eliminado.");
            } else {
                $message = "Erro ao eliminar o utilizador.";
            }
            $stmt->close();
        } elseif ($action === 'adicionar') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $permissao = $_POST['permissao'];

            $sql = "INSERT INTO utilizadores (nome, email, password, permissao) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nome, $email, $password, $permissao);
            if ($stmt->execute()) {
                $message = "Utilizador adicionado com sucesso.";
                registarLog($conn, $id_utilizador, 'adicionar', "Utilizador $nome adicionado.");
            } else {
                $message = "Erro ao adicionar o utilizador.";
            }
            $stmt->close();
        } elseif ($action === 'editar') {
            $id_user = intval($_POST['id_utilizador']);
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $permissao = $_POST['permissao'];

            $sql = "UPDATE utilizadores SET nome = ?, email = ?, permissao = ? WHERE id_utilizador = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nome, $email, $permissao, $id_user);
            if ($stmt->execute()) {
                $message = "Utilizador atualizado com sucesso.";
                registarLog($conn, $id_utilizador, 'editar', "Utilizador ID $id_user atualizado.");
            } else {
                $message = "Erro ao atualizar o utilizador.";
            }
            $stmt->close();
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
    <link rel="stylesheet" href="./css/adicionar.css">
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
            <h1>Utilizadores</h1>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
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
                            <td>
                                <select name="permissao">
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

            <h2>Adicionar Utilizador</h2>
            <form method="post">
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="permissao">
                    <option value="adm">Administrador</option>
                    <option value="utilizador">Utilizador</option>
                </select>
                <button type="submit" name="action" value="adicionar">Adicionar</button>
            </form>
        </main>
    </div>
</body>
</html>
