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
    <div id="evaluation-container" class="container evaluation" data-done="<?= ($task->status == "DONE") ?>">
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
      </ul>

      <div class="row">
        <div class="col-md-12 page-header row mx-0 mt-0">
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
                              <label class="btn btn-primary <?= $sentence->evaluation == $label['value'] ? "active" : "" ?>  <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                                <input type="radio" name="evaluation" autocomplete="off" required <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" value="<?= $label['value'] ?>"> <?= underline($label['label'], $label['value']) ?>
                              </label>
                            </a>
                          <?php } ?>
                      </div>
                      <div class="col-md-12 col-xs-12" style="margin-top: 1em;">
                          <?php foreach (array_slice(sentence_task_dto::$labels, -2, 1) as $label) { ?>
                            <a href="#tab_<?= $label['value'] ?>" data-toggle="tab" class="evaluation_tab_link <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                              <label class="btn btn-success <?= $sentence->evaluation == $label['value'] ? "active" : "" ?>  <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                                <input type="radio" name="evaluation" autocomplete="off" required <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" value="<?= $label['value'] ?>"> <?= underline($label['label'], $label['value']) ?>
                              </label>
                            </a>
                          <?php } ?>
                          <?php foreach (array_slice(sentence_task_dto::$labels, -1, 1) as $label) { ?>
                            <a href="#tab_<?= $label['value'] ?>" data-toggle="tab" class="evaluation_tab_link <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                              <label class="btn btn-warning <?= $sentence->evaluation == $label['value'] ? "active" : "" ?>  <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                                <input type="radio" name="evaluation" autocomplete="off" required <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" value="<?= $label['value'] ?>"> <?= underline($label['label'], $label['value']) ?>
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
                                $option["text"] = str_replace("[SOURCE]", $task->source_lang_object->langname, $option["text"]);
                                $option["text"] = str_replace("[TARGET]", $task->target_lang_object->langname, $option["text"]);
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

      <div class="col-xs-12 col-md-12">
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
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#quickreference" role="tab" data-toggle="tab">Quick reference</a></li>
                <li role="presentation"><a href="#examples" aria-controls="profile" role="tab" data-toggle="tab">Examples</a></li>
                <li role="presentation"><a href="#moreabout" aria-controls="messages" role="tab" data-toggle="tab">More about labels</a></li>
              </ul>

              <div class="modal-body-content">
                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="quickreference">
                    <div class="row">
                      <div class="col-md-12 col-xs-12">
                        <p class="h4"><strong class="label label-info">Keep in mind</strong></p>
                        <ul class="arrow-list">
                          <li>
                            Only one type of error should be attributed to each pair of sentences
                          </li>
                          <li>
                            The given translation must receive the benefit of the doubt
                          </li>
                        </ul>
                      </div>
                      <div class="col-md-12 col-xs-12 quickreference">
                        <p class="h4"><strong class="label label-default">Error hierarchy</strong></p>
                        <div class="row">
                          <div class="col-md-4 col-xs-4">
                            <span class="number-container number-container-small">1</span>
                            <strong>Wrong Language Identification</strong>
                          </div>
                          <div class="col-md-8 col-xs-6">The crawler tools failed in identifying the right language</div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-4">
                            <span class="number-container number-container-small">2</span>
                            <strong>Incorrect Alignment</strong>
                          </div>
                          <div class="col-md-8 col-xs-6">There are segments having different content due to wrong alignment</div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-4">
                            <span class="number-container number-container-small">3</span>
                            <strong>Wrong Tokenization</strong>
                            </div>
                          <div class="col-md-8 col-xs-6">The text has not been tokenized properly by the crawler tools (no separator between words)</div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-4">
                              <span class="number-container number-container-small">4</span>
                              <strong>Mt-translated Content</strong>
                            </div>
                          <div class="col-md-8 col-xs-6">Content identified as a translation through a Machine Translation system</div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-4">
                            <span class="number-container number-container-small">5</span>
                            <strong>Translation Errors</strong>
                          </div>
                          <div class="col-md-8 col-xs-6">Lexical mistakes, syntactic errors or poor use of language</div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-4">
                            <span class="number-container number-container-small">6</span>
                            <strong>Free Translation</strong>
                          </div>
                          <div class="col-md-8 col-xs-6">Non-literal translation, that is, the content is completely reformulated in one language</div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="examples">
                    <p>
                      These examples use English as the source language and Spanish as the target language.
                    </p>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                        <span class="label label-default">L</span> Wrong Language Identification
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          Even though his parents stood against God and his house was cursed, God still saved this child regardless of what his parents had done because the son was good.
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          Очевидно, этот текст не на испанском. Он написан на на русском я языке, язык России, очень интересная страна.
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                      <span class="label label-default">A</span> Incorrect Alignment
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          Thursday, 01 November 2012 03:55
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          Miércoles, 14 Noviembre 2012 06:16
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                      <span class="label label-default">T</span> Wrong Tokenization
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          The warranty is one year.It valid time start from the time you receive it.
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          La garantía es de un año. El tiempo válido comienza desde el momento en que lo recibes.
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                        <span class="label label-default">M</span> MT-translated content
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          There must be another way.
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          Hay que haber otro camino.
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                        <span class="label label-default">E</span> Translation Errors
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          Since I lived near Orbea… and nowadays I have a horrible relationship with his family.
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          Como vivía cerquita de Orbea… y hoy es el día en el que tengo una relación tremenda con su familia.
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                      <span class="label label-default">F</span> Free translation
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          I'll make you two promises: a very good steak, medium rare, and the truth, which is very rare
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          Te prometo dos cosas: un sabroso bistec a la plancha y decir siempre la verdad, lo que es raro en mí
                        </p>
                      </div>
                    </div>
                  </div>
                  <div role="tabpanel" class="tab-pane" id="moreabout">
                  <div class="row">
                      <div class="col-xs-12 col-md-12">
                        <p>
                          <strong>Wrong language identification</strong> means the crawler tools failed in identifying the right language.
                        </p>

                        <p>
                          <strong>Incorrect alignment</strong> refers to segments having a different content due to wrong alignment.
                        </p>

                        <p>
                          <strong>Wrong tokenization</strong> means the text has not been tokenized properly by the crawler tools (no separator between words).
                        </p>

                        <p>
                          <strong>MT translation</strong> refers to content identified as having been translated through a Machine Translation system. A few hints to detect if this is the case:
                          <ul>
                            <li>Grammar errors such as gender and number agreement</li>
                            <li>Words that are not to be translated (trademarks for instance Nike Air => if ‘Air’ is translated in the target language instead of being kept unmodified)</li>
                            <li>Inconsistencies (use of different words for referring to the same object/person)</li>
                            <li>Translation errors showing there is no human behind</li>
                          </ul>
                        </p>

                        <p>
                          <strong>Translation error</strong> refers to:
                          <ul>
                            <li>Lexical errors (omitted/added words or wrong choice of lexical item, due to misinterpretation or mistranslation)</li>
                            <li>Syntactic error (grammatical errors such as problems with verb tense, coreference and inflection, misinterpretation of the grammatical relationships among the words in the text)</li>
                            <li>Poor usage of language (awkward, unidiomatic usage of the target language and failure to use commonly recognized titles and terms). It could be due to MT translation</li>
                          </ul>
                        </p>

                        <p>
                          <strong>Free translation</strong> means a non-literal translation in the sense of having the content completely reformulated in one language (for editorial purposes for instance). This is a correct translation but in a different style or form. This includes figures of speech such as metaphors, anaphors, etc. We consider it as important to tag such cases for data that will be used for training MT systems.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
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