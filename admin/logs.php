<?php
session_start();
include '../db.php';

// Verifica se o utilizador está logado e se é ChiefAdmin
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'chiefadmin') {
    // Redireciona para o login ou outra página
    header("Location: ../login.php");
    exit;
}

// Queries para o dashboard
$sql = "SELECT logs.*, utilizadores.nome AS nome_utilizador 
        FROM logs 
        INNER JOIN utilizadores ON logs.id_utilizador = utilizadores.id_utilizador 
        ORDER BY data DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Registos de Ações</title>
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
            <h1>Registos de Ações</h1>
            <table>
                <tr>
                    <th>Data</th>
                    <th>Utilizador</th>
                    <th>Ação</th>
                    <th>Descrição</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['data']); ?></td>
                        <td><?php echo htmlspecialchars($row['nome_utilizador']); ?></td>
                        <td><?php echo htmlspecialchars($row['acao']); ?></td>
                        <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </main>
    </div>
</body>
</html>
