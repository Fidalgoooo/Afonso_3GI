<?php
// Conexão à base de dados
$host = "localhost"; // Alterar se necessário
$user = "root"; // Utilizador da base de dados
$password = ""; // Password da base de dados
$dbname = "sprintcar";

$conn = new mysqli($host, $user, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Iniciar sessão
session_start();

$erro = ""; // Inicializar variável para mensagens de erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os campos 'email' e 'password' existem no array $_POST
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Query para verificar o utilizador
        $sql = "SELECT * FROM administradores WHERE email = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Utilizador encontrado
            $user = $result->fetch_assoc();
            $_SESSION['id_admin'] = $user['id_admin'];
            $_SESSION['nome'] = $user['nome'];

            // Redirecionar para a página inicial
            header("Location: dashboard.php");
            exit;
        } else {
            $erro = "Email ou password incorretos.";
        }

        $stmt->close();
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/admin.css">

</head>
<body>
    <h2>Login de Administrador</h2>
    <form method="POST" action="">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Entrar</button>
    </form>

    <?php if (!empty($erro)): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>
</body>
</html>
