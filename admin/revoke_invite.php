<?php
/**
 * Revokes an invitation and redirects to the Invtations page
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/invite_dao.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$invite_dao = new invite_dao();
$invite_dao->revokeInvite($_GET["id"]);
header("Location: /admin/index.php#invitations");

