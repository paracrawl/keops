<?php
/**
 * Serves a file containing the annotated sentences for a task, ready to be downloaded
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/language_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$task_id = filter_input(INPUT_GET, "task_id");


if (isset($task_id)) {
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);
  $project_dao = new project_dao();
  $project = $project_dao->getProjectById($task->project_id);
  $sentence_task_dto = new sentence_task_dto();

  //  if ((($project->owner == $USER->id) || ($task->assigned_user == $USER->id)) && ($task->status == "DONE")) {
  if (($project->owner == $USER->id) && ($task->status == "DONE")) {
    $sentence_task_dao = new sentence_task_dao();
    $lang_dao = new language_dao();
    $st_array = $sentence_task_dao->getAnnotatedSentecesByTask($task->id);
    $source_lang_id = $project->source_lang;
    $target_lang_id = $project->target_lang;
    $source_lang = $lang_dao->getLanguageById($source_lang_id)->langcode;
    $target_lang = $lang_dao->getLanguageById($target_lang_id)->langcode;

    
// output headers so that the file is downloaded rather than displayed
    
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=task_' . $task->id . '-evaluation.tsv');

// create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    $delimiter = chr(9);
    $headers  = array("Source", "Target", "Source lang", "Target lang", "Evaluation", "Description", "Comments");
            
    fputs($output, $bom = ( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

// output the column headings
    fputs($output, implode($headers, $delimiter)."\n");
     foreach ($st_array as $st) {  
       $row = array($st->source_text, $st->target_text, $source_lang, $target_lang, $st->evaluation, $sentence_task_dto->getLabel($st->evaluation), $st->comments);
       fputs($output, implode($row, $delimiter)."\n");
     }  
    
  }
  else{
    //The user is not the owner
  }
}