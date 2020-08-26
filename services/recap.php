<?php
/**
 * Project services.
 * Currently "new", that creates a new project and redirects to the Projects tab,
 *  and "list_dt", that serves the datatables content of the Projects table
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");
$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");


$service = filter_input(INPUT_POST, "service");
if ($service == "stats_ade") {
  $failedparams = checkPostParameters(["type", "task_id"]);
  if (count($failedparams) == 0){
    $type = filter_input(INPUT_POST, "type");
    
    if ($type == "intra") {
      $task_id = filter_input(INPUT_POST, "task_id");
      $mode = filter_input(INPUT_POST, "mode") ?? "ADE";
      $task_dao = new task_dao();

      try {
          $stats = $task_dao->getStatsForTask($task_id, $mode);
          echo json_encode(array("result" => 200, "stats" => $stats));
      } catch (Exception $e) {
          echo json_encode(array("result" => -1));
      }
    } else if ($type == "inter") {
      $task_id = filter_input(INPUT_POST, "task_id");
      $mode = filter_input(INPUT_POST, "mode") ?? "ADE";
      $task_dao = new task_dao();

      try {
          $stats = $task_dao->getInterStatsForTask($task_id, $mode);
          echo json_encode(array("result" => 200, "stats" => $stats));
      } catch (Exception $e) {
          echo json_encode(array("result" => -1));
      }
    } else {
      echo json_encode(array("result" => -1));
    }
  } else {
    echo json_encode(array("result" => -1));
  }
} else if ($service = "stats_ran") {
  $failedparams = checkPostParameters(["task_id"]);
  if (count($failedparams) > 0) {
    echo json_encode(array("result" => -1));
    return;
  }

  $task_id = filter_input(INPUT_POST, "task_id");
  $sentence_task_dao = new sentence_task_dao();
  $sentences = $sentence_task_dao->getAnnotatedSentecesByTask($task_id);
  $scores = array();

  foreach ($sentences as $sentence) {
    $ranking = json_decode($sentence->evaluation, true);
    $systems = array_keys($ranking);
    foreach ($systems as $system) {
      if (array_key_exists($system, $scores)) {
        $scores[$system] += ($ranking[$system] == 1) ? 1 : 0;;
      } else {
        $scores[$system] = ($ranking[$system] == 1) ? 1 : 0;
      }
    }
  }

  echo json_encode(array("result" => 200, "stats" => $scores));
} else {
  echo json_encode(array("result" => -1));
}