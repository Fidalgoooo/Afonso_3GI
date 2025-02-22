<?php
include 'db.php'; // Conexão com a base de dados



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitização dos dados
    $nome = trim($_POST['nome']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Erro: Email inválido.");
    }

    // Verifica se o email já existe
    $sql_check = "SELECT * FROM utilizadores WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param('s', $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        die("Erro: O email já está registado.");
    } else {
        // Insere o utilizador na base de dados com código de indicação
        $sql_insert = "INSERT INTO utilizadores (nome, email, password, ) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param('ssss', $nome, $email, $password);

        if ($stmt_insert->execute()) {
            // Obter o email do utilizador inserido
            $sql_get_user = "SELECT email FROM utilizadores WHERE email = ?";
            $stmt_get_user = $conn->prepare($sql_get_user);
            $stmt_get_user->bind_param('s', $email);
            $stmt_get_user->execute();
            $result_user = $stmt_get_user->get_result();
            $user_data = $result_user->fetch_assoc();
            $to = $user_data['email'];

            // Enviar email de confirmação
            $subject = "Confirmação de Registo - Sprint Car";
            $message = file_get_contents('./email/email_registo.html');

            if ($message !== false) {
                // Substituir placeholders pelos dados reais
                $message = str_replace('{$nome}', $nome, $message);
                $message = str_replace('{$email}', $to, $message);

                $headers = "From: no-reply@sprintcar.com\r\n";
                $headers .= "Reply-To: suporte@sprintcar.com\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                mail($to, $subject, $message, $headers);
            }

            // Redirecionar para a página de login
            header("Location: login.php");
            exit();
        } else {
            die("Erro ao registar utilizador.");
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo - Aluguer de Carros</title>
    <link rel="stylesheet" href="css/login/registo.css">
</head>
<body>
    <div class="register-container">
        <div class="form-container">
            <h2 class="form-title">Registe-se</h2>
            <form class="register-form" action="registo.php" method="POST">
                <div class="input-group">
                    <label for="nome" class="input-label">Nome Completo</label>
                    <input type="text" id="nome" name="nome" class="input-field" placeholder="Digite seu nome" required>
                </div>
                <div class="input-group">
                    <label for="email" class="input-label">Email</label>
                    <input type="email" id="email" name="email" class="input-field" placeholder="Digite seu email" required>
                </div>
                <div class="input-group">
                    <label for="password" class="input-label">Senha</label>
                    <input type="password" id="password" name="password" class="input-field" placeholder="Digite sua senha" required>
                </div>
                <button type="submit" class="btn-submit">Registar</button>
            </form>
            <p class="login-link">
                Já tem uma conta? <a href="login.php">Inicie Sessão</a>
            </p>
            <!-- Exibe mensagens de sucesso ou erro -->
            <?php if (isset($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success-message"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

