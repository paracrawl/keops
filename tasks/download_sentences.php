<?php
/**
 * Serves a file containing the annotated sentences for a task, ready to be downloaded
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/comment_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/TSVGenerator.class.php");

$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$task_id = filter_input(INPUT_GET, "task_id");


if (isset($task_id)) {
  // output headers so that the file is downloaded rather than displayed
  header('Content-Encoding: UTF-8');
  header('Content-Type: text/csv; charset=UTF-8');
  header('Content-Disposition: attachment; filename=task_' . $task_id . '-evaluation.tsv');

  $generator = new TSVGenerator();
  $generator->generate_annotated($task_id);
}