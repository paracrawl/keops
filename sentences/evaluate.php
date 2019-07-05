<?php
/**
 * Evaluation page for a sentence
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");

  
$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$task_id = filter_input(INPUT_GET, "task_id");
$sentence_id = filter_input(INPUT_GET, "id");
$search_term = filter_input(INPUT_GET, "term");
$filter_label = filter_input(INPUT_GET, "label");

$filtered = isset($search_term) && isset($filter_label);

if (isset($task_id)) {
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);
  if ($task->id == $task_id && $task->assigned_user == $USER->id) {
    
    $sentence_task_dao = new sentence_task_dao();
    $paginate = filter_input(INPUT_GET, "p");
    if (isset($paginate) && $paginate == "1") {
      if ($filtered) {
        $sentence = $sentence_task_dao->gotoSentenceByTaskAndFilters($sentence_id, $task_id, $search_term, $filter_label);
      } else {
        $sentence = $sentence_task_dao->gotoSentenceByTask($sentence_id, $task_id);
      }
    }
    else if (isset($sentence_id)) {
      $sentence = $sentence_task_dao->getSentenceByIdAndTask($sentence_id, $task_id);
    }
    else {
      if ($task->status == "DONE" && isset($_GET['review'])) {
        // If the task is done but the review flag is set, we let the user
        // review their evaluated sentences. This will disable the Save button
        // below.
        $sentence = $sentence_task_dao->getFirstSentenceByTask($task_id);
      } else {
        $sentence = $sentence_task_dao->getNextPendingSentenceByTask($task_id);
      }
    }
    
    //TODO if the user reached the end of the task -> they should have an option to mark the task as DONE
    if($sentence->task_id == null) { // Check that sentence exists in db
      if (!$filtered) {
        // ERROR: Sentence doesn't exist
        // Message: We couldn't find this task for you
        header("Location: /tasks/recap.php?id=" . $task->id);
        die();
      }
    }
    
    $project_dao = new project_dao();
    $project = $project_dao->getProjectById($task->project_id);

    if ($filtered) {
      $task_progress = $sentence_task_dao->getCurrentProgressByIdAndTaskAndFilters($sentence->id, $task->id, $search_term, $filter_label);
    } else {
      $task_progress = $sentence_task_dao->getCurrentProgressByIdAndTask($sentence->id, $task->id);
    }
  }
  else {
      // ERROR: Task doesn't exist or it's already done or user is not the assigned to the task
      // Message: You don't have access to this evaluation / We couldn't find this task for you
      header("Location: /index.php");
      die();
  }
}
else {
  // ERROR: Task doesn't exist or it's already done or user is not the assigned to the task
  // Message: You don't have access to this evaluation / We couldn't find this task for you
  header("Location: /index.php");
  die();
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
    <div id="evaluation-container" class="container evaluation">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>

      <?php if ($task->status == "DONE") { ?>
        <div class="alert alert-success" role="alert">
          <b>This task is done!</b> The evaluation can be read but not changed. <a href="/tasks/recap.php?id=<?php echo $task->id; ?>">See the recap</a>.
        </div>
      <?php } ?>

      <ul class="breadcrumb" id="top">
        <li><a href="/index.php">Tasks</a></li>
        <li><a href="/sentences/evaluate.php?task_id=<?= $task->id ?>" title="Go to the first pending sentence">Evaluation of <?= $project->name ?> </a></li>
        <li class="active">Task #<?= $task->id ?></li> 
        <a class="pull-right"  href="mailto:<?= $project->owner_object->email  ?>" title="Contact Project Manager">Contact PM <span id="contact-mail-logo" class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a>
      </ul>

      <div class="col-md-12" style="margin-bottom: 1em;">
        <div class="page-header" style="padding-bottom: 0px;">
          <div class="row">
            <div class="col-sm-4" style="margin-bottom: 1em;">
              <span class="h1">Task #<?php echo $task->id ?></span>
            </div>

            <div class="col-sm-8 text-right">
              <form action="" class="form-inline">
                <div class="form-group">
                    <input type="hidden" name="task_id" value="<?= $task->id ?>" />
                    <input type="hidden" name="p" value="1" />
                    <input type="hidden" name="id" value="1" />

                    <input class="form-control" id="search-term" name="term" value="<?php if (isset($search_term)) { echo $search_term; } ?>" placeholder="Search through sentences">

                    <?php
                        $labels = array("P", "L", "A", "T", "MT", "F", "V");
                    ?>

                    <select class="form-control" name="label">
                    <option value="ALL">Everything</option>
                        <?php
                            foreach ($labels as $labelcode) { ?>
                                <option value="<?php echo $labelcode; ?>" <?php if (isset($filter_label) && $labelcode == $filter_label) { echo "selected"; } ?>><?php echo $labelcode; ?> label</option>
                            <?php }
                        ?>
                    </select>

                    <button type=submit class="btn btn-primary" id="search-term-button">Search</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <?php if ($sentence->task_id == NULL && $filtered) { ?>
        <div class="col-xs-12 col-md-12">
          <div class="alert alert-danger" role="alert">
            <strong>No sentences found with those filters!</strong>
            <a href="?p=1&id=1&task_id=<?= $task->id ?>">See all</a>
          </div>
        </div>
      <?php } else {?>

      <div class="col-md-6">
          <div class="row">
          <?php if ($filtered) { ?>
            <div class="col-xs-12 col-md-12">
              <div class="alert alert-warning" role="alert">
                <strong>Sentences filtered!</strong>
                <a href="?p=1&id=1&task_id=<?= $task->id ?>">See all</a>
              </div>
            </div>
          <?php } ?>
            <div class="col-xs-12 col-md-12 vcenter">
                <div class="row">
                  <div class="col-xs-6 col-md-6 text-increase">
                    <?= $project->source_lang_object->langname ?>
                  </div>
                  <div class="col-xs-6 col-md-6 text-right">
                    <a href="https://translate.google.com/?op=translate&sl=<?= $project->source_lang_object->langcode?>&tl=<?=$project->target_lang_object->langcode?>&text=<?=$sentence->source_text ?>"  title="Translate source sentence with Google" target="_blank"><span class="glyphicon glyphicon-new-window"></span></a>
                  </div>
                </div>
              </div>
            <div class="col-xs-12 col-md-12 vcenter" style="margin-bottom: 1em;">
              <div class="text-box text-increase">
                <?= $sentence->source_text ?>
              </div>
            </div>          
          </div>
          <div class="row">
              <div class="col-xs-12 col-md-12 vcenter">
                <div class="row">
                  <div class="col-xs-6 col-md-6 text-increase">
                    <?= $project->target_lang_object->langname ?>
                  </div>
                  <div class="col-xs-6 col-md-6 text-right">
                    <a href="https://translate.google.com/?op=translate&sl=<?= $project->target_lang_object->langcode?>&tl=<?=$project->source_lang_object->langcode?>&text=<?=$sentence->target_text ?>"  title="Translate target sentence with Google" target="_blank"><span class="glyphicon glyphicon-new-window"></span></a>
                  </div>
                </div>
              </div>
              <div class="col-xs-12 col-md-12 vcenter">
                <div class="text-box text-increase">
                  <?= $sentence->target_text ?>
                </div>
              </div>

              <div class="col-md-12 col-xs-12 text-right">
                Pair #<?= $sentence->id ?>
              </div>
          </div>
      </div>

      <div class="col-md-6">
        <form id="evaluation-form" class="form-horizontal" action="/sentences/sentence_save.php" role="form" method="post" data-toggle="validator">
          <input type="hidden" name="task_id" value="<?= $task->id ?>">
          <input type="hidden" name="sentence_id" value="<?= $sentence->id ?>">
          <div class="col-md-6">
            <h4>Annotation</h4>
            <p class="p1">Select one of the following labels to tag these parallel sentences</p>
          </div>
          <div data-toggle="modal" data-target="#evaluation-help" class="col-md-6 guidelines text-right">
            Validation guidelines <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
          </div>
          <div class="form-group col-md-12">
            <div class="col-md-6">
            <?php foreach (array_slice(sentence_task_dto::$labels, 0, count(sentence_task_dto::$labels) - 2) as $label) { ?>
              <div class="radio wrong-eval" title="<?= $label['title'] ?>">
                <label><input <?php if ($task->status == "DONE") { echo "disabled"; } ?> required <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" name="evaluation" value="<?= $label['value'] ?>"><?= underline($label['label'], $label['value']); ?></label>
              </div>
            <?php } ?>
            </div>
            <div class="col-md-6">
            <?php foreach (array_slice(sentence_task_dto::$labels, -2, 1) as $label) { ?>
              <div class="radio valid-eval" title="<?= $label['title'] ?>">
                <label><input <?php if ($task->status == "DONE") { echo "disabled"; } ?> required <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" name="evaluation" value="<?= $label['value'] ?>"><?= underline($label['label'], $label['value']) ?></label>
              </div>
            <?php } ?>
            <?php foreach (array_slice(sentence_task_dto::$labels, -1, 1) as $label) { ?>
              <div class="radio pending-eval" title="<?= $label['title'] ?>">
                <label><input <?php if ($task->status == "DONE") { echo "disabled"; } ?> required <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" name="evaluation" value="<?= $label['value'] ?>"><?= underline($label['label'], $label['value']) ?></label>
              </div>
            <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <label for="comments" class="col-sm-1 control-label">Comment</label>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <textarea <?php if ($task->status == "DONE") { echo "disabled"; } ?> rows="3" name="comments" id="comments" class="form-control" aria-describedby="helpComment" placeholder="Write something you want to remark for these parallel sentences [optional]" maxlength="1000" tabindex="4"><?= $sentence->comments ?></textarea>
            </div>
          </div>
          <div class="form-group" style="display: flex; align-items: baseline;">
            <div class="col-md-12 text-center">
              <?php if ($task->status != "DONE") { ?>
                <button id="evalution-save-button" type="submit" class="btn btn-primary" tabindex="5">Save</button>
              <?php } else { ?>
                <span class="btn btn-success btn-lg disabled" tabindex="5" title="The evaluation cannot be changed because the task is marked as done.">Task done</span>
              <?php } ?>
            </div>
          </div>
        </form>
      </div>
      <div class="col-md-12" style="margin-top: 2em;">
          <div class="col-md-4">
          <!--<form>
            <input type="hidden" id="task_id" name="task_id" value="<?= $task->id ?>">
            <input class="form-control search-term" id="search-term" name="search">
            <button class="btn btn-xs btn-link" id="search-term-button" >Search</button>          
          </form>-->
        </div>

        <div class="col-md-4">
          <div class="text-center text-increase">
            <ul class="pagination pagination-sm">
            <?php // TODO We are assuming that sentence ids are consecutive
              if ($task_progress->current > 1) { ?>
              <li><a href="/sentences/evaluate.php?task_id=<?= $task->id ?>&id=1&p=1<?php if (isset($search_term)) { echo "&term=".$search_term; } ?><?php if (isset($filter_label)) { echo "&label=".$filter_label; } ?>#top">First</a></li>
              <?php } else { ?>
              <li class="disabled"><a href="#">First</a></li>
              <?php } ?>
              
              <?php // TODO We are assuming that sentence ids are consecutive
              if ($task_progress->current > 1) { ?>
              <li>
                <a href="/sentences/evaluate.php?task_id=<?= $task->id ?>&p=1&id=<?= $task_progress->current-1 ?><?php if (isset($search_term)) { echo "&term=".$search_term; } ?><?php if (isset($filter_label)) { echo "&label=".$filter_label; } ?>#top">Prev</a>
              </li>
              <?php } else { ?>
              <li class="disabled"><a href="#">Prev</a></li>
              <?php } ?>
              
              <li class="active"><a href="#"><?= $task_progress->current ?> / <?= $task_progress->total ?></a></li>
              
              <?php // TODO We are assuming that sentence ids are consecutive
              if ($task_progress->current < $task_progress->total) { ?>
              <li><a href="/sentences/evaluate.php?task_id=<?= $task->id ?>&p=1&id=<?= $task_progress->current+1 ?><?php if (isset($search_term)) { echo "&term=".$search_term; } ?><?php if (isset($filter_label)) { echo "&label=".$filter_label; } ?>#top">Next</a></li>
              <?php } else { ?>
              <li class="disabled"><a href="#">Next</a></li>
              <?php } ?>
              
              <?php
              if ($task_progress->current < $task_progress->total) { ?>
                <li><a href="/sentences/evaluate.php?p=1&task_id=<?= $task->id ?>&id=<?= $task_progress->total ?><?php if (isset($search_term)) { echo "&term=".$search_term; } ?><?php if (isset($filter_label)) { echo "&label=".$filter_label; } ?>#top">Last</a></li>
              <?php } else { ?>
                <li class="disabled"><a href="#">Last</a></li>
              <?php } ?>
            </ul>
          </div>
        </div>
        <div class="col-md-2">
        </div>
        <div class="col-md-2">
          <form id="gotoform" method="get" action="/sentences/evaluate.php">
            <input type="hidden" name="p" value="1">
            <input type="hidden" name="task_id" value="<?= $task->id ?>">

            <?php if ($filtered) { ?>
              <input type="hidden" name="term" value="<?= $search_term ?>" />
              <input type="hidden" name="label" value="<?= $filter_label ?>" />
            <?php } ?>

            <input class="form-control go-to-page" id="gotopage" name="id" type="number" min="1" max="<?= $task_progress->total ?>" value="<?= $task_progress->current ?>">
            <button type="submit" class="btn btn-xs btn-link">Go!</button>
          </form>
        </div>
        
      </div>
      <div class="col-md-12">
        <div class="col-md-3">
        </div>
        <div class="col-md-6">
          <div class="progress" title="<?= $task_progress->completed . " of " . $task_progress->total . " senteces evaluated"; ?>" >
            <div  class="progress-bar" role="progressbar" aria-valuenow="<?= ($task_progress->completed / $task_progress->total) * 100 ?>"
                  aria-valuemin="0" aria-valuemax="100" style="width:<?= ($task_progress->completed / $task_progress->total) * 100 ?>%">  
              <span id="evaluate-percent"><?= round(($task_progress->completed / $task_progress->total) * 100, 2)?>%</span>
            </div>
          </div>
      </div>
        
          <div class="col-md-3">
          </div>
      </div>
      
    <?php } ?>
      <!-- Modal -->
      <div id="evaluation-help" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">How to annotate</h4>
            </div>
            <div class="modal-body">
              <h3>Taking the right decision</h3>
              <p><b>Only one type of error should be attributed to each TU.</b> The hierarchy presented below should be followed in the identification of errors:</p>
              <ul>
                <li>The <b>wrong language identification</b> is the most important error to tag, followed by <b>incorrect
                  alignment</b> and then, <b>wrong tokenization</b>. These features are related to the automatic
                  processing by the crawling / alignment tools. If any of these errors are detected, the translation
                  itself will not be checked more in depth.
                </li>
                <li>When no errors in the format are found, the content of the TU should be checked, focusing first
                  on potential <b>MT-translated content</b> (in this case all the TUs from such a source should be
                  discarded). Then major <b>translation errors</b> must be noted. If none of the errors described above
                  are detected and the translation contains minor differences in formulation it must be noted as
                  <b>free translation</b>.
                </li>
              </ul>
              <p>It is essential that the given <b>translation receives the benefit of the doubt</b>. Only clear errors should be indicated.</p>
              <p>
                In case you don't want to take a decision at the moment, you can go to next sentence and the current one will be kept as <b>pending</b>.
                Otherwise, you can mark the parallel sentence as <b>valid translation</b>.
              </p>
              <h3>Type explanation</h3>
              <p>Validators should use the following types/labels to tag problematic cases:</p>
              <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover table-condensed">
                  <thead>
                    <tr>
                      <th>Label</th>
                      <th>Shortcut</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Wrong language identification</td>
                      <td>L</td>
                    </tr>
                    <tr>
                      <td>Incorrect alignment</td>
                      <td>A</td>
                    </tr>
                    <tr>
                      <td>Wrong tokenization</td>
                      <td>T</td>
                    </tr>
                    <tr>
                      <td>MT translation</td>
                      <td>MT</td>
                    </tr>
                    <tr>
                      <td>Translation error</td>
                      <td>E</td>
                    </tr>
                    <tr>
                      <td>Free translation</td>
                      <td>F</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <p><b>Wrong language identification</b> means the crawler tools failed in identifying the right language.</p>
              <p><b>Incorrect alignment</b> refers to segments having a different content due to wrong alignment.</p>
              <p><b>Wrong tokenization</b> means the text has not been tokenized properly by the crawler tools (no separator between words).</p>
              <p><b>MT translation</b> refers to content identified as having been translated through a Machine Translation system. A few hints to detect if this is the case:</p>
              <ul>
                <li>
                  grammar errors such as gender and number agreement;
                </li>
                <li>
                  words that are not to be translated (trademarks for instance Nike Air => if ‘Air’ is translated
                  in the target language instead of being kept unmodified);
                </li>
                <li>
                  inconsistencies (use of different words for referring to the same object/person);
                </li>
                <li>
                  translation errors showing there is no human behind.
                </li>
              </ul>
              
              <p><b>Translation error</b> refers to:</p>
              <ul>
                <li>
                  Lexical errors (omitted/added words or wrong choice of lexical item, due to misinterpretation
                  or mistranslation),
                </li>
                <li>
                  Syntactic error (grammatical errors such as problems with verb tense, coreference and
                  inflection, misinterpretation of the grammatical relationships among the words in the text).
                </li>
                <li>
                  Poor usage of language (awkward, unidiomatic usage of the target language and failure to use
                  commonly recognized titles and terms). It could be due to MT translation.
                </li>
              </ul>

              <p><b>Free translation</b> means a non-literal translation in the sense of having the content completely
                  reformulated in one language (for editorial purposes for instance). This is a correct translation but in a
                  different style or form. This includes figures of speech such as metaphors, anaphors, etc. We consider
                  it as important to tag such cases for data that will be used for training MT systems.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/resources.php");
    ?>
    <script type="text/javascript" src="/js/evaluation.js"></script>
  </body>
</html>

