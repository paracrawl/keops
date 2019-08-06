<?php
    require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
    require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
    require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_dto.php");
    require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/mail_helper.class.php");
    require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/templates/mail/contactform.php");

    $PAGETYPE = "public";
    require_once(RESOURCES_PATH . "/session.php");

    if (count(checkPostParameters(["from", "subject", "message", "g-recaptcha-response"])) != 0) {
        $_SESSION["contacterror"] = true;
        header("Location: /contact.php");
        exit();
    } else {
        $to = "test@example.com";
        $from = filter_input(INPUT_POST, "from", FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
        $subject = filter_input(INPUT_POST, "subject", FILTER_SANITIZE_STRING);
        $message = filter_input(INPUT_POST, "message", FILTER_SANITIZE_STRING);
        $copy = filter_input(INPUT_POST, "copy", FILTER_SANITIZE_STRING);
        $recaptcha = filter_input(INPUT_POST, "g-recaptcha-response");

        // We validate the CAPTCHA
        $recaptcha_verify = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create(
            array('http' => array(
                'method' => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query(array(
                    'secret' => '6LekjLEUAAAAAJCz1juAh32dHaFbfKq7La4h_7oz',
                    'response' => $recaptcha
                ))
            ))
        )));

        if ($recaptcha_verify == false || $recaptcha_verify->success == false) {
            $_SESSION["contacterror"] = true;
            header("Location: /contact.php");
            exit();
        }

        $user = new user_dto();
        $user->email = $from;
        $user->name = $name;
        
        $mail = new MailHelper();
        $mail->prepare(new MailTemplate(), (object) ["subject" => $subject, "message" => $message, "user" => $user]);
        $result = $mail->send($to);

        if ($copy == "on") {
            $mail->send($user->email);
        }

        $_SESSION["contacterror"] = false;
        header("Location: /contact.php");
        exit();
    }
?>