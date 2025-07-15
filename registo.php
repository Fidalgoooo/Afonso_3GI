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
        $sql_insert = "INSERT INTO utilizadores (nome, email, password) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param('sss', $nome, $email, $password);

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

                $headers .= "From: Sprintcar <suporte@sprintcar.com>" . "\r\n";
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
    <style>
/* Modal escuro de fundo */
.modal {
    display: none;
    position: fixed;
    z-index: 999;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(2px);
}

/* Conteúdo do modal */
.modal-conteudo {
    background-color: #ffffff;
    margin: 5% auto;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 650px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    animation: slideDown 0.3s ease-out;
}

/* Animação ao abrir */
@keyframes slideDown {
    from {
        transform: translateY(-40px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Botão fechar */
.fechar {
    color: #555;
    float: right;
    font-size: 26px;
    font-weight: bold;
    cursor: pointer;
    margin-top: -10px;
    margin-right: -10px;
}

.fechar:hover {
    color: #e74c3c;
}

/* Tipografia do modal */
.modal-conteudo h2 {
    font-size: 24px;
    color: #2c3e50;
    margin-bottom: 15px;
}

.modal-conteudo h3 {
    font-size: 18px;
    color: #34495e;
    margin-top: 20px;
}

.modal-conteudo p {
    font-size: 15px;
    color: #555;
    line-height: 1.6;
}

/* Checkbox de termos */
.checkbox-group {
    margin-top: 15px;
    font-size: 14px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
}

.checkbox-label input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #007bff;
}

.termos-link {
    color: #007bff;
    text-decoration: underline;
    cursor: pointer;
    transition: color 0.2s;
}

.termos-link:hover {
    color: #0056b3;
}
</style>

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

                <!-- Checkbox termos -->
                <div class="input-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="termos" required>
                        Li e aceito os<span class="termos-link" onclick="abrirModal()">termos e condições</span>
                    </label>
                </div>

                <button type="submit" class="btn-submit">Registar</button>
            </form>

            <p class="login-link">
                Já tem uma conta? <a href="login.php">Inicie Sessão</a>
            </p>

            <!-- Mensagens -->
            <?php if (isset($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="success-message"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal dos Termos e Condições -->
    <div id="modal-termos" class="modal">
        <div class="modal-conteudo">
            <span class="fechar" onclick="fecharModal()">&times;</span>
            <h2>Termos e Condições</h2>
            <p>Ao utilizar o nosso serviço, o utilizador concorda com os seguintes termos e condições.</p>

            <h3>1. Condições Gerais</h3>
            <p>O serviço destina-se a maiores de 18 anos com carta válida.</p>

            <h3>2. Responsabilidade</h3>
            <p>O utilizador é responsável pela viatura durante o aluguer.</p>

            <h3>3. Pagamento e Cauções</h3>
            <p>Pagamentos são feitos no ato de reserva. Pode ser exigida caução.</p>

            <h3>4. Cancelamentos</h3>
            <p>Cancelamentos até 24h antes têm direito a reembolso.</p>

            <h3>5. Privacidade</h3>
            <p>Os dados são tratados com confidencialidade.</p>
        </div>
    </div>

    <!-- Script -->
    <script>
        function abrirModal() {
            document.getElementById('modal-termos').style.display = 'block';
        }

        function fecharModal() {
            document.getElementById('modal-termos').style.display = 'none';
        }

        // Fecha se clicar fora do conteúdo
        window.onclick = function(event) {
            const modal = document.getElementById('modal-termos');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
