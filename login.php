<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar e validar entrada
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Por favor, preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Formato de e-mail inválido.";
    } else {
        // Preparar consulta SQL para buscar usuário
        $sql = "SELECT * FROM utilizadores WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verificar senha
            if (password_verify($password, $user['password'])) {
                // Armazenar dados do usuário na sessão
                $_SESSION['user_id'] = $user['id_utilizador'];
                $_SESSION['user_name'] = htmlspecialchars($user['nome']);
                $_SESSION['user_permission'] = $user['permissao'];
                $_SESSION['foto_perfil'] = $user['foto_perfil'] ?? ''; 

                // Verificar redirecionamento pendente
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect_url = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: $redirect_url");
                    exit;
                }

                // Redirecionar para a página inicial
                header("Location: index.php");
                exit;
            } else {
                $error = "Credenciais inválidas.";
            }
        } else {
            $error = "Credenciais inválidas.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aluguer de Carros - Login</title>
    <link rel="stylesheet" href="css/login/login.css">
</head>
<body>
    <div class="login-container">
        <div class="car-banner">
        </div>
        <div class="form-container">
            <h2 class="form-title">Faça Login</h2>
            <form class="login-form" action="login.php" method="POST">
                <div class="input-group">
                    <label for="email" class="input-label">Email</label>
                    <input type="email" id="email" name="email" class="input-field" placeholder="Digite seu email" required>
                </div>
                <div class="input-group">
                    <label for="password" class="input-label">Senha</label>
                    <input type="password" id="password" name="password" class="input-field" placeholder="Digite sua senha" required>
                </div>
                <button type="submit" class="btn-submit">Entrar</button>
            </form>

            <?php
            // Mostrar mensagem de erro, se houver
            if (isset($error)) {
                echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
            }
            ?>

            <p class="forgot-password">
                <a href="recuperar_pw.php">Esqueceu sua senha?</a>
            </p>
            <p class="login-link">
                Não tem conta? <a href="registo.php">Crie Agora</a>
            </p>
        </div>
    </div>
</body>
</html>
