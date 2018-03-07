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
    $st_array = $sentence_task_dao->getAnnotatedSentecesByTask($task->id);

// output headers so that the file is downloaded rather than displayed
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=task_' . $task->id . '-evaluation.csv');

// create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

// output the column headings
    //fputcsv($output, $labels);
// loop over the rows, outputting them
    $delimiter = "\t";
     foreach ($st_array as $st) {  
       $row = array($st->source_text, $st->target_text, $st->evaluation, $st->comments);
       fputcsv($output, $row, $delimiter);
     }  
    
  }
  else{
    //The user is not the owner
  }
}