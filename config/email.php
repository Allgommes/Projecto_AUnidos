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
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'gomesalvarogomes@gmail.com';
        $mail->Password   = 'mgtz foyz hsne xckb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 587;

        // CORREÇÃO: Adicionar opções SSL para evitar problemas de certificado
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Remetente e destinatário
        $mail->setFrom('gomesalvarogomes@gmail.com', 'AUnidos');
        $mail->addAddress($destinatario, $nome_destinatario);

$mail->send();
return true;
} catch (Exception $e) {
error_log("Mail error: " . $mail->ErrorInfo);
return false;
}
}
?>