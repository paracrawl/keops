<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/corpus_dao.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$corpus_dao = new corpus_dao();
echo $corpus_dao->getDatatablesCorpora($_GET);
