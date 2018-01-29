<?php
defined("TEMPLATES_PATH")
    or define("TEMPLATES_PATH", realpath(dirname(__FILE__) . '/templates'));

defined("RESOURCES_PATH")
    or define ("RESOURCES_PATH", realpath(dirname(__FILE__)));

/*$to      = 'jferrandez@prompsit.com';
$subject = 'the subject';
$message = 'hello1';
$headers = 'From: jferrandez@prompsit.com' . "\r\n" .
    'Reply-To: jferrandez@prompsit.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
*/