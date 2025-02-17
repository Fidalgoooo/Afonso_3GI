<?php
session_start();
include '../db.php';

// Verificar autenticação
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_permission'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['user_permission'] !== 'utilizador') {
    header("Location: ../admin/dashboard.php");
    exit();
}

$id_utilizador = $_SESSION['user_id'];

// ✅ Atualizar status das reservas que já terminaram
$update_stmt = $conn->prepare("UPDATE reservas SET status = 'Finalizada' WHERE id_utilizador = ? AND status = 'Confirmada' AND data_fim < CURDATE()");
$update_stmt->bind_param("i", $id_utilizador);
$update_stmt->execute();


// Buscar dados do utilizador
$utilizador_stmt = $conn->prepare("SELECT nome, email, foto_perfil, pontos_fidelidade, codigo_indicacao FROM utilizadores WHERE id_utilizador = ?");
$utilizador_stmt->bind_param("i", $id_utilizador);
$utilizador_stmt->execute();
$resultado = $utilizador_stmt->get_result();
$utilizador = $resultado->fetch_assoc();

if (!$utilizador) {
    echo "Erro: Utilizador não encontrado.";
    exit();
}

// Buscar reservas do utilizador
$reservas_stmt = $conn->prepare("SELECT r.id_reserva, CONCAT(c.marca, ' ', c.modelo) AS nome_carro, r.data_inicio, r.data_fim, r.preco_total, r.status 
                                FROM reservas r 
                                JOIN carros c ON r.id_carro = c.id_carro 
                                WHERE r.id_utilizador = ? ORDER BY r.data_registo DESC");

$reservas_stmt->bind_param("i", $id_utilizador);
$reservas_stmt->execute();
$reservas_result = $reservas_stmt->get_result();
$reservas = $reservas_result->fetch_all(MYSQLI_ASSOC);

// Buscar histórico de reservas passadas
$historico_stmt = $conn->prepare("SELECT CONCAT(c.marca, ' ', c.modelo) AS nome_carro, r.data_inicio, r.data_fim, r.preco_total FROM reservas r JOIN carros c ON r.id_carro = c.id_carro WHERE r.id_utilizador = ? AND r.status = 'Finalizada' ORDER BY r.data_fim DESC");
$historico_stmt->bind_param("i", $id_utilizador);
$historico_stmt->execute();
$historico_result = $historico_stmt->get_result();
$historico = $historico_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Utilizador</title>
    <link rel="stylesheet" href="./css/index.css">
    <script>
        function mostrarSecao(secao) {
            document.querySelectorAll('.secao').forEach(div => div.style.display = 'none');
            document.getElementById(secao).style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <ul>
            <li onclick="mostrarSecao('reservas')">📅 Minhas Reservas</li>
            <li onclick="mostrarSecao('perfil')">👤 Editar Perfil</li>
            <li onclick="mostrarSecao('historico')">📜 Histórico de Reservas</li>
            <li onclick="mostrarSecao('fidelidade')">🎁 Programa de Fidelidade</li>
            <li onclick="mostrarSecao('avaliacoes')">⭐ Avaliações</li>
            <li onclick="mostrarSecao('indicacoes')">👥 Indique um Amigo</li>
            <li><a href="../index.php">🚪 Sair</a></li>
        </ul>
    </div>

    <div class="content">
        <h2>Bem-vindo, <?php echo htmlspecialchars($utilizador['nome']); ?>!</h2>
        <img src="../uploads/<?php echo $utilizador['foto_perfil'] ?: 'default.png'; ?>" alt="Foto de Perfil" class="perfil-img">

        <div id="reservas" class="secao">
            <h3>Suas Reservas</h3>
            <table>
                <tr>
                    <th>Carro</th>
                    <th>Data Início</th>
                    <th>Data Fim</th>
                    <th>Preço Total</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reserva['nome_carro']); ?></td>
                        <td><?php echo $reserva['data_inicio']; ?></td>
                        <td><?php echo $reserva['data_fim']; ?></td>
                        <td><?php echo number_format($reserva['preco_total'], 2, ',', '.'); ?> €</td>
                        <td><?php echo $reserva['status']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div id="historico" class="secao" style="display: none;">
            <h3>Histórico de Reservas</h3>
            <ul>
                <?php foreach ($historico as $reserva): ?>
                    <li><?php echo htmlspecialchars($reserva['nome_carro']) . " - " . $reserva['data_inicio'] . " a " . $reserva['data_fim'] . " - " . number_format($reserva['preco_total'], 2, ',', '.') . " €"; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div id="fidelidade" class="secao" style="display: none;">
            <h3>Programa de Fidelidade</h3>
            <p>Você possui <strong><?php echo $utilizador['pontos_fidelidade']; ?></strong> pontos acumulados!</p>
        </div>

        <div id="avaliacoes" class="secao" style="display: none;">
            <h3>Avaliações</h3>
            <p>Deixe sua opinião sobre nossos serviços e carros.</p>
        </div>

        <div id="indicacoes" class="secao" style="display: none;">
            <h3>Indique um Amigo</h3>
            <p>Compartilhe seu código de indicação: <strong><?php echo $utilizador['codigo_indicacao']; ?></strong></p>
        </div>

        <div id="perfil" class="secao" style="display: none;">
            <h3>Editar Conta</h3>
            <form action="editar_perfil.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_utilizador" value="<?php echo $id_utilizador; ?>">
                <input type="text" name="nome" value="<?php echo htmlspecialchars($utilizador['nome']); ?>" required>
                <input type="email" name="email" value="<?php echo htmlspecialchars($utilizador['email']); ?>" required>
                <label for="foto_perfil">Foto de Perfil:</label>
                <input type="file" name="foto_perfil" accept="image/*">
                <button type="submit">Atualizar</button>
            </form>
        </div>
    </div>
</body>
</html>
