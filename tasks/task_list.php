<?php
/**
 * Retrieves the content of the Tasks table, ready for datatables, if no "corpus_id" parameter is given.
 * If the "corpus_id" parameter is given, retrieves the content of the TaskByCorpus table (ready for datatables)
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$task_dao = new task_dao();
        
if (isset($_GET["corpus_id"])){
 echo $task_dao->getDatatablesTasksByCorpus($_GET) ;
}
else {
  echo $task_dao->getDatatablesTasks($_GET);
}
