<?php
require 'vendor/autoload.php'; // Carregar o autoloader do Composer
include 'paypal-config.php';  // Incluir a configuração do PayPal

// Verificar se os dados da reserva estão disponíveis na sessão
if (!isset($_SESSION['dados_reserva'])) {
    die("Erro: Dados da reserva não encontrados. Volte à página de reserva.");
}

// Obter os dados da reserva
$reserva = $_SESSION['dados_reserva'];
$preco_total = $reserva['preco_total'];
$descricao = "Reserva do carro: " . $reserva['carro'];

// PayPal - Criar pagamento
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;

$payer = new Payer();
$payer->setPaymentMethod('paypal');

$item = new Item();
$item->setName($descricao)
     ->setCurrency('EUR')
     ->setQuantity(1)
     ->setPrice($preco_total);

$itemList = new ItemList();
$itemList->setItems([$item]);

$amount = new Amount();
$amount->setCurrency('EUR')
       ->setTotal($preco_total);

$transaction = new Transaction();
$transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription('Pagamento da reserva')
            ->setInvoiceNumber(uniqid());

$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl("http://localhost/pap/Afonso_3GI/sucesso.php")
             ->setCancelUrl("http://localhost/pap/Afonso_3GI/cancelar.php");

$payment = new Payment();
$payment->setIntent('sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions([$transaction]);

try {
    $payment->create($apiContext);
    header("Location: " . $payment->getApprovalLink());
    exit;
} catch (Exception $ex) {
    die("Erro ao criar pagamento: " . $ex->getMessage());
}
?>
