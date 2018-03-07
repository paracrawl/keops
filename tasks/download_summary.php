<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$task_id = filter_input(INPUT_GET, "task_id");


if (isset($task_id)) {
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);
  $project_dao = new project_dao();
  $project = $project_dao->getProjectById($task->project_id);

  if (($project->owner == $USER->id) && ($task->status == "DONE")) {
    $sentence_task_dao = new sentence_task_dao();
    $task_stats_dto = $sentence_task_dao->getStatsByTask($task->id);

// output headers so that the file is downloaded rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=task_' . $task->id . '-summary.csv');

    $csv_header = array("Label", "Description", "Count");
// create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');
    //excel header
    fputs($output, $bom = ( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
// output the column headings
    //fputcsv($output, $labels);
// loop over the rows, outputting them
    fputs($output, implode($csv_header, ",") . "\n");
    foreach (sentence_task_dto::$labels as $label) {
      $array = array($label["value"], $label["label"], $task_stats_dto->array_type[$label['value']]);
      fputs($output, implode($array, ",") . "\n");
    }
    $total_row = array("Total", "Total", $task_stats_dto->total);
    fputs($output, implode($total_row, ",") . "\n");
  } else {
    //The user is not the owner
  }
}