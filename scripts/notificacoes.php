<?php
// Verifica se há uma notificação para exibir
if (isset($_SESSION['notification'])) {
    $type = $_SESSION['notification']['type']; // 'success' (verde) ou 'error' (vermelho)
    $message = $_SESSION['notification']['message'];
    $class = ($type === 'success') ? 'success' : 'error';

    echo "
        <style>
            .notification-container {
                display: flex;
                justify-content: center;
                margin: 10px 0;
            }

            .notification {
                max-width: 600px;
                width: 100%;
                text-align: center;
                padding: 10px;
                border-radius: 5px;
                font-weight: bold;
                font-size: 16px;
            }

            .notification.success {
                background-color: #d4edda;
                color: #155724;
                border-left: 5px solid #155724;
            }

            .notification.error {
                background-color: #f8d7da;
                color: #721c24;
                border-left: 5px solid #721c24;
            }
        </style>

        <div class='notification-container'>
            <div class='notification $class'>
                $message
            </div>
        </div>
    ";

    // Remove a notificação da sessão após exibi-la
    unset($_SESSION['notification']);
}
?>
