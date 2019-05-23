<?php
/**
 * Logs out the user, and redirects to the index page.
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
$PAGETYPE = "public";
require_once(RESOURCES_PATH . "/session.php");
$_SESSION['userinfo'] =  null;

header("Location: /index.php");
die();