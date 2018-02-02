<?php

$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");
$_SESSION['userinfo'] =  null;

header("Location: /index.php");
