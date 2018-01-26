<?php
$config = array(
    "db" => array(
        "db1" => array(
            "dbname" => "keopsdb",
            "username" => "keopsdb",
            "password" => ".keopsdb.",
            "host" => "localhost",
            "port" => "5432"
        )
    )
);

defined("TEMPLATES_PATH")
    or define("TEMPLATES_PATH", realpath(dirname(__FILE__) . '/templates'));


/*$to      = 'jferrandez@prompsit.com';
$subject = 'the subject';
$message = 'hello1';
$headers = 'From: jferrandez@prompsit.com' . "\r\n" .
    'Reply-To: jferrandez@prompsit.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
*/