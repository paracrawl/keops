<?php
/**
 * Retrieves the content of the Languages table, ready for datatables
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/language_dao.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$language_dao = new language_dao();
echo $language_dao->getDatatablesLanguages($_GET);
