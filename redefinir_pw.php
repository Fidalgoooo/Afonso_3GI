<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (empty($_POST['password']) || empty($_POST['confirm_password'])) {
        $error = "Por favor, preencha todos os campos.";
    } elseif ($_POST['password'] !== $_POST['confirm_password']) {
        $error = "As senhas não coincidem.";
    } else {
        // Verifica se o token é válido
        $sql = "SELECT * FROM utilizadores WHERE reset_token = ? AND reset_expira > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $sql = "UPDATE utilizadores SET password = ?, reset_token = NULL, reset_expira = NULL WHERE reset_token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ss', $new_password, $token);
            $stmt->execute();

            $success = "Senha redefinida com sucesso! <a href='login.php'>Inicie sessão</a>";
        } else {
            $error = "Token inválido ou expirado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="css/login/recuperarPw.css">
</head>
<body>
    <div class="register-container">
        <div class="form-container">
            <h2 class="form-title">Redefinir Senha</h2>
            <form action="redefinir_pw.php" method="POST">
            <input type="hidden" name="token" value="<?= isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '' ?>">
                <div class="input-group">
                    <label for="password">Nova Senha</label>
                    <input type="password" id="password" name="password" required placeholder="Digite sua nova senha">
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirmar Senha</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirme sua nova senha">
                </div>
                <button type="submit" class="btn-submit" style="display: block; margin: 0 auto;">Redefinir Senha</button>
            </form>
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

