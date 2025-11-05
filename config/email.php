<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function enviar_email($destinatario, $nome_destinatario, $assunto, $mensagem)
{
    // Verificar se os ficheiros do PHPMailer existem
    if (
        !file_exists('PHPMailer/src/Exception.php') ||
        !file_exists('PHPMailer/src/PHPMailer.php') ||
        !file_exists('PHPMailer/src/SMTP.php')
    ) {
        error_log("Erro: Ficheiros do PHPMailer não encontrados");
        return false;
    }

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor - CORREÇÃO: Adicionar opções SSL
        $mail->isSMTP();
        $mail->Host       = 'smtp.sapo.pt';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gomesme_meting@sapo.pt';
        $mail->Password   = '5Pz6jqfOr$3C';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // CORREÇÃO: Adicionar opções SSL para evitar problemas de certificado
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Remetente e destinatário
        $mail->setFrom('gomesme_meting@sapo.pt', 'AUnidos');
        $mail->addAddress($destinatario, $nome_destinatario);

$mail->send();
return true;
} catch (Exception $e) {
error_log("Mail error: " . $mail->ErrorInfo);
return false;
}
}
?>