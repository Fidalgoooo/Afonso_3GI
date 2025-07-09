<?php
function responderAutomaticamente($mensagemCliente, $cliente_id, $conn) {
    session_start();

    $mensagem = strtolower(trim($mensagemCliente));
    $autor = "bot";

    $mensagemFinal = "Posso ajudar com mais alguma coisa? Escreva pagamento, levantamento ou dúvida.\nOu escreva menu para ver as opções.";

    if ($mensagem === "menu") {
        $_SESSION['estado_chat'] = 'inicio';
        $resposta = "Escolha uma opção:\n- Métodos de Pagamento\n- Locais de Levantamento\n- Outra Dúvida";
        gravarResposta($conn, $cliente_id, $resposta, $autor);
        return;
    }

    if (!isset($_SESSION['estado_chat'])) {
        $_SESSION['estado_chat'] = 'inicio';
    }

    switch ($_SESSION['estado_chat']) {
        case 'inicio':
            if (str_contains($mensagem, 'pagamento') || str_contains($mensagem, 'método') || str_contains($mensagem, 'metodo')) {
                $_SESSION['estado_chat'] = 'inicio';
                gravarResposta($conn, $cliente_id, "Aceitamos os seguintes métodos de pagamento:\n✔ Cartão de Crédito\n✔ PayPal\n\nApós o pagamento, receberá um email com a confirmação e o recibo da compra.", $autor);
                gravarResposta($conn, $cliente_id, $mensagemFinal, $autor);
                return;

            } elseif (str_contains($mensagem, 'levantamento') || str_contains($mensagem, 'local')) {
                $_SESSION['estado_chat'] = 'levantamento_1';
                $resposta = "Temos 3 locais disponíveis para levantamento: Porto, Lisboa e Faro.\n\nPretende saber sobre devoluções também?";
                // ⚠️ NÃO enviar mensagemFinal aqui
            } elseif (str_contains($mensagem, 'dúvida') || str_contains($mensagem, 'duvida')) {
                $_SESSION['estado_chat'] = 'duvida_1';
                $resposta = "Pode explicar melhor a sua dúvida? Tentarei ajudar o mais rápido possível.";
                // ⚠️ NÃO enviar mensagemFinal aqui
            } else {
                $resposta = "Escolha uma opção:\n- Métodos de Pagamento\n- Locais de Levantamento\n- Outra Dúvida";
                gravarResposta($conn, $cliente_id, $resposta, $autor);
                gravarResposta($conn, $cliente_id, $mensagemFinal, $autor);
                return;
            }
            break;

        case 'levantamento_1':
            if (str_contains($mensagem, 'devolução') || str_contains($mensagem, 'devolucao') || str_contains($mensagem, 'sim')) {
                $_SESSION['estado_chat'] = 'inicio';
                gravarResposta($conn, $cliente_id, "A devolução deve ser feita no mesmo local onde levantou o veículo.", $autor);
                gravarResposta($conn, $cliente_id, $mensagemFinal, $autor);
                return;
            } else {
                $resposta = "Os locais de levantamento disponíveis são:\n✔ Porto\n✔ Lisboa\n✔ Faro\n\nSe tiver dúvidas sobre a devolução, escreva 'sim'.";
                // ⚠️ NÃO enviar mensagemFinal aqui
            }
            break;

        case 'duvida_1':
            $_SESSION['estado_chat'] = 'inicio';
            gravarResposta($conn, $cliente_id, "A sua mensagem foi encaminhada para um administrador, que irá entrar em contacto consigo em breve.", $autor);
            return;
    }

    // Grava a resposta normal
    gravarResposta($conn, $cliente_id, $resposta ?? "Não percebi a sua mensagem. Pode repetir?", $autor);
}

function gravarResposta($conn, $cliente_id, $mensagem, $autor) {
    $stmt = $conn->prepare("INSERT INTO suporte_chat (cliente_id, mensagem, enviado_por) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $cliente_id, $mensagem, $autor);
    $stmt->execute();
}
 