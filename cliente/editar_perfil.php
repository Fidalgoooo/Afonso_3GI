<?php
session_start();
include '../db.php';

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_utilizador = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_nome = trim($_POST['nome']);
    $novo_email = trim($_POST['email']);

    // Verificar se os campos estão vazios
    if (empty($novo_nome) || empty($novo_email)) {
        echo "Erro: Todos os campos são obrigatórios.";
        exit();
    }

    // Verificar se foi feito upload de uma nova foto
    if (!empty($_FILES['foto_perfil']['name'])) {
        $foto_nome = $_FILES['foto_perfil']['name'];
        $foto_tmp = $_FILES['foto_perfil']['tmp_name'];
        $foto_tamanho = $_FILES['foto_perfil']['size'];
        $foto_extensao = strtolower(pathinfo($foto_nome, PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($foto_extensao, $extensoes_permitidas)) {
            echo "Erro: Apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
            exit();
        }

        if ($foto_tamanho > 20971520) { // 20MB
            echo "Erro: A imagem deve ter no máximo 20MB.";
            exit();
        }        

        // Nome único para a imagem
        $novo_foto_nome = "perfil_" . $id_utilizador . "." . $foto_extensao;
        $destino = "../uploads/" . $novo_foto_nome;

        if (move_uploaded_file($foto_tmp, $destino)) {
            // Atualizar a foto de perfil no banco de dados
            $update_foto_stmt = $conn->prepare("UPDATE utilizadores SET foto_perfil = ? WHERE id_utilizador = ?");
            $update_foto_stmt->bind_param("si", $novo_foto_nome, $id_utilizador);
            $update_foto_stmt->execute();
        } else {
            echo "Erro ao fazer upload da imagem.";
            exit();
        }
    }

    // Atualizar os dados do utilizador
    $update_stmt = $conn->prepare("UPDATE utilizadores SET nome = ?, email = ? WHERE id_utilizador = ?");
    $update_stmt->bind_param("ssi", $novo_nome, $novo_email, $id_utilizador);

    if ($update_stmt->execute()) {
        // Atualiza a sessão com os novos dados
        $_SESSION['nome'] = $novo_nome;
        $_SESSION['email'] = $novo_email;

        echo "Perfil atualizado com sucesso!";
        header("Location: index.php"); // Redireciona de volta ao painel
        exit();
    } else {
        echo "Erro ao atualizar o perfil.";
    }
}
?>
