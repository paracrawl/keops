<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_langs_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_langs_dto.php");

$PAGETYPE = "public";
require_once(RESOURCES_PATH . "/session.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/password_renew_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/mail_helper.class.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/templates/mail/password_renew.php");

$failedparams = checkPostParameters(["service"]);

$service = filter_input(INPUT_POST, 'service');

if ($service == "renew") {
    if (count($failedparams) == 0) {
        $given_token = filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password');
        $password2 = filter_input(INPUT_POST, 'password2');
        $password_renew_dao = new password_renew_dao();
        $token = $password_renew_dao->getRenewToken($given_token);
        
        if ($password == $password2) {
            $password_renew_dao->revokeToken($given_token);
            if ($token->token == $given_token) {
                $then = strtotime($token->created_time . " UTC");
                $now = time();
                $diff = $now - $then; // in seconds
                
                if ($diff > (15 * 60)) {
                    $_SESSION["error"] = "expired";
                    header("Location: /forgot_password.php");
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $user_dao = new user_dao();
                    if ($user_dao->updateUserPassword($token->user_id, $hash)) {
                        $_SESSION["resetpassword"] = true;
                        header("Location: /signin.php");
                    } else {
                        $_SESSION["error"] = "unknownerror";
                        header("Location: /forgot_password.php");
                    }
                }
            } else {
                $_SESSION["error"] = "unknownerror";
                header("Location: /forgot_password.php");
            }
        } else {
            $_SESSION["error"] = "wrongpassword";
            header("Location: /renew_password.php?token=" . $given_token);
        }
    }
} else if ($service == "send") {
    $email = filter_input(INPUT_POST, "email");
    if (isset($email)) {
        $user_dao = new user_dao();
        $user = $user_dao->getUserByEmail($email);

        if ($user && $user->id != null) {
            $password_renew_dao = new password_renew_dao();
            $token = $password_renew_dao->generateRenewToken($user->id);

            $mail = new MailHelper();
            $mail->prepare(new PasswordResetTemplate(), 
                (object) ["token_url" => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . "/renew_password.php?token=" . $token->token]
            );

            $result = $mail->send($email);

            if ($result == true) {
                $_SESSION["error"] = "none";
            } else {
                $_SESSION["error"] = "unknownerror";
            }

            header("Location: /forgot_password.php");
        } else {
            $_SESSION["error"] = "wrongmail";
            header("Location: /forgot_password.php");
        }
    }
}
?>