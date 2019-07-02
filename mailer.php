<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/mail/PHPMailerAutoload.php");

    $mail = new PHPMailer;

    $mail->SMTPDebug = 3;
    $mail->isSMTP();
    $mail->Host = 'localhost';
    $mail->Port = 25;
    $mail->SMTPSecure = false;
    $mail->SMTPAutoTLS = false;

    $mail->setFrom('admin@example.com', 'Admin');
    $mail->addAddress('test1@example.com', 'Test 1');
    $mail->isHTML(true);

    $mail->Subject = 'PHP Test Mail';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b> <a href="#">Hola!</a>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
?>