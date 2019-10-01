<?php
/**
 * Retrieves a list of invitations, in a datatables-friendly format
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/invite_dao.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$invite_dao = new invite_dao();
echo $invite_dao->getDatatablesInvited($_GET, getUserId());
