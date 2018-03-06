<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");

$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$sentence_task_dao = new sentence_task_dao();
$sentence_task_dto = new sentence_task_dto();

$task_id = filter_input(INPUT_POST, "task_id");
$search_term = filter_input(INPUT_POST, "search_term");
if (isset($task_id) && isset($search_term)) {
  echo $sentence_task_dao->getSentenceIdByTermAndTask($task_id, $search_term);  
}
else {
  echo 0;
}



