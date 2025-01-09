<?php
session_start();
include '../db.php';

// Verifica se o utilizador está logado e é administrador
if (!isset($_SESSION['user_permission']) || $_SESSION['user_permission'] !== 'adm') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Administração</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <header>
        <h1>Painel de Administração</h1>
        <a href="../logout.php">Logout</a>
    </header>
    <main>
        <h2>Bem-vindo ao Painel de Administração</h2>
        <p>Selecione uma das opções no menu para gerir o sistema.</p>
    </main>
    <nav>
        <ul>
            <!-- <li><a href="ver_utilizadores.php">Utilizadores</a></li>
            <li><a href="gerir_carros.php">Gerir Carros</a></li>
            <li><a href="consultar_reservas.php">Reservas</a></li> -->
            <li><a href="logs.php">Logs do Sistema</a></li>
        </ul>
    </nav>
</body>
</html>
