<?php
session_start();
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
require 'vendor/autoload.php'; // Inclui o autoloader do Composer


// Configurar o contexto da API do PayPal
$apiContext = new ApiContext(
    new OAuthTokenCredential(
        'AQtzsOFQ-vWs56KL9ZzqVId991zUHkI7YtniEO0Y7H5qxLkMj22iiMHZwnc76EHO_IMIdYswTN-VNX14',     // Substituir pelo Client ID
        'ECczt-zAB5I0CqJ31WexV1O2CzYnSx7u-MgyOUbhDBpn6nAdsqzSA3YxlKB-sbBgBee3rG6EbqF3y6Ov'  // Substituir pelo Secret
    )
);

$apiContext->setConfig([
    'mode' => 'sandbox', // Alterar para 'live' quando for produção
    'log.LogEnabled' => true,
    'log.FileName' => 'PayPal.log',
    'log.LogLevel' => 'DEBUG', // DEBUG para desenvolvimento ou ERROR para produção
    'cache.enabled' => true,
]);
?>
