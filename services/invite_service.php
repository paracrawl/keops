<?php
    require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
    require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/mail_helper.class.php");
    require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/templates/mail/invitation.php");

    $PAGETYPE = "admin";
    require_once(RESOURCES_PATH . "/session.php");

    $to = filter_input(INPUT_POST, "to");
    $token_url = filter_input(INPUT_POST, "token_url");

    if (!isset($to) || !isset($token_url)) {
        echo json_encode(array("result" => false));
    } else {
        $mail = new MailHelper();
        $mail->prepare(new MailTemplate(), array());

        $result = $mail->send($to);

        echo json_encode(array("result" => $result));
    }
?>