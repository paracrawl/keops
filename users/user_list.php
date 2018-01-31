<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");
require_once(RESOURCES_PATH . "/session.php");

if (!$ADMIN_VIEW_PERMISSIONS) {
  header("Location: /index.php");
  die();
}

$user_dao = new user_dao();
echo $user_dao->getDatatablesUsers($_GET);
