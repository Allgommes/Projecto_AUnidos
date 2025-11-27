<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->configureSMTP();
    }
    
    /**
     * Configurar SMTP
     */
    private function configureSMTP() {
        try {
            // Configurações do servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $_ENV['SMTP_USERNAME'] ?? '';
            $this->mailer->Password = $_ENV['SMTP_PASSWORD'] ?? '';
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $_ENV['SMTP_PORT'] ?? 587;
            $this->mailer->CharSet = 'UTF-8';
            
            // Remetente padrão
            $this->mailer->setFrom(
                $_ENV['MAIL_FROM_ADDRESS'] ?? 'gomesalvarogomes@gmail.com',
                $_ENV['MAIL_FROM_NAME'] ?? 'AUnidos'
            );
            
        } catch (Exception $e) {
            error_log("Erro ao configurar SMTP: " . $e->getMessage());
        }
    }
    
    /**
     * Enviar email genérico
     */
    public function sendEmail($to, $subject, $body, $altBody = '') {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $this->getEmailTemplate($subject, $body);
            $this->mailer->AltBody = $altBody ?: strip_tags($body);
            
            $this->mailer->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erro ao enviar email: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Enviar email de verificação
     */
    public function sendVerificationEmail($email, $nome, $token) {
        $subject = 'Verificação de Email - AUnidos';
        $verifyUrl = baseUrl("verify-email.php?token=" . $token);
        
        $body = "
            <h2>Bem-vindo ao AUnidos, {$nome}!</h2>
            <p>Obrigado por se registar na nossa plataforma.</p>
            <p>Para ativar a sua conta, clique no botão abaixo:</p>
            <p style='text-align: center; margin: 30px 0;'>
                <a href='{$verifyUrl}' style='background-color: #4CAF50; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                    Verificar Email
                </a>
            </p>
            <p style='color: #666; font-size: 12px;'>
                Se não conseguir clicar no botão, copie e cole o seguinte URL no seu navegador:<br>
                <a href='{$verifyUrl}'>{$verifyUrl}</a>
            </p>
            <p style='color: #666; font-size: 12px;'>
                Se não fez este pedido, por favor ignore este email.
            </p>
        ";
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    /**
     * Enviar email de recuperação de password
     */
    public function sendPasswordResetEmail($email, $nome, $token) {
        $subject = 'Recuperação de Password - AUnidos';
        $resetUrl = baseUrl("reset-password.php?token=" . $token);
        
        $body = "
            <h2>Recuperação de Password</h2>
            <p>Olá {$nome},</p>
            <p>Recebemos um pedido para recuperar a sua password.</p>
            <p>Para criar uma nova password, clique no botão abaixo:</p>
            <p style='text-align: center; margin: 30px 0;'>
                <a href='{$resetUrl}' style='background-color: #2196F3; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                    Recuperar Password
                </a>
            </p>
            <p style='color: #666; font-size: 12px;'>
                Se não conseguir clicar no botão, copie e cole o seguinte URL no seu navegador:<br>
                <a href='{$resetUrl}'>{$resetUrl}</a>
            </p>
            <p style='color: #d32f2f; font-size: 12px;'>
                <strong>Este link é válido por 1 hora.</strong>
            </p>
            <p style='color: #666; font-size: 12px;'>
                Se não fez este pedido, por favor ignore este email. A sua password não será alterada.
            </p>
        ";
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    /**
     * Enviar notificação de agendamento
     */
  /*  public function sendAgendamentoNotification($agendamentoId) {
        try {
            //$agendamentoModel = new \App\Models\Agendamento();
           // $agendamento = $agendamentoModel->getById($agendamentoId);
            
            if (!$agendamento) {
                return false;
            }
            
            // Email para o educador
            $subjectEducador = 'Novo Agendamento - AUnidos';
            $bodyEducador = "
                <h2>Novo Agendamento Recebido</h2>
                <p>Olá {$agendamento['educador_nome']},</p>
                <p>Recebeu um novo pedido de agendamento:</p>
                <ul>
                    <li><strong>Serviço:</strong> {$agendamento['servico_nome']}</li>
                    <li><strong>Cliente:</strong> {$agendamento['dono_nome']}</li>
                    <li><strong>Data/Hora:</strong> " . formatDateTime($agendamento['data_hora']) . "</li>
                    <li><strong>Contacto:</strong> {$agendamento['dono_telefone']}</li>
                </ul>
                <p>Aceda ao seu dashboard para confirmar ou gerir este agendamento.</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='" . baseUrl("dashboard.php") . "' style='background-color: #4CAF50; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        Ver Dashboard
                    </a>
                </p>
            ";
            
            $this->sendEmail($agendamento['educador_email'], $subjectEducador, $bodyEducador);
            
            // Email para o dono
            $subjectDono = 'Agendamento Criado - AUnidos';
            $bodyDono = "
                <h2>Agendamento Criado com Sucesso</h2>
                <p>Olá {$agendamento['dono_nome']},</p>
                <p>O seu pedido de agendamento foi criado:</p>
                <ul>
                    <li><strong>Serviço:</strong> {$agendamento['servico_nome']}</li>
                    <li><strong>Educador:</strong> {$agendamento['educador_nome']}</li>
                    <li><strong>Data/Hora:</strong> " . formatDateTime($agendamento['data_hora']) . "</li>
                    <li><strong>Estado:</strong> Pendente</li>
                </ul>
                <p>O educador irá confirmar o agendamento em breve. Será notificado quando houver uma atualização.</p>
                <p style='text-align: center; margin: 30px 0;'>
                    <a href='" . baseUrl("dashboard.php") . "' style='background-color: #2196F3; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        Ver Meus Agendamentos
                    </a>
                </p>
            ";
            
            $this->sendEmail($agendamento['dono_email'], $subjectDono, $bodyDono);
            
            return true;
            
        } catch (\Exception $e) {
            error_log("Erro ao enviar notificação de agendamento: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Template HTML para emails
     */
    private function getEmailTemplate($subject, $body) {
        return "
<!DOCTYPE html>
<html lang='pt'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$subject}</title>
</head>
<body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px 0;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                    <!-- Header -->
                    <tr>
                        <td style='background-color: #4CAF50; padding: 30px; text-align: center;'>
                            <h1 style='color: #ffffff; margin: 0; font-size: 28px;'>AUnidos</h1>
                            <p style='color: #ffffff; margin: 5px 0 0 0; font-size: 14px;'>Conectando Donos e Educadores Caninos</p>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style='padding: 40px 30px;'>
                            {$body}
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style='background-color: #f9f9f9; padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;'>
                            <p style='margin: 0; color: #666; font-size: 12px;'>
                                © " . date('Y') . " AUnidos. Todos os direitos reservados.
                            </p>
                            <p style='margin: 10px 0 0 0; color: #666; font-size: 12px;'>
                                Este é um email automático, por favor não responda.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
        ";
    }
}
