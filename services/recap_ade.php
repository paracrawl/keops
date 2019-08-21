<?php
/**
 * Project services.
 * Currently "new", that creates a new project and redirects to the Projects tab,
 *  and "list_dt", that serves the datatables content of the Projects table
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");


$service = filter_input(INPUT_POST, "service");
if ($service == "stats") {
  $failedparams = checkPostParameters(["task_id"]);
  if (count($failedparams) == 0){
    $task_id = filter_input(INPUT_POST, "task_id");
    $task_dao = new task_dao();

    try {
        $stats = $task_dao->getStatsForTask($task_id);
        echo json_encode(array("result" => 200, "stats" => $stats));
    } catch (Exception $e) {
        echo json_encode(array("result" => -1));
    }
  }
}