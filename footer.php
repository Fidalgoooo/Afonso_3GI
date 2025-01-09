<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer SprintCar</title>
    <style>
        /* Estilo do footer */
        .footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-section {
            flex: 1;
            min-width: 200px;
            padding: 10px;
        }

        .footer-section h3 {
            margin-bottom: 10px;
            color: #f4a261;
        }

        .footer-section p, .footer-section a {
            font-size: 14px;
            color: #ccc;
            text-decoration: none;
        }

        .footer-section a:hover {
            color: #fff;
        }

        .footer-bottom {
            margin-top: 20px;
            border-top: 1px solid #444;
            padding-top: 10px;
            font-size: 12px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <main class="content">
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>SprintCar</h3>
                <p>O seu parceiro de confiança para aluguer de carros em Portugal.</p>
            </div>
            <div class="footer-section">
                <h3>Contactos</h3>
                <p><strong>Endereço:</strong> Avenida Central, Lisboa</p>
                <p><strong>Email:</strong> <a href="mailto:info@sprintcar.com">info@sprintcar.com</a></p>
                <p><strong>Telefone:</strong> +351 123 456 789</p>
            </div>
            <div class="footer-section">
                <h3>Links Rápidos</h3>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="informacoes/sobre.php">Sobre Nós</a></li>
                    <li><a href="informacoes/contacto.php">Contactos</a></li>
                    <li><a href="#">Termos de Serviço</a></li>
                    <li><a href="#">Política de Privacidade</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 SprintCar. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
