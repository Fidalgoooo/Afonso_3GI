<?php
session_start();
include '../db.php';

// Verifica se o utilizador está logado e é administrador
if (!isset($_SESSION['user_permission']) || ($_SESSION['user_permission'] !== 'adm' && $_SESSION['user_permission'] !== 'chiefadmin')) {
    header("Location: ../login.php");
    exit;
}

// Queries para o dashboard
$users_query = "SELECT COUNT(*) as total FROM utilizadores";
$users_result = mysqli_fetch_assoc(mysqli_query($conn, $users_query))['total'] ?? 0;

$drivers_query = "SELECT COUNT(*) as total FROM condutores";
$drivers_result = mysqli_fetch_assoc(mysqli_query($conn, $drivers_query))['total'] ?? 0;

$vehicles_query = "SELECT COUNT(*) as total FROM carros";
$vehicles_result = mysqli_fetch_assoc(mysqli_query($conn, $vehicles_query))['total'] ?? 0;

$bookings_query = "SELECT COUNT(*) as total FROM reservas";
$bookings_result = mysqli_fetch_assoc(mysqli_query($conn, $bookings_query))['total'] ?? 0;

// Query para calcular o faturamento do primeiro semestre
$faturamento_query = "
    SELECT MONTH(data_inicio) AS mes, SUM(preco_total) AS faturamento
    FROM reservas
    WHERE MONTH(data_inicio) BETWEEN 1 AND 7 AND YEAR(data_inicio) = 2025
    GROUP BY MONTH(data_inicio)
";
$faturamento_result = mysqli_query($conn, $faturamento_query);

// Inicializa o faturamento com 0 para os meses do primeiro semestre
$faturamentoSemestre = array_fill(1, 7, 0);

while ($row = mysqli_fetch_assoc($faturamento_result)) {
    $faturamentoSemestre[(int)$row['mes']] = (float)$row['faturamento'];
}

// Calcula veículos ocupados e disponíveis
$ocupados_query = "SELECT COUNT(DISTINCT id_carro) AS total_ocupados 
                FROM reservas 
                WHERE CURDATE() BETWEEN data_inicio AND data_fim
                AND status NOT IN ('Finalizada', 'Cancelada');";
                   
$ocupados_result = mysqli_fetch_assoc(mysqli_query($conn, $ocupados_query))['total_ocupados'] ?? 0;


$total_carros_query = "SELECT COUNT(*) as total FROM carros";
$total_carros_result = mysqli_fetch_assoc(mysqli_query($conn, $total_carros_query))['total'] ?? 0;


$total_veiculos = $vehicles_result;
$disponiveis = $total_veiculos - $ocupados_result;

// Meses do primeiro semestre
$mesesSemestre = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho"];

// Envia os dados para o JavaScript
echo '<script>
    const faturamentoSemestre = ' . json_encode($faturamentoSemestre) . ';
    const mesesSemestre = ' . json_encode($mesesSemestre) . ';
    const usersData = ' . $users_result . ';
    const driversData = ' . $drivers_result . ';
    const vehiclesData = ' . $vehicles_result . ';
    const bookingsData = ' . $bookings_result . ';
    const ocupados = ' . $ocupados_result . ';
    const disponiveis = ' . $disponiveis . ';
</script>';
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
    <div class="top-right">
        <a href="../index.php" class="view-site-btn"><i class="fas fa-eye"></i>Ver Site</a>
    </div>
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
            <div class="cards">
                <div class="card">
                    <h3><?php echo $users_result; ?> Utilizadores</h3>
                    <a href="utilizadores.php">Ver Detalhes</a>
                </div>
                <div class="card">
                    <h3><?php echo $drivers_result; ?> Condutores</h3>
                    <a href="condutores.php">Ver Detalhes</a>
                </div>
                <div class="card">
                    <h3><?php echo $vehicles_result; ?> Veículos</h3>
                    <a href="veiculos.php">Ver Detalhes</a>
                </div>
                <div class="card">
                    <h3><?php echo $bookings_result; ?> Reservas</h3>
                    <a href="reservas.php">Ver Detalhes</a>
                </div>
            </div>

            <div class="charts">
                <div class="chart-container">
                    <canvas id="usersChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="gaugeChart"></canvas>
                </div>
            </div>
            <div class="table-section">
    <h2>Reservas</h2>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Contacto</th>
                <th>Data Início</th>
                <th>Data Fim</th>
                <th>Método de Pagamento</th>
                <th>Veículo</th>
                <th>Preço Total</th>
                <th>Data Registo</th>
                <th>Status</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT r.nome, r.email, r.contacto, r.data_inicio, r.data_fim, r.metodo_pagamento, 
                       r.preco_total, r.data_registo ,r.status, c.marca, c.modelo
                FROM reservas r
                JOIN carros c ON r.id_carro = c.id_carro
            ";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$row['nome']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['contacto']}</td>
                        <td>{$row['data_inicio']}</td>
                        <td>{$row['data_fim']}</td>
                        <td>{$row['metodo_pagamento']}</td>
                        <td>{$row['marca']} {$row['modelo']}</td>
                        <td>{$row['preco_total']}</td>
                        <td>{$row['data_registo']}</td>                        
                        <td>{$row['status']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Nenhuma Reserva</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>


    <script>
        const usersData = <?php echo $users_result; ?>;
        const driversData = <?php echo $drivers_result; ?>;
        const vehiclesData = <?php echo $vehicles_result; ?>;
        const bookingsData = <?php echo $bookings_result; ?>;
        const reservasMensais = <?php echo json_encode($reservas_mensais); ?>;
        const meses = <?php echo json_encode($meses); ?>;
        const ocupados = <?php echo $ocupados_result; ?>;
        const disponiveis = <?php echo $disponiveis; ?>;
    </script>

    <script src="../scripts/graficos.js"></script>

</body>
</html>
