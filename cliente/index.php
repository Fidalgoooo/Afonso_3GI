<?php
session_start();
include '../db.php';
include('header_cliente.php');


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

// Atualizar status das reservas que já terminaram
$update_stmt = $conn->prepare("UPDATE reservas SET status = 'Finalizada' WHERE id_utilizador = ? AND status = 'Confirmada' AND data_fim < CURDATE()");
$update_stmt->bind_param("i", $id_utilizador);
$update_stmt->execute();

// Buscar dados do utilizador
$utilizador_stmt = $conn->prepare("SELECT nome, email, foto_perfil FROM utilizadores WHERE id_utilizador = ?");
$utilizador_stmt->bind_param("i", $id_utilizador);
$utilizador_stmt->execute();
$resultado = $utilizador_stmt->get_result();
$utilizador = $resultado->fetch_assoc();

if (!$utilizador) {
    echo "Erro: Utilizador não encontrado.";
    exit();
}

// Buscar reservas do utilizador
$reservas_stmt = $conn->prepare("SELECT r.id_reserva, r.codigo_reserva, CONCAT(c.marca, ' ', c.modelo) AS nome_carro, r.data_inicio, r.data_fim, r.preco_total, r.status 
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script>
        function mostrarSecao(secao) {
            document.querySelectorAll('.secao').forEach(div => div.style.display = 'none');
            document.getElementById(secao).style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="sidebar">
    <h2>Área de Cliente</h2>
        <ul>
            <li onclick="mostrarSecao('reservas')">
                <a href="#" class="menu-item">
                    <i class="bi bi-calendar3"></i>
                    <span>Minhas Reservas</span>
                </a>
            </li>            
            <li onclick="mostrarSecao('perfil')">
                <a href="#" class="menu-item">
                    <i class="bi bi-person-circle"></i>
                    <span>Editar Perfil</span>
                </a>
            </li>
            <li onclick="mostrarSecao('avaliacoes')">
                <a href="#" class="menu-item">
                    <i class="bi bi-star-fill"></i>
                    <span>Avaliações</span>
                </a>
            </li>
            <li onclick="mostrarSecao('historico')">
                <a href="#" class="menu-item">
                    <i class="bi bi-clock-history"></i>
                    <span>Histórico de Reservas</span>
                </a>
            </li>
            <li onclick="mostrarSecao('pagamentos')">
                <a href="#" class="menu-item">
                    <i class="bi bi-credit-card"></i>
                    <span>Histórico de Pagamentos</span>
                </a>
            </li>
        </ul>



</div>
    <div class="content">
        <div id="reservas" class="secao">
            <h2>Bem-vindo, <?php echo htmlspecialchars($utilizador['nome']); ?>!</h2>
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
                        <td><?php echo date('d-m-Y', strtotime($reserva['data_inicio'])); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($reserva['data_fim'])); ?></td>
                        <td><?php echo number_format($reserva['preco_total'], 2, ',', '.'); ?> €</td>
                        <td><?php echo htmlspecialchars($reserva['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div id="historico" class="secao" style="display: none;">
            <h3>Histórico de Reservas</h3>
            
            <div class="historico-lista">
                <?php foreach ($historico as $reserva): ?>
                    <div class="reserva-card">
                        <h4><?= htmlspecialchars($reserva['nome_carro']) ?></h4>
                        <p><strong>De:</strong> <?= date('d-m-Y', strtotime($reserva['data_inicio'])) ?> 
                        <strong>até</strong> <?= date('d-m-Y', strtotime($reserva['data_fim'])) ?></p>
                        <p><strong>Preço:</strong> <?= number_format($reserva['preco_total'], 2, ',', '.') ?> €</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>


                <div id="pagamentos" class="secao" style="display: none;">
            <h3>Histórico de Pagamentos</h3>
            <table>
                <tr>
                    <th>Reserva</th>
                    <th>Período</th>
                    <th>Valor</th>
                    <th>Fatura</th>
                </tr>
                <?php foreach ($reservas as $reserva): 
                    $codigo = $reserva['codigo_reserva'];
                    $pdf_file = '../faturas/fatura_' . $codigo . '.pdf';
                ?>
                    <tr>
                        <td><?= htmlspecialchars($codigo) ?></td>
                        <td>
                            <?= date('d-m-Y', strtotime($reserva['data_inicio'])) ?>
                            a 
                            <?= date('d-m-Y', strtotime($reserva['data_fim'])) ?>
                        </td>
                        <td><?= number_format($reserva['preco_total'], 2, ',', '.') ?> €</td>
                        <td>
                            <?php if (file_exists($pdf_file)): ?>
                                <a href="<?= $pdf_file ?>" target="_blank">Ver Fatura</a>
                            <?php else: ?>
                                Não disponível
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>


<div id="avaliacoes" class="secao" style="display: none;">
    <div class="card-container">
        <h2>Deixe uma Avaliação</h2>
        <form action="avaliar.php" method="POST" class="avaliacao-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="id_carro">Carro:</label>
                    <select name="id_carro" required>
                        <?php
                        $carros_stmt = $conn->prepare("SELECT DISTINCT c.id_carro, CONCAT(c.marca, ' ', c.modelo) AS nome_carro 
                                                        FROM reservas r 
                                                        JOIN carros c ON r.id_carro = c.id_carro 
                                                        WHERE r.id_utilizador = ? AND r.status = 'Finalizada'");
                        $carros_stmt->bind_param("i", $id_utilizador);
                        $carros_stmt->execute();
                        $carros_result = $carros_stmt->get_result();

                        while ($carro = $carros_result->fetch_assoc()) {
                            echo "<option value='{$carro['id_carro']}'>{$carro['nome_carro']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="avaliacao">Nota (1 a 5):</label>
                    <select name="avaliacao" required>
                        <option value="1">1 - Péssimo</option>
                        <option value="2">2 - Mau</option>
                        <option value="3">3 - Normal</option>
                        <option value="4">4 - Bom</option>
                        <option value="5">5 - Excelente</option>
                    </select>
                </div>
            </div>

            <div class="form-group full-width">
                <label for="comentario">Comentário:</label>
                <textarea name="comentario" placeholder="Escreve a tua opinião..." required></textarea>
            </div>

            <div class="form-group full-width">
                <button type="submit">Enviar Avaliação</button>
            </div>
        </form>
    </div>

    <div class="card-container">
        <h2>Suas Avaliações</h2>
        <table>
            <tr>
                <th>Carro</th>
                <th>Nota</th>
                <th>Comentário</th>
                <th>Data</th>
            </tr>
            <?php
            $avaliacoes_stmt = $conn->prepare("SELECT c.marca, c.modelo, a.avaliacao, a.comentario, a.data_avaliacao 
                                                FROM avaliacoes a 
                                                JOIN carros c ON a.id_carro = c.id_carro 
                                                WHERE a.id_utilizador = ? ORDER BY a.data_avaliacao DESC");
            $avaliacoes_stmt->bind_param("i", $id_utilizador);
            $avaliacoes_stmt->execute();
            $avaliacoes_result = $avaliacoes_stmt->get_result();

                while ($avaliacao = $avaliacoes_result->fetch_assoc()) {
                    $dataFormatada = date('d-m-Y', strtotime($avaliacao['data_avaliacao']));
                    
                    echo "<tr>
                            <td>{$avaliacao['marca']} {$avaliacao['modelo']}</td>
                            <td>{$avaliacao['avaliacao']} ⭐</td>
                            <td>{$avaliacao['comentario']}</td>
                            <td>{$dataFormatada}</td>
                        </tr>";
                }
            ?>
        </table>
    </div>
</div>


<div id="perfil" class="secao" style="display: none;">
    <div class="card-container">
        <h2>Editar Conta</h2>
        <form action="editar_perfil.php" method="POST" enctype="multipart/form-data" class="edit-profile-form">
            <input type="hidden" name="id_utilizador" value="<?php echo $id_utilizador; ?>">

            <div class="form-row">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($utilizador['nome']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($utilizador['email']); ?>" required>
                </div>
            </div>

            <div class="form-group full-width">
                <label for="foto_perfil">Foto de Perfil:</label>
                <input type="file" name="foto_perfil" accept="image/*">
            </div>

            <div class="form-group full-width">
                <button type="submit">Atualizar</button>
            </div>
        </form>
    </div>
</div>

    </div>
    <?php include 'chat_widget.php'; ?>
</body>
</html>
