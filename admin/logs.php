<?php
session_start();
include '../db.php';

// Verifica se o utilizador está logado e se é ChiefAdmin
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'chiefadmin') {
    header("Location: ../login.php");
    exit;
}

// Paginação
$por_pagina = 10;
$pagina_atual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_atual - 1) * $por_pagina;

// Obter total de registos
$res_total = $conn->query("SELECT COUNT(*) AS total FROM logs");
$total_linhas = $res_total->fetch_assoc()['total'];
$total_paginas = ceil($total_linhas / $por_pagina);

// Query com LIMIT
$sql = "SELECT logs.*, utilizadores.nome AS nome_utilizador 
        FROM logs 
        INNER JOIN utilizadores ON logs.id_utilizador = utilizadores.id_utilizador 
        ORDER BY data DESC 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $por_pagina);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="pt">
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
                <li><a href="admin_chat.php"><i class="fas fa-phone"></i> Chat</a></li>
                <?php if ($_SESSION['user_permission'] === 'chiefadmin'): ?>
                    <li><a href="resets.php"><i class="fas fa-lock"></i> Password Resets</a></li>
                    <li><a href="logs.php"><i class="fas fa-cogs"></i> Logs</a></li>
                <?php endif; ?>
                <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="dashboard">
            <h1>Registos de Ações</h1>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Utilizador</th>
                        <th>Ação</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $numero = $offset + 1;
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($row['data']); ?></td>
                        <td><?= htmlspecialchars($row['nome_utilizador']); ?></td>
                        <td><?= htmlspecialchars($row['acao']); ?></td>
                        <td><?= htmlspecialchars($row['descricao']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Paginação com setas e página atual -->
            <div class="pagination" style="margin-top: 20px; display: flex; gap: 10px; align-items: center;">
                <?php if ($pagina_atual > 1): ?>
                    <a href="?pagina=<?= $pagina_atual - 1 ?>" title="Anterior">←</a>
                <?php else: ?>
                    <span style="color: grey;">←</span>
                <?php endif; ?>

                <span style="font-weight: bold;"><?= $pagina_atual ?></span>

                <?php if ($pagina_atual < $total_paginas): ?>
                    <a href="?pagina=<?= $pagina_atual + 1 ?>" title="Seguinte">→</a>
                <?php else: ?>
                    <span style="color: grey;">→</span>
                <?php endif; ?>
            </div>

        </main>
    </div>
</body>
</html>
