<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$task_id = filter_input(INPUT_GET, "task_id");
$sentence_id = filter_input(INPUT_GET, "id");

if (isset($task_id)) {
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);
  if ($task->id == $task_id && $task->status != "DONE" && $task->assigned_user == $USER->id) {
    
    $sentence_task_dao = new sentence_task_dao();
    if (isset($sentence_id)) {
      $sentence = $sentence_task_dao->getSentenceByIdAndTask($sentence_id, $task_id);
    }
    else {
      $sentence = $sentence_task_dao->getNextPendingSentenceByTask($task_id);
    }
    //TODO if the user reached the end of the task -> they should have an option to mark the task as DONE
    if($sentence->task_id == null) { // Check that sentence exists in db
      // ERROR: Sentence doesn't exist
      // Message: We couldn't find this task for you
      header("Location: /index.php");
      die();
    }
    
    $project_dao = new project_dao();
    $project = $project_dao->getProjectById($task->project_id);
    
    $task_progress = $sentence_task_dao->getTaskCurrentProgress($sentence->id, $task->id);
  }
  else {
    // ERROR: Task doesn't exist or it's already done or user is not the assigned to the task
    // Message: You don't have access to this evaluation / We couldn't find this task for you
    header("Location: /index.php");
    die();
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Evaluation of task #<?= $task->id ?> - <?= $project->name ?></title>
    <?php
    require_once(TEMPLATES_PATH . "/head.php");
    ?>
  </head>
  <body>
    <div class="container evaluation">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="breadcrumb">
        <li><a href="/index.php">Tasks</a></li>
        <li><a href="/evaluate.php?task_id=<?= $task->id ?>">Evaluation of <?= $project->name ?></a></li>
        <li class="active">Task #<?= $task->id ?></li>
      </ul>
      <div class="col-md-6">
        <div class="text-center text-increase"><?= $project->source_lang_object->langname ?></div>
      </div>
      <div class="col-md-6">
        <div class="text-center text-increase"><?= $project->target_lang_object->langname ?></div>
      </div>
      <div class="col-md-6">
        <div class="text-box text-increase"><?= $sentence->source_text ?></div>
      </div>
      <div class="col-md-6">
        <div class="text-box text-increase"><?= $sentence->target_text ?></div>
      </div>
      <div class="col-md-12">
        <h4 class="text-center">Annotation</h4>
        <p class="text-center">Select one of the following labels to tag these parallel sentences</p>
        <form class="form-horizontal" action="/sentences/sentence_save.php" role="form" method="post" data-toggle="validator">
          <input type="hidden" name="task_id" value="<?= $task->id ?>">
          <input type="hidden" name="sentence_id" value="<?= $sentence->id ?>">
          <div class="form-group">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
            <?php foreach (sentence_task_dto::$labels as $label) { ?>
              <div class="radio" title="<?= $label['title'] ?>">
                <label><input required <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" name="evaluation" value="<?= $label['value'] ?>"><?= $label['label'] ?> [<?= $label['value'] ?>]</label>
              </div>
            <?php } ?>
            </div>
            <div class="col-md-3">
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
              <label for="comments" class="col-sm-1 control-label">Comment</label>
            </div>
            <div class="col-md-3">
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-3">
            </div>
            <div class="col-md-6">
              <textarea name="comments" id="comments" class="form-control" aria-describedby="helpComment" placeholder="Write about something you want to remark for these parallel sentences" maxlength="1000" tabindex="4"><?= $sentence->comments ?></textarea>
            </div>
            <div class="col-md-3">
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-3 text-center">
            </div>
            <div class="col-md-6 text-center">
              <button type="submit" class="btn btn-primary" tabindex="5">Save</button>
            </div>
            <div class="col-md-3 text-center">
            </div>
          </div>
        </form>
      </div>
      <div class="col-md-12">
        <div class="text-center text-increase">
          <ul class="pagination pagination-sm">
            <?php // TODO We are assuming that sentence ids are consecutive
            if ($task_progress->current > 1) { ?>
            <li><a href="/sentences/evaluate.php?task_id=<?= $task->id ?>&id=<?= $sentence->id-1 ?>">Prev</a></li>
            <?php } else { ?>
            <li class="disabled"><a href="#">Prev</a></li>
            <?php } ?>
            <li class="active"><a href="#"><?= $task_progress->current ?> / <?= $task_progress->total ?></a></li>
            <?php // TODO We are assuming that sentence ids are consecutive
            if ($task_progress->current < $task_progress->total) { ?>
            <li><a href="/sentences/evaluate.php?task_id=<?= $task->id ?>&id=<?= $sentence->id+1 ?>">Next</a></li>
            <?php } else { ?>
            <li class="disabled"><a href="#">Next</a></li>
            <?php } ?>
          </ul>
        </div>
      </div>
      <div class="col-md-3">
      </div>
      <div class="col-md-6">
        <div class="progress">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?= ($task_progress->current / $task_progress->total)*100 ?>"
          aria-valuemin="0" aria-valuemax="100" style="width:<?= ($task_progress->current / $task_progress->total)*100 ?>%">
            <?= ($task_progress->current / $task_progress->total)*100 ?>%
          </div>
        </div>
      </div>
      <div class="col-md-3">
      </div>
      <p style="display:none">4. To ensure consistency from one validator to another, the following system has been adopted for
      grading translations. Validators should use the following types/labels to tag problematic cases:
Label Shortcut
Wrong language identification L
Incorrect alignment A
Wrong tokenization T
MT translation MT
Translation error E
Free translation F
Wrong language identification means the crawler tools failed in identifying the right language.
Incorrect alignment refers to segments having a different content due to wrong alignment.
Wrong tokenization means the text has not been tokenized properly by the crawler tools (no separator
between words).
MT translation refers to content identified as having been translated through a Machine Translation
system. A few hints2
to detect if this is the case:
● grammar errors such as gender and number agreement;
● words that are not to be translated (trademarks for instance Nike Air => if ‘Air’ is translated
in the target language instead of being kept unmodified);
● inconsistencies (use of different words for referring to the same object/person);
● translation errors showing there is no human behind.
Translation error refers to:
● Lexical errors (omitted/added words or wrong choice of lexical item, due to misinterpretation
or mistranslation),
● Syntactic error (grammatical errors such as problems with verb tense, coreference and
inflection, misinterpretation of the grammatical relationships among the words in the text).
● Poor usage of language (awkward, unidiomatic usage of the target language and failure to use
commonly recognized titles and terms). It could be due to MT translation.
Free translation means a non-literal translation in the sense of having the content completely
reformulated in one language (for editorial purposes for instance). This is a correct translation but in a
different style or form. This includes figures of speech such as metaphors, anaphors, etc. We consider
it as important to tag such cases for data that will be used for training MT systems.
5. Only one type of error should be attributed to each TU. The hierarchy presented below should be
followed in the identification of errors:
a. The wrong language identification is the most important error to tag, followed by incorrect
alignment and then, wrong tokenization. These features are related to the automatic
processing by the crawling / alignment tools. If any of these errors are detected, the translation
itself will not be checked more in depth.
b. When no errors in the format are found, the content of the TU should be checked, focusing first
on potential MT-translated content (in this case all the TUs from such a source should be
discarded). Then major translation errors must be noted. If none of the errors described above
are detected and the translation contains minor differences in formulation it must be noted as
free translation.
6. It is essential that the given translation receives the benefit of the doubt. Only clear errors should be
indicated.</p>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/resources.php");
    ?>
  </body>
</html>
