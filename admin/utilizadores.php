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

// Verifica ações enviadas pelo formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $id_utilizador = $_SESSION['user_id']; // ID do administrador logado

        if ($action === 'eliminar') {
            $id_user = intval($_POST['id_utilizador']);
            $sql = "DELETE FROM utilizadores WHERE id_utilizador = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_user);

            if ($stmt->execute()) {
                $message = "Utilizador eliminado com sucesso.";
                $descricao = "Utilizador com ID $id_user eliminado.";
                registarLog($conn, $id_utilizador, 'Eliminar', $descricao);
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
                $descricao = "Novo utilizador adicionado: Nome: $nome, Email: $email, Permissão: $permissao.";
                registarLog($conn, $id_utilizador, 'Adicionar', $descricao);
            } else {
                $message = "Erro ao adicionar o utilizador.";
            }
            $stmt->close();
        } elseif ($action === 'editar') {
            $id_user = intval($_POST['id_utilizador']);
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            // $permissao = $_POST['permissao'];

            $sql = "UPDATE utilizadores SET nome = ?, email = ? WHERE id_utilizador = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $nome, $email, $id_user);

            if ($stmt->execute()) {
                $message = "Utilizador atualizado com sucesso.";
                $descricao = "Utilizador com ID $id_user atualizado: Nome: $nome, Email: $email.";
                registarLog($conn, $id_utilizador, 'Editar', $descricao);
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
        <!-- <link rel="stylesheet" href="./css/dashboard.css"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <!-- <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a></li> -->
        </ul>
    </aside>

    <!-- Conteúdo e header à direita -->
    <div style="flex: 1; display: flex; flex-direction: column;">

      <?php include 'header_admin.php'; ?>
        <main class="dashboard">
            <div class="title-bar">
                <h1>Adicionar utilizadores</h1>            
                <form method="post" class="adicionar">
                <input type="text" name="nome" placeholder="Nome" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="permissao">
                    <option value="utilizador">Utilizador</option>
                    <option value="adm">Administrador</option>
                </select>
                <button type="submit" name="action" value="adicionar">Adicionar</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
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
