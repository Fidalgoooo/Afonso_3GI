<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    if (empty($email)) {
        $error = "Por favor, insira um e-mail.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Formato de e-mail inválido.";
    } else {
        // Verifica se o e-mail existe
        $sql = "SELECT * FROM utilizadores WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $nome = htmlspecialchars($user['nome']);

            // Gerar token único
            $token = bin2hex(random_bytes(50));
            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Salvar token na base de dados
            $sql = "UPDATE utilizadores SET reset_token = ?, reset_expira = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $token, $expira, $email);
            $stmt->execute();

            // Link de redefinição
            $link = "http://localhost/pap/Afonso_3GI/redefinir_pw.php?token=$token";

            // Criar o corpo do e-mail
            $subject = "Recuperação de Senha - Sprintcar";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: Sprintcar <suporte@sprintcar.com>" . "\r\n";

            $message = file_get_contents(filename: './email/email_pw.html');
            $message = str_replace(['{$nome}', '{$email}', '{$link}'], [$nome, $email, $link], $message);

            // Enviar e-mail
            if (mail($email, $subject, $message, $headers)) {
                $success = "Um e-mail foi enviado para $email.";
            } else {
                $error = "Falha ao enviar o e-mail.";
            }
        } else {
            $error = "Se este e-mail estiver registado, receberá um link de redefinição.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - Sprintcar</title>
    <link rel="stylesheet" href="css/login/recuperarPw.css">
</head>
<body>
    <div class="register-container">
        <div class="form-container">
            <h2 class="form-title">Recuperar Senha</h2>
            <form action="recuperar_pw.php" method="POST">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Digite seu e-mail">
                </div>
                <button type="submit" class="btn-submit">Recuperar Senha</button>
            </form>
            <p class="login-link">
                Lembrou sua senha? <a href="login.php">Inicie Sessão</a>
            </p>
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
