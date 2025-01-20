<?php
session_start();
include '../db.php';

// Verifica se o utilizador está logado e é administrador
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'adm') {
    header("Location: ../login.php");
    exit;
}

// Queries para o dashboard
$users_query = "SELECT COUNT(*) as total FROM utilizadores";
$users_result = mysqli_fetch_assoc(mysqli_query($conn, $users_query))['total'];

$drivers_query = "SELECT COUNT(*) as total FROM condutores";
$drivers_result = mysqli_fetch_assoc(mysqli_query($conn, $drivers_query))['total'];

$vehicles_query = "SELECT COUNT(*) as total FROM carros";
$vehicles_result = mysqli_fetch_assoc(mysqli_query($conn, $vehicles_query))['total'];

$bookings_query = "SELECT COUNT(*) as total FROM reservas";
$bookings_result = mysqli_fetch_assoc(mysqli_query($conn, $bookings_query))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Vehicle Booking System</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Vehicle Booking System</h2>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="drivers.php"><i class="fas fa-id-card"></i> Drivers</a></li>
                <li><a href="vehicles.php"><i class="fas fa-car"></i> Vehicles</a></li>
                <li><a href="bookings.php"><i class="fas fa-book"></i> Bookings</a></li>
                <li><a href="feedbacks.php"><i class="fas fa-comment"></i> Feedbacks</a></li>
                <li><a href="password_resets.php"><i class="fas fa-lock"></i> Password Resets</a></li>
                <li><a href="logs.php"><i class="fas fa-cogs"></i> System Logs</a></li>
            </ul>
        </aside>
        
        <main class="dashboard">
            <div class="cards">
                <div class="card">
                    <h3><?php echo $users_result; ?> Users</h3>
                    <a href="users.php">View Details</a>
                </div>
                <div class="card">
                    <h3><?php echo $drivers_result; ?> Drivers</h3>
                    <a href="drivers.php">View Details</a>
                </div>
                <div class="card">
                    <h3><?php echo $vehicles_result; ?> Vehicles</h3>
                    <a href="vehicles.php">View Details</a>
                </div>
                <div class="card">
                    <h3><?php echo $bookings_result; ?> Bookings</h3>
                    <a href="bookings.php">View Details</a>
                </div>
            </div>

            <div class="table-section">
                <h2>Bookings</h2>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Vehicle Type</th>
                            <th>Reg No</th>
                            <th>Booking Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysqli_query($conn, "SELECT r.id, u.nome as name, u.telefone as phone, c.tipo as vehicle_type, c.reg_no, r.data_reserva as booking_date, r.status 
                                                       FROM reservas r 
                                                       JOIN utilizadores u ON r.id_utilizador = u.id 
                                                       JOIN carros c ON r.id_carro = c.id");
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>{$row['vehicle_type']}</td>
                                    <td>{$row['reg_no']}</td>
                                    <td>{$row['booking_date']}</td>
                                    <td>{$row['status']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No bookings available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
