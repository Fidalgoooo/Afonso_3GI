<?php
session_start();
include '../db.php';

// Definir fuso horário para Lisboa
date_default_timezone_set('Europe/Lisbon');


if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$id_utilizador = $_SESSION['user_id'];
$id_carro = $_POST['id_carro'] ?? null;
$avaliacao = $_POST['avaliacao'] ?? null;
$comentario = trim($_POST['comentario'] ?? '');
$data_avaliacao = date("Y-m-d H:i:s");

// Validação
if (!$id_carro || !$avaliacao || $avaliacao < 1 || $avaliacao > 5 || empty($comentario)) {
    header("Location: index.php");
    exit;
}

// Inserir avaliação na base de dados
$stmt = $conn->prepare("INSERT INTO avaliacoes (id_utilizador, id_carro, avaliacao, comentario, data_avaliacao) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiss", $id_utilizador, $id_carro, $avaliacao, $comentario, $data_avaliacao);

if ($stmt->execute()) {
    header("Location: index.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
