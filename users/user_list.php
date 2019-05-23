<?php
/**
* Retrieves the content of the Users table, ready for datatables
*/
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/user_dao.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$user_dao = new user_dao();
echo $user_dao->getDatatablesUsers($_GET);
