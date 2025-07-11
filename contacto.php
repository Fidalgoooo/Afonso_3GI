<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = htmlspecialchars($_POST['nome']);
    $email = htmlspecialchars($_POST['email']);
    $mensagem = htmlspecialchars($_POST['mensagem']);

    $para = "afonsoluispereira@gmail.com";
    $assunto = "Nova mensagem de contacto de $nome";

    // Carregar o template HTML e substituir placeholders
    $message = file_get_contents('./email/email_contacto.html');
    $message = str_replace('{$nome}', $nome, $message);
    $message = str_replace('{$email}', $email, $message);
    $message = str_replace('{$mensagem}', nl2br($mensagem), $message);

    $headers = "From: Sprintcar <suporte@sprintcar.com>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if (mail($para, $assunto, $message, $headers)) {
        $sucesso = "Mensagem enviada com sucesso. Obrigado pelo contacto!";
    } else {
        $erro = "Erro ao enviar a mensagem. Por favor tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Contactos | SprintCar</title>
    <link rel="stylesheet" href="css/sobre_contacto.css">
</head>
<body>
    <?php include 'scripts/menu.php'; ?>

    <section class="contactos">
        <div class="container">
            <h1>Contacte-nos</h1>
            <?php if (isset($sucesso)) echo "<p style='color: green;'>$sucesso</p>"; ?>
            <?php if (isset($erro)) echo "<p style='color: red;'>$erro</p>"; ?>
            <p>Estamos disponíveis para esclarecer qualquer dúvida, apoiar nas suas reservas ou receber feedback sobre o serviço.</p>

            <form method="POST" class="contact-form">
                <label for="nome">Nome:</label>
                <input type="text" name="nome" required>

                <label for="email">Email:</label>
                <input type="email" name="email" required>

                <label for="mensagem">Mensagem:</label>
                <textarea name="mensagem" rows="5" required></textarea>

                <button type="submit">Enviar Mensagem</button>
            </form>
        </div>
    </section>
  <?php include './scripts/footer.php'; ?>
</body>
</html>
