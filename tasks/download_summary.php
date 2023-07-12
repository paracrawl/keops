<?php
/**
 * Serves a file containing the summary of a task, ready to be downloaded
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/TSVWriter.class.php");

$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$task_id = filter_input(INPUT_GET, "task_id");


if (isset($task_id)) {
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);
  $project_dao = new project_dao();
  $project = $project_dao->getProjectById($task->project_id);

  if ($task->status == "DONE") {
    $rows = array();

    if ($task->mode == "VAL" || $task->mode=="VAL_MAC" || $task->mode=="MONO") {
      $sentence_task_dao = new sentence_task_dao();
      $task_stats_dto = $sentence_task_dao->getStatsByTask($task->id);

      $rows[] = array("Label", "Description", "Count", "Percentage");

      foreach (sentence_task_dto::$labels as $label) {
        $rows[] = array($label["value"], $label["label"], $task_stats_dto->array_type[$label['value']], (($task_stats_dto->array_type[$label['value']]) / $task_stats_dto->total) * 100);
      }

      $rows[] = array("Total", "Total", $task_stats_dto->total);
    } else if ($task->mode == "ADE" || $task->mode == "FLU") {
      $stats = $task_dao->getStatsForTask($task_id, $task->mode);

      $rows[] = array("Percentage", "# of sentences");
      for ($i = 0; $i < 110; $i += 10) {
        $amount = (array_key_exists($i, $stats)) ? $stats[$i] : 0;
        $rows[] = array($i, $amount);
      }
    } else if ($task->mode == "RAN") {
      $sentence_task_dao = new sentence_task_dao();
      $sentences = $sentence_task_dao->getAnnotatedSentecesByTask($task->id);
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

      $rows[] = array("System", "Position");
      foreach ($systems as $system) {
        $rows[] = array($system, $scores[$system]);
      }
    }

    // output headers so that the file is downloaded rather than displayed
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/tab-separated-values; charset=utf-8');
    header('Content-Disposition: attachment; filename=task_' . $task->id . '-summary.tsv');
    $tsv_writer = new TSVWriter();
    $tsv_writer->write($rows);
  } else {
    //The task is not done
  }
}