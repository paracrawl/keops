<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(RESOURCES_PATH . "/mail/src/Exception.php");
require_once(RESOURCES_PATH . "/mail/src/PHPMailer.php");
require_once(RESOURCES_PATH . "/mail/src/SMTP.php");

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/mail_template.interface.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/config.php");

class MailHelper {
    private $mail = null;
    private $KEOPS_USER = null;
    private $KEOPS_PWD = null;

    public function __construct() {
        $this->KEOPS_USER = getenv('KEOPS_HELPER_EMAIL');
        $this->KEOPS_PWD = getenv('KEOPS_HELPER_PASSWORD');

        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host       = 'mail.prompsit.com';
        $this->mail->SMTPAuth   = true; 
        $this->mail->Username   = $this->KEOPS_USER;
        $this->mail->Password   = $this->KEOPS_PWD;
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port       = 587;
        $this->mail->CharSet   = 'UTF-8';
        $this->mail->Encoding  = 'base64';
        $this->mail->setFrom($this->KEOPS_USER, 'KEOPS');
        $this->mail->isHTML(true);
    }

    public function prepare(MailTemplateI $template, $params) {
        $this->mail->isHTML(true);
        $this->mail->Subject = $template->getSubject($params);
        $this->mail->Body = MailHelper::generateHTML($template, $params);
    }

    public function send($to, $name = '') {
        try {
            $this->mail->addAddress($to, $name);
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addCC($to) {
        try {
            $this->mail->addCC($to);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function generateHTML(MailTemplateI $template, $params) {
        $html = "<!doctype html><html><head><meta charset='utf-8' />" . $template->getHead($params) . "</head><body>" . $template->getBody($params) . "</body></html>";
        return $html;
    }
}