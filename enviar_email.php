<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $assunto = $_POST['assunto'];

    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Altere para o servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@example.com'; // Altere para seu e-mail
        $mail->Password = 'your-email-password'; // Altere para sua senha
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Remetente e destinatário
        $mail->setFrom($email, $nome);
        $mail->addAddress('matheusdahmer22@gmail.com'); // Seu e-mail

        // Conteúdo do e-mail
        $mail->isHTML(false);
        $mail->Subject = 'Solicitação de Orçamento';
        $mail->Body = "Nome: $nome\nE-mail: $email\nTelefone: $telefone\n\nAssunto:\n$assunto";

        $mail->send();
        echo "<script>alert('E-mail enviado com sucesso!'); window.location.href = 'contato.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Falha ao enviar e-mail: {$mail->ErrorInfo}'); window.location.href = 'contato.php';</script>";
    }
} else {
    echo "Método de requisição inválido.";
}
?>
