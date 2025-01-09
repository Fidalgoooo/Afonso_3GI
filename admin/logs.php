<?php
include '../db.php';
$sql = "SELECT logs.*, utilizadores.nome AS nome_utilizador 
        FROM logs 
        INNER JOIN utilizadores ON logs.id_utilizador = utilizadores.id_utilizador 
        ORDER BY data DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Registos de Ações</title>
</head>
<body>
    <h1>Registos de Ações</h1>
    <table border="1">
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
</body>
</html>
