<?php
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/TSVWriter.class.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/comment_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");

class TSVGenerator {
    function generate_annotated($task_id, $output = 'php://output') {
        $task_dao = new task_dao();
        $task = $task_dao->getTaskById($task_id);
        $project_dao = new project_dao();
        $project = $project_dao->getProjectById($task->project_id);
        $sentence_task_dto = new sentence_task_dto();
    
        if ($task->status == "DONE") {
            $sentence_task_dao = new sentence_task_dao();
            $rows = array();
            $st_array = $sentence_task_dao->getAnnotatedSentecesByTask($task->id, false);
        
            if ($task->mode == "FLU") {
                $headers  = array("Target", "Target lang");
                $headers = array_merge($headers, array("Evaluation", "Description", "Evaluation details"));
                $rows[] = $headers;
            } else {
                $sample = (count($st_array) > 0) ? $st_array[0] : null;
                if (!isset($sample)) return;
        
                $headers  = array("Source");
        
                if ((isset($task->target_lang))) {
                for ($i = 0; $i < count($sample->target_text); $i++) {
                    $headers[] = (isset($sample->target_text[$i]->system) ? $sample->target_text[$i]->system : "Target " . ($i + 1));
                }
                }
        
                $headers[] = "Source lang";
                if (isset($task->target_lang)) $headers[] = "Target lang";
                $headers = array_merge($headers, array("Evaluation", "Description", "Evaluation details", "Time"));

                if ($task->mode == "ADE") {
                    $headers[] = "Type";
                }

                $rows[] = $headers;
            }
        
            $comment_dao = new comment_dao();
            $sentence_dao = new sentence_dao();

            foreach ($st_array as $st) {  
                $source_text = $st->source_text;
                $target_text = $st->target_text;
                
                // Comments
                $sentence_comments = $comment_dao->getCommentsByPair($st->id);
                $sentence_comment = array();
                foreach ($sentence_comments as $stc) {
                $sentence_comment[] = $stc->name . ": " . $stc->value;
                }
        
                $row = array($source_text);
                if ((isset($task->target_lang))) {
                    foreach($target_text as $text) {
                        $row[] = $text->source_text;
                    }
                }
        
                if (isset($task->source_lang)) $row[] = $task->source_lang;
                if (isset($task->target_lang)) $row[] = $task->target_lang;
                $row = array_merge($row, array($st->evaluation, $sentence_task_dto->getLabel($st->evaluation), implode($sentence_comment, "; ")));
                $row[] = $st->time;

                if ($task->mode == "ADE") {
                    $sentence = $sentence_dao->getSentenceById($st->sentence_id);
                    if ($sentence != null) {
                        $row[] = $sentence->type;
                    }
                }
                
                $rows[] = $row;
            }
            
            $tsv_writer = new TSVWriter();
            $tsv_writer->write($rows, $output);

            return true;
        } else {
            //The task is not done
            return false;
        }
    }
}
?>