<?php
/**
 * Evaluation page for a sentence
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/sentence_task_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/project_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/sentence_task_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/comment_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/utils/utils.php");

  
$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$task_id = filter_input(INPUT_GET, "task_id");
$sentence_id = filter_input(INPUT_GET, "id");
$search_term = filter_input(INPUT_GET, "term");
$filter_label = filter_input(INPUT_GET, "label");

$filtered = isset($search_term) && isset($filter_label);

$tabs = (object) [
  ["label" => "L", "comments" => [
    [
      "name" => "L_sentence",
      "text" => "Sentence with wrong language identification",
      "options" => [
        ["value" => "source", "text" => "[SOURCE]"],
        ["value" => "target", "text" => "[TARGET]"]
      ]
    ]
  ]],
  ["label" => "MT", "comments" => [
    [
      "name" => "MT_direction",
      "text" => "MT translation",
      "options" => [
        ["value" => "source", "text" => "[SOURCE] to [TARGET]"],
        ["value" => "target", "text" => "[TARGET] to [SOURCE]"]
      ]
    ]
  ]]
];

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
            <b>This task is done!</b> The evaluation can be read but not changed. <a href="/tasks/recap.php?id=<?php echo $task->id; ?>" class="alert-link">See the recap</a>.
        </div>
      <?php } ?>

      <ul class="breadcrumb" id="top">
        <li><a href="/index.php">Tasks</a></li>
        <li><a href="/sentences/evaluate.php?task_id=<?= $task->id ?>" title="Go to the first pending sentence">Evaluation of <?= $project->name ?> </a></li>
        <li class="active">Task #<?= $task->id ?></li> 
        <a class="pull-right"  href="mailto:<?= $project->owner_object->email  ?>" title="Contact Project Manager">Contact PM <span id="contact-mail-logo" class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a>
      </ul>

      <div class="row">
        <div class="col-md-12 page-header row mx-0">
          <div class="col-sm-4 col-xs-12">
            <span class="h2">Task #<?php echo $task->id ?> <small><?= ($task_progress->completed / $task_progress->total) * 100 ?>% done</small></span>
          </div>

          <div class="col-sm-8 col-xs-12 text-right">
            <form action="" class="search-form form-inline mt-xs">
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

      <div class="row">
        <?php if ($sentence->task_id == NULL && $filtered) { ?>
          <div class="col-xs-12 col-md-12">
            <div class="alert alert-danger" role="alert">
              <strong>No sentences found with those filters!</strong>
              <a href="?p=1&id=1&task_id=<?= $task->id ?>" class="alert-link">See all</a>
            </div>
          </div>
        <?php } else {?>
        <div class="col-md-6">
          <div class="row">
            <?php if ($filtered) { ?>
              <div class="col-xs-12 col-md-12">
                <div class="alert alert-warning" role="alert">
                  <a href="?p=1&id=1&task_id=<?= $task->id ?>" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></a>
                  <strong>Sentences filtered!</strong>
                  <a href="?p=1&id=1&task_id=<?= $task->id ?>" class="alert-link">See all</a>
                </div>
              </div>
            <?php } ?>
              <div class="col-xs-12 col-md-12">
                  <div class="row">
                    <div class="col-xs-6 col-md-6 text-increase">
                      <?= $project->source_lang_object->langname ?>
                    </div>
                    <div class="col-xs-6 col-md-6 text-right">
                      <a href="https://translate.google.com/?op=translate&sl=<?= $project->source_lang_object->langcode?>&tl=<?=$project->target_lang_object->langcode?>&text=<?=$sentence->source_text ?>"  title="Translate source sentence with Google" target="_blank"><span class="glyphicon glyphicon-globe"></span></a>
                    </div>
                  </div>
              </div>
              <div class="col-xs-12 col-md-12">
                <div class="well"><?= $sentence->source_text ?></div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 col-md-12">
                <div class="row">
                  <div class="col-xs-6 col-md-6 text-increase">
                    <?= $project->target_lang_object->langname ?>
                  </div>
                  <div class="col-xs-6 col-md-6 text-right">
                    <a href="https://translate.google.com/?op=translate&sl=<?= $project->target_lang_object->langcode?>&tl=<?=$project->source_lang_object->langcode?>&text=<?=$sentence->target_text ?>"  title="Translate target sentence with Google" target="_blank"><span class="glyphicon glyphicon-globe"></span></a>
                  </div>
                </div>
              </div>
              <div class="col-xs-12 col-md-12">
                <div class="well"><?= $sentence->target_text ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <form id="evaluationform" action="/sentences/sentence_save.php" role="form" method="post" data-toggle="validator">
              <div class="row form-horizontal" style="margin-top: 0; padding: 0;">
                <input type="hidden" name="task_id" value="<?= $task->id ?>">
                <input type="hidden" name="sentence_id" value="<?= $sentence->id ?>">
                <input type="hidden" name="p_id" value="<?= $task_progress->current ?>">

                <?php if ($filtered) { ?>
                  <input type="hidden" name="term" value="<?= $search_term ?>" />
                  <input type="hidden" name="label" value="<?= $filter_label ?>" />
                <?php } ?>

                <div class="col-md-12">
                  <div class="row vertical-align mt-xs" style="margin-bottom: 1em;">
                    <div class="col-sm-4 col-xs-6">
                      <h4 style="margin-top: 0px;">Annotation</h4>
                    </div>
                    <div class="col-sm-8 col-xs-6 text-right">
                      <div data-toggle="modal" data-target="#evaluation-help" class="guidelines">
                        Validation guidelines <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
                      </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12" style="padding-bottom: 1em;">
                    <div class="row btn-group evaluation-btn-group" data-toggle="buttons">
                      <div class="col-md-12 col-xs-12">
                          <?php foreach (array_slice(sentence_task_dto::$labels, 0, count(sentence_task_dto::$labels) - 2) as $label) { ?>
                            <a href="#tab_<?= $label['value'] ?>" class="evaluation_tab_link <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                              <label class="btn btn-primary wrong-eval <?= $sentence->evaluation == $label['value'] ? "active" : "" ?>  <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                                <input type="radio" name="evaluation" autocomplete="off" required <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" value="<?= $label['value'] ?>"> <?= $label['label'] ?>
                              </label>
                            </a>
                          <?php } ?>
                      </div>
                      <div class="col-md-12 col-xs-12" style="margin-top: 1em;">
                          <?php foreach (array_slice(sentence_task_dto::$labels, -2, 1) as $label) { ?>
                            <a href="#tab_<?= $label['value'] ?>" data-toggle="tab" class="evaluation_tab_link <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                              <label class="btn btn-success valid-eval <?= $sentence->evaluation == $label['value'] ? "active" : "" ?>  <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                                <input type="radio" name="evaluation" autocomplete="off" required <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" value="<?= $label['value'] ?>"> <?= $label['label'] ?>
                              </label>
                            </a>
                          <?php } ?>
                          <?php foreach (array_slice(sentence_task_dto::$labels, -1, 1) as $label) { ?>
                            <a href="#tab_<?= $label['value'] ?>" data-toggle="tab" class="evaluation_tab_link <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                              <label class="btn btn-warning pending-eval <?= $sentence->evaluation == $label['value'] ? "active" : "" ?>  <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                                <input type="radio" name="evaluation" autocomplete="off" required <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" value="<?= $label['value'] ?>"> <?= $label['label'] ?>
                              </label>
                            </a>
                          <?php } ?>
                      </div>
                    </div>
                  </div>
                  <div class="col-xs-12 col-md-12">
                    <hr />
                    <!--
                      This is the fixed comments section. The name of the parameters
                      which contain each comment should start with the code of the label
                      followed by an underscore (for example, MT_ for Machine Translation).
                    -->
                    <?php $comment_dao = new comment_dao(); ?>
                    <div class="tab-content evaluation-tab-content">
                      <?php foreach($tabs as $tab) { ?>
                      <div role="tabpanel" class="tab-pane" id="tab_<?= $tab["label"] ?>">
                        <?php foreach($tab["comments"] as $comment) {?>
                        <div class="row">
                          <div class="col-xs-12 col-md-4">
                            <label for="<?= $comment["name"] ?>"><?= $comment["text"] ?></label>
                          </div>
                          <div class="col-xs-12 col-md-8 text-right-md">
                            <div class="btn-group btn-group-justified" data-toggle="buttons">
                              <?php foreach($comment["options"] as $option) { ?>
                              <?php 
                                $option_data = $comment_dao->getCommentById($sentence->id, $comment["name"]); 
                                $option["text"] = str_replace("[SOURCE]", $project->source_lang_object->langname, $option["text"]);
                                $option["text"] = str_replace("[TARGET]", $project->target_lang_object->langname, $option["text"]);
                                ?>
                              <label class="btn btn-default  <?= (($option_data != false) ? (($option_data->value == $option["value"]) ? "active" : "") : "") ?> <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                                <input type="radio" <?php if ($task->status == "DONE") { echo "disabled"; } ?> <?= (isset($option_data->value) ? (($option_data->value == $option["value"]) ? "checked" : "") : "") ?> name="<?= $comment["name"] ?>" autocomplete="off" type="radio" value="<?= $option["value"] ?>"> <?= $option["text"] ?>
                              </label>
                              <?php } ?>
                            </div>
                          </div>
                        </div>
                        <?php } ?>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
      </div>

      <div class="col-xs-12 col-md-12" style="margin-top: 4em;">
        <div class="row">
          <div class="col-md-2 col-md-push-6 col-xs-2 pt-xs">
            <a class="btn btn-link-lg <?= ($task_progress->current-1 == 0) ? "disabled" : "" ?>" style="padding-left: 0em;" href="/sentences/evaluate.php?task_id=<?= $task->id ?>&p=1&id=<?= $task_progress->current-1 ?><?php if (isset($search_term)) { echo "&term=".$search_term; } ?><?php if (isset($filter_label)) { echo "&label=".$filter_label; } ?>#top" tabindex="5" title="Go to the previous sentence"><span class="glyphicon glyphicon-arrow-left"></span> Previous</a>
          </div>

          <div class="col-md-4 col-md-push-6 col-xs-10 text-right">
            <a href="/sentences/evaluate.php?task_id=<?= $task->id ?>#top" class="btn btn-link" title="Go to the first pending sentence">First pending</a>

            <?php if ($task->status == "DONE") { ?>
              <button id="evalutionsavebutton" data-next="/sentences/evaluate.php?task_id=<?= $task->id ?>&p=1&id=<?= $task_progress->current+1 ?><?php if (isset($search_term)) { echo "&term=".$search_term; } ?><?php if (isset($filter_label)) { echo "&label=".$filter_label; } ?>#top" class="btn btn-primary btn-lg" style="padding-left: 1em; padding-right: 1em;" tabindex="5" title="Go to the next sentence">
                Next <span class="glyphicon glyphicon-arrow-right"></span>
              </button>
            <?php } else { ?>
              <button id="evalutionsavebutton" class="btn btn-primary btn-lg" style="padding-left: 1em; padding-right: 1em;" tabindex="5" title="Save this evaluation and go to the next sentence">Next <span class="glyphicon glyphicon-arrow-right"></span></button>
            <?php } ?>
          </div>

          <div class="col-md-6 col-md-pull-6 col-xs-12 mt-xs">
            <div class="row">
              <div class="col-md-5 col-md-offset-4 col-xs-12">
                <form id="gotoform" method="get" action="/sentences/evaluate.php">
                  <input type="hidden" name="p" value="1">
                  <input type="hidden" name="task_id" value="<?= $task->id ?>">
                  
                  <div class="input-group">
                      <input type="number" name="id" class="form-control" value="<?= $task_progress->current ?>" min="1" max="<?= $task_progress->total ?>" />
                      <div class="input-group-addon">of <?= $task_progress->total ?></div>
                      <div class="input-group-btn">
                      <button type="submit" class="btn btn-default">Go</button>
                      </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <?php } ?>

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