<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'sprintcar';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

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


?>
