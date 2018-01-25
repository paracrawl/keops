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
    )/*,
    "urls" => array(
        "baseUrl" => "http://example.com"
    ),
    "paths" => array(
        "resources" => "/path/to/resources",
        "images" => array(
            "content" => $_SERVER["DOCUMENT_ROOT"] . "/images/content",
            "layout" => $_SERVER["DOCUMENT_ROOT"] . "/images/layout"
        )
    )*/
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