<?php
require 'db.php'; // Conexão ao banco de dados
require 'paypal-config.php'; // Configuração do PayPal

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

// Verificar se os parâmetros foram recebidos
if (!isset($_GET['paymentId'], $_GET['PayerID'])) {
    die("<p class='error-message'>Erro: Parâmetros de pagamento não encontrados!</p>");
}

$paymentId = $_GET['paymentId'];
$payerId = $_GET['PayerID'];

// Verificar se o ID do utilizador está na sessão
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    die("<p class='error-message'>Erro: Usuário não autenticado. ID do utilizador não encontrado.</p>");
}

$user_id = $_SESSION['user_id']; // Obtém o ID do utilizador autenticado

try {
    // Obter o pagamento criado anteriormente
    $payment = Payment::get($paymentId, $apiContext);

    // Configurar a execução do pagamento
    $execution = new PaymentExecution();
    $execution->setPayerId($payerId);

    // Executar o pagamento
    $result = $payment->execute($execution, $apiContext);

    // Verificar se o pagamento foi aprovado
    if ($result->getState() === "approved") {
        // Obter o ID da transação gerado pelo PayPal
        $transactionId = $result->getTransactions()[0]->getRelatedResources()[0]->getSale()->getId();

        // Obter o valor total pago
        $total_pago = $result->getTransactions()[0]->getAmount()->getTotal();

        // Status do pagamento
        $status_pagamento = "Concluído";

        // Obter os dados da reserva da sessão 
        if (!isset($_SESSION['dados_reserva']) || empty($_SESSION['dados_reserva'])) {
            die("<p class='error-message'>Erro: Dados da reserva não encontrados. Volte à página de reserva.</p>");
        }

        $reserva = $_SESSION['dados_reserva'];

        $reserva = $_SESSION['dados_reserva'];

$sql = "SELECT marca, modelo FROM carros WHERE id_carro = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reserva['id_carro']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $dados_carro = $result->fetch_assoc();
    $marca = $dados_carro['marca'];
    $modelo = $dados_carro['modelo'];
} else {
    $marca = "Desconhecido";
    $modelo = "Desconhecido";
}

$stmt->close();

// Adicionar ao array da reserva para poderes usar no restante código
$reserva['marca'] = $marca;
$reserva['modelo'] = $modelo;


        // Gerar um código único para a reserva
        do {
            $codigo_reserva = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

            // Verificar se o código já existe
            $sql_check = "SELECT codigo_reserva FROM reservas WHERE codigo_reserva = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("s", $codigo_reserva);
            $stmt_check->execute();
            $stmt_check->store_result();
        } while ($stmt_check->num_rows > 0);

        $stmt_check->close();

        // Inserir os dados da reserva na tabela 'reservas'
        $sql_reserva = "INSERT INTO reservas (id_utilizador, id_carro, nome, email, contacto, data_inicio, data_fim, metodo_pagamento, preco_total, codigo_reserva, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmada')"; // Adicionado o campo status

$stmt_reserva = $conn->prepare($sql_reserva);

if ($stmt_reserva) {
    $stmt_reserva->bind_param(
        "iissssssds",
        $user_id, // ID do utilizador corretamente passado
        $reserva['id_carro'],
        $reserva['nome'],
        $reserva['email'],
        $reserva['contacto'],
        $reserva['data_inicio'],
        $reserva['data_fim'],
        $reserva['metodo_pagamento'],
        $reserva['preco_total'],
        $codigo_reserva
    );

    if (!$stmt_reserva->execute()) {
        die("<p class='error-message'>Erro ao salvar a reserva: " . $stmt_reserva->error . "</p>");
    }

    $stmt_reserva->close();
} else {
    die("<p class='error-message'>Erro ao preparar a consulta de reserva: " . $conn->error . "</p>");
}


        // Inserir os dados do pagamento na tabela 'pagamentos'
        $sql_pagamento = "INSERT INTO pagamentos (codigo_reserva, metodo_pagamento, status, total_pago, transaction_id) 
                          VALUES (?, ?, ?, ?, ?)";
        $stmt_pagamento = $conn->prepare($sql_pagamento);

        if ($stmt_pagamento) {
            $stmt_pagamento->bind_param("sssds", $codigo_reserva, $reserva['metodo_pagamento'], $status_pagamento, $total_pago, $transactionId);

            if (!$stmt_pagamento->execute()) {
                die("<p class='error-message'>Erro ao salvar o pagamento: " . $stmt_pagamento->error . "</p>");
            }

            $stmt_pagamento->close();
        } else {
            die("<p class='error-message'>Erro ao preparar a consulta de pagamento: " . $conn->error . "</p>");
        }

        // Enviar o e-mail de confirmação
        $to = $reserva['email'];
        $subject = "Confirmação de Reserva - Sprint Car";
        $message = file_get_contents('./email/email.html');

        // Substituir os placeholders com os dados reais
        $message = str_replace('{$reserva[\'nome\']}', $reserva['nome'], $message);
        $message = str_replace('{$codigo_reserva}', $codigo_reserva, $message);
        $message = str_replace('{$reserva[\'data_inicio\']}', $reserva['data_inicio'], $message);
        $message = str_replace('{$reserva[\'data_fim\']}', $reserva['data_fim'], $message);
        $message = str_replace('{$total_pago}', $total_pago, $message);
        $message = str_replace('{$reserva[\'metodo_pagamento\']}', $reserva['metodo_pagamento'], $message);

        $headers = "From: no-reply@sprintcar.com\r\n";
        $headers .= "Reply-To: suporte@sprintcar.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Confirmação de sucesso
    } else {
        die("<p class='error-message'>Pagamento não foi aprovado. Estado: " . $result->getState() . "</p>");
    }
} catch (Exception $e) {
    die("<p class='error-message'>Erro ao executar o pagamento: " . $e->getMessage() . "</p>");
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Pagamento</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/sucesso.css">
</head>
<body>
    <div class="container">
        <h1>Confirmação de Pagamento</h1>
        <p class="success-message">O seu pagamento foi processado com sucesso!</p>
        <div class="details">
            <p><strong>Código da Reserva:</strong> <span><?php echo $codigo_reserva; ?></span></p>
            <p><strong>Nome do Cliente:</strong> <span><?php echo htmlspecialchars($reserva['nome']); ?></span></p>
            <p><strong>Email:</strong> <span><?php echo htmlspecialchars($reserva['email']); ?></span></p>
            <p><strong>Total Pago:</strong> <span><?php echo number_format($total_pago, 2, ',', '.') . " €"; ?></span></p>
            <p><strong>Status do Pagamento:</strong> <span><?php echo $status_pagamento; ?></span></p>
            <p><strong>ID da Transação:</strong> <span><?php echo $transactionId; ?></span></p>
            <?php
            if (mail($to, $subject, $message, $headers)) {
                echo "<p class='success-message'>E-mail enviado com sucesso para {$reserva['email']}</p>";
            } else {
                echo "<p class='error-message'>Erro ao enviar o e-mail de confirmação.</p>";
            }

            // Gerar o recibo em PDF e enviar no segundo email

require 'vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Configuração do DOMPDF
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);


// Primeiro convertemos o logotipo em base64
$logoPath = 'img/logo.jpg';
$logoData = base64_encode(file_get_contents($logoPath));

// Agora o teu HTML completo com o logótipo embutido
$html = '
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333333; margin: 40px; }
    .header { text-align: center; margin-bottom: 30px; }
    .header img { width: 120px; margin-bottom: 10px; }
    .header h2 { color: #0066cc; margin-bottom: 5px; }
    .header p { font-size: 10px; color: #777777; margin-top: 0; }
    .section { margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; border: 1px solid #cccccc; }
    th, td { border: 1px solid #cccccc; padding: 10px; text-align: left; }
    th { background-color: #f2f2f2; }
    .footer { text-align: center; font-size: 10px; color: #777777; margin-top: 30px; }
</style>

<div class="header">
    <img src="data:image/jpeg;base64,'.$logoData.'" alt="SprintCar">
    <h2>Recibo de Pagamento</h2>
    <p>Este documento não substitui a fatura oficial. Apenas comprova o pagamento efetuado.</p>
</div>

<div class="section">
    <p><b>Data de Emissão:</b> ' . date('d/m/Y') . '</p>
    <p><b>Código da Reserva:</b> ' . $codigo_reserva . '</p>
    <p><b>ID da Transação PayPal:</b> ' . $transactionId . '</p>
</div>

<table>
    <tr><th>Cliente</th><td>' . htmlspecialchars($reserva['nome']) . '</td></tr>
    <tr><th>Viatura</th><td>' . htmlspecialchars($reserva['marca']) . ' ' . htmlspecialchars($reserva['modelo']) . '</td></tr>
    <tr><th>Período da Reserva</th><td>' . $reserva['data_inicio'] . ' a ' . $reserva['data_fim'] . '</td></tr>
    <tr><th>Valor Total Pago</th><td>' . number_format($total_pago, 2, ',', '.') . ' €</td></tr>
</table>

<div class="section">
    <p>Obrigado por confiar na SprintCar. Caso tenha alguma questão, estamos à disposição.</p>
</div>

<div class="footer">
    SprintCar &nbsp;|&nbsp; suporte@sprintcar.com &nbsp;|&nbsp; www.sprintcar.com<br>
</div>
';

// Gerar o PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdf = $dompdf->output();
$fatura_path = 'faturas/fatura_' . $codigo_reserva . '.pdf';
file_put_contents($fatura_path, $pdf);

// Preparar o segundo email com o PDF em anexo
$subject_recibo = "Recibo da sua reserva - Sprint Car";
$message_recibo = "Caro(a) {$reserva['nome']},\n\nSegue em anexo o recibo da sua reserva.\n\nObrigado pela sua preferência.";

$separator = md5(time());
$eol = "\r\n";
$file_size = filesize($pdf_filename);
$handle = fopen($pdf_filename, "r");
$content = fread($handle, $file_size);
fclose($handle);
$content = chunk_split(base64_encode($content));

$headers_recibo = "From: no-reply@sprintcar.com" . $eol;
$headers_recibo .= "MIME-Version: 1.0" . $eol;
$headers_recibo .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;

$body = "--" . $separator . $eol;
$body .= "Content-Type: text/plain; charset=\"UTF-8\"" . $eol . $eol;
$body .= $message_recibo . $eol;

$body .= "--" . $separator . $eol;
$body .= "Content-Type: application/octet-stream; name=\"" . $pdf_filename . "\"" . $eol;
$body .= "Content-Transfer-Encoding: base64" . $eol;
$body .= "Content-Disposition: attachment; filename=\"" . $pdf_filename . "\"" . $eol . $eol;
$body .= $content . $eol;
$body .= "--" . $separator . "--";

// Enviar o segundo email com o recibo
if (mail($to, $subject_recibo, $body, $headers_recibo)) {
    echo "<p class='success-message'>Recibo enviado com sucesso para {$reserva['email']}.</p>";
} else {
    echo "<p class='error-message'>Erro ao enviar o recibo.</p>";
}

// Apagar o ficheiro temporário

            ?>
        </div>
        <a href="index.php" class="btn">Voltar à Página Inicial</a>
    </div>
    <?php
// Limpa variáveis de sessão relacionadas à reserva
unset(
    $_SESSION['etapa'],
    $_SESSION['id_carro'],
    $_SESSION['data_inicio'],
    $_SESSION['data_fim'],
    $_SESSION['nome'],
    $_SESSION['email'],
    $_SESSION['contacto'],
    $_SESSION['metodo_pagamento'],
    $_SESSION['dados_reserva'],
    $_SESSION['reserva_sucesso']
);
?>
</body>
</html>
