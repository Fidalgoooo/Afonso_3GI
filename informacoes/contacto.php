<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página em Desenvolvimento</title>
    <!-- <link rel="stylesheet" href="css/formatacao.css"> -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            color: white;
            text-align: center;
            padding: 100px 0;
            margin: 0;
        }

        h1 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.5em;
            margin-bottom: 30px;
        }

        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #2575fc;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        footer {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 1em;
            color: #ddd;
        }
    </style>
</head>
<body>
    <h1>Esta Página Está em Desenvolvimento</h1>
    <p>Aguarde um momento, estamos a trabalhar para trazer algo incrível!</p>
    <div class="spinner"></div>
    <footer>
        © 2024 Todos os direitos reservados.
    </footer>
</body>
</html>
