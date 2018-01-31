<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/language_dao.php");
require_once(RESOURCES_PATH . "/session.php");

if (!$ADMIN_VIEW_PERMISSIONS) {
  header("Location: /index.php");
  die();
}

$language_dao = new language_dao();
echo $language_dao->getDatatablesLanguages($_GET);
