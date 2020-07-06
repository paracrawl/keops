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

$comments = [
  "L" => [
    "single_option" => false,
    "options" => [
      ["value" => "true", "text" => "[SOURCE]", "name" => "L_sentence_source"],
      ["value" => "true", "text" => "[TARGET]", "name" => "L_sentence_target"]
    ]
  ],
  "MT" => [
    "single_option" => false,
    "options" => [
      ["value" => "true", "text" => "[SOURCE]", "name" => "MT_source"],
      ["value" => "true", "text" => "[TARGET]", "name" => "MT_target"]
    ]
  ],
  "F" =>[
    "single_option" => true,
    "options" => [
      ["value" => "keep", "text" => "Keep", "name" => "F_keep"],
      ["value" => "discard", "text" => "Discard", "name" => "F_keep"]
    ]
  ]
];

if (isset($task_id)) {
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);
  if ($task->id == $task_id && $task->assigned_user == $USER->id && $task->mode == "VAL") {
    
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
<html lang="en">
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

      <div class="row" style="border-bottom: solid 1px #eee;">
        <div class="col-md-12 row mx-0 mt-0 mb-4">
          <div class="row">
            <div class="col-md-4 col-sm-12 col-xs-12">
              <span class="h2">Task #<?php echo $task->id ?>
              <?php if (!$filtered) { ?>
                <small><?= $task_progress->completed ?> out of <?= $task_progress->total ?> (<?= round(($task_progress->completed / $task_progress->total) * 100, 2) ?>%) done</small></span>
              <?php } ?>
            </div>

            <div class="col-md-8 col-sm-12 col-xs-12">
              <input type="hidden" name="seall" value="?p=1&id=1&task_id=<?= $task->id ?>" />
              <div class="row">
                <form action="" class="form-inline col-sm-12 col-md-8 col-md-offset-4 search-form mt-1 mt-sm-0 pl-md-0">
                  <input type="hidden" name="task_id" value="<?= $task->id ?>" />
                  <input type="hidden" name="p" value="1" />
                  <input type="hidden" name="id" value="1" />

                  <div class="form-group w-100 pr-sm-4">
                    <input class="form-control" style="width: 100%!important;" id="search-term" name="term" value="<?php if (isset($search_term)) { echo $search_term; } ?>" placeholder="Search through sentences" aria-label="Search through sentences">
                  </div>

                  <?php
                      $labelcodes = array("P", "L", "A", "T", "MT", "F", "V");
                      $labels = array("Pending", "Language", "Alignment", "Tokenization", "Machine Translation", "Free translation", "Valid");
                  ?>
                  <div class="form-group w-100 pr-sm-4">
                    <select class="form-control" style="width: 100%!important;" name="label" aria-label="Select label">
                      <option value="ALL">Everything</option>
                      <?php
                          for ($i = 0; $i < count($labelcodes); $i++) { $labelcode = $labelcodes[$i]; $label = $labels[$i]; ?>
                              <option value="<?php echo $labelcode; ?>" <?php if (isset($filter_label) && $labelcode == $filter_label) { echo "selected"; } ?>><?php echo $label; ?></option>
                          <?php }
                      ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <div class="btn-group float-right" role="group" style="display:flex;">
                      <button type=submit class="btn btn-primary" id="search-term-button" title="Search" aria-label="Search"><i class="glyphicon glyphicon-search"></i></button>
                      <a class="btn <?= ($filtered) ? "btn-danger" : "btn-primary disabled" ?>" href="?p=1&id=1&task_id=<?= $task->id ?>" title="Clear search" aria-label="Clear search"><i class="glyphicon glyphicon-remove"></i></a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4" id="evaluation-sentences">
        <?php if ($sentence->task_id == NULL && $filtered) { ?>
          <div class="col-xs-12 col-md-12">
            <div class="alert alert-danger" role="alert">
              <strong>No sentences found with those filters!</strong>
              <a href="?p=1&id=1&task_id=<?= $task->id ?>" class="alert-link">Remove filters</a>
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
                  <a href="?p=1&id=1&task_id=<?= $task->id ?>" class="alert-link">Remove filters</a>
                </div>
              </div>
            <?php } ?>
              <div class="col-xs-12 col-md-12">
                  <div class="row mb-3">
                    <div class="col-xs-6 col-md-6 text-increase">
                      <?= $task->source_lang_object->langname ?>
                    </div>
                    <div class="col-xs-6 col-md-6 text-right">
                      <a href="https://translate.google.com/?op=translate&sl=<?= $task->source_lang_object->langcode?>&tl=<?=$task->target_lang_object->langcode?>&text=<?=$sentence->source_text ?>"  title="Translate source sentence with Google" target="_blank"><img alt="Translate source sentence with Google" src="/img/googletranslate.png" /></a>
                    </div>
                  </div>
              </div>
              <div class="col-xs-12 col-md-12">
                <div class="well"><?= $sentence->source_text ?></div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-12 col-md-12">
                <div class="row mb-3">
                  <div class="col-xs-6 col-md-6 text-increase">
                    <?= $task->target_lang_object->langname ?>
                  </div>
                  <div class="col-xs-6 col-md-6 text-right">
                    <a href="https://translate.google.com/?op=translate&sl=<?= $task->target_lang_object->langcode?>&tl=<?=$task->source_lang_object->langcode?>&text=<?=$sentence->target_text[0]->source_text ?>"  title="Translate target sentence with Google" target="_blank"><img alt="Translate target sentence with Google" src="/img/googletranslate.png" /></a>
                  </div>
                </div>
              </div>
              <div class="col-xs-12 col-md-12">
                <div class="well"><?= $sentence->target_text[0]->source_text ?></div>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <form id="evaluationform" action="/sentences/sentence_save.php" role="form" method="post" data-toggle="validator">
              <input type=hidden name="evaluation" value="P" />

              <div class="row form-horizontal" style="margin-top: 0; padding: 0;">
                <input type="hidden" name="task_id" value="<?= $task->id ?>">
                <input type="hidden" name="sentence_id" value="<?= $sentence->id ?>">
                <input type="hidden" name="p_id" value="<?= $task_progress->current ?>">
                <input type="hidden" name="time" value="<?php $date = new DateTime(); echo $date->getTimestamp(); ?>" />

                <?php if ($filtered) { ?>
                  <input type="hidden" name="term" value="<?= $search_term ?>" />
                  <input type="hidden" name="label" value="<?= $filter_label ?>" />
                <?php } ?>

                <div class="col-md-11 col-md-offset-1">
                  <div class="row vertical-align mt-1 mt-sm-0 mb-4" style="margin-bottom: 1em;">
                    <div class="col-sm-4 col-xs-6">
                      <h4 class="my-0">Annotation</h4>
                    </div>
                    <div class="col-sm-8 col-xs-6 text-right">
                      <a href="#" data-toggle="modal" data-target="#evaluation-help">
                        Validation guidelines <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
                      </a>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12 col-xs-12 btn-group evaluation-btn-group">
                    <?php $comment_dao = new comment_dao(); $count = 0; ?>
                    <?php foreach (array_slice(sentence_task_dto::$labels, 1, count(sentence_task_dto::$labels) - 2) as $label) { ?>
                      <?php $count++; ?>
                      <div class="row">
                      <div class="col-md-5 col-xs-12">
                        <div class="btn-group btn-group-annotation w-100 mb-1" role="group" style="display:flex;">
                          <span class="btn btn-primary disabled outline col-md-2 col-xs-2 px-0"><?= $count ?></span>
                          <label class="btn btn-annotation outline btn-primary col-md-10 col-xs-10 <?= $sentence->evaluation == $label['value'] ? "active" : "" ?>  <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                            <input type="radio" name="evaluation" autocomplete="off" <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" value="<?= $label['value'] ?>"> <?= $label['label'] ?>
                          </label>
                        </div>
                      </div>
                      <div class="col-md-7 col-xs-12 question-column d-none">
                        <?php
                            if (array_key_exists($label['value'], $comments)) {
                            $comment = $comments[$label['value']];
                        ?>
                        <div class="btn-group btn-group-justified w-100 my-3 my-sm-0" data-toggle="buttons" data-single="<?= $comment["single_option"] ?>">
                          <?php foreach ($comment["options"] as $option) { ?>
                          <?php $comment_dto = $comment_dao->getCommentById($sentence->id, $option["name"]); ?>
                          <label class="btn btn-default <?= ($comment_dto) ? ($comment_dto->value == $option["value"] ? "active" : "") : "" ?>">
                            <input type="checkbox" name="<?= $option["name"] ?>" value="<?= $option["value"] ?>" <?= ($comment_dto) ? ($comment_dto->value == $option["value"] ? "checked" : "") : "" ?> /> <?= str_replace("[SOURCE]", $task->source_lang_object->langname, str_replace("[TARGET]",  $task->target_lang_object->langname, $option["text"])); ?>
                          </label>
                          <?php } ?>
                        </div>
                        <?php } else {  ?> &nbsp; <?php } ?>
                      </div>
                    </div>
                    <?php } ?>

                    <div class="row">
                      <?php foreach (array_slice(sentence_task_dto::$labels, -1, 1) as $label) { ?>
                      <div class="col-md-5 col-xs-12">
                      <div class="btn-group w-100 mb-1" role="group" style="display:flex;">
                          <span class="btn btn-success disabled outline col-md-2 col-xs-2 px-0"><?= $count + 1 ?></span>
                          <label class="btn btn-annotation outline btn-success px-0 col-md-10 col-xs-10 <?= $sentence->evaluation == $label['value'] ? "active" : "" ?>  <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                            <input type="radio" name="evaluation" autocomplete="off" <?= $sentence->evaluation == $label['value'] ? "checked" : "" ?> type="radio" value="<?= $label['value'] ?>"> <?= $label['label'] ?>
                          </label>
                        </div>
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-bottom: 1em;">
                  <hr />

                  <!--
                    This is the fixed comments section. The name of the parameters
                    which contain each comment should start with the code of the label
                    followed by an underscore (for example, MT_ for Machine Translation).
                  
                  -->

                  <?php
                    $content_error = $comment_dao->getCommentById($sentence->id, "content_error");
                    $personal_data = $comment_dao->getCommentById($sentence->id, "personal_data");
                  ?>

                  <div class="col-xs-12 col-md-12">
                    <label for="personal_data" class="checkbox-custom <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                      <input type="checkbox" id="personal_data" <?php if ($task->status == "DONE") { echo "disabled"; } ?> <?= (($personal_data) ? (($personal_data->value == "1") ? "checked" : "") : "") ?> autocomplete="off" type="radio" name="personal_data" />
                      <span class="checkbox-control"></span>
                      Contains personal data
                    </label>
                  </div>
                  <div class="col-xs-12 col-md-12">
                    <label for="content_error" class="checkbox-custom <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                      <input type="checkbox" id="content_error" <?php if ($task->status == "DONE") { echo "disabled"; } ?> <?= (($content_error) ? (($content_error->value == "1") ? "checked" : "") : "") ?> autocomplete="off" type="radio" name="content_error" />
                      <span class="checkbox-control"></span>
                      Contains inappropriate language
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="col-xs-12 col-md-12">
        <div class="row">
          <div class="col-md-2 col-md-push-6 col-xs-2 pt-xs-1">
            <a class="btn btn-lg btn-previous <?= ($task_progress->current-1 == 0) ? "disabled" : "" ?>" style="padding-left: 0em;" href="/sentences/evaluate.php?task_id=<?= $task->id ?>&p=1&id=<?= $task_progress->current-1 ?><?php if (isset($search_term)) { echo "&term=".$search_term; } ?><?php if (isset($filter_label)) { echo "&label=".$filter_label; } ?>" title="Go to the previous sentence"><span class="glyphicon glyphicon-arrow-left"></span> Previous</a>
          </div>

          <div class="col-md-4 col-md-push-6 col-xs-10 text-right">
            <a href="/sentences/evaluate.php?task_id=<?= $task->id ?>" class="btn btn-link" title="Go to the first pending sentence">First pending</a>

            <?php if ($task->status == "DONE") { ?>
              <button id="evalutionsavebutton" data-next="/sentences/evaluate.php?task_id=<?= $task->id ?>&p=1&id=<?= $task_progress->current+1 ?><?php if (isset($search_term)) { echo "&term=".$search_term; } ?><?php if (isset($filter_label)) { echo "&label=".$filter_label; } ?>" class="btn btn-primary btn-lg" style="padding-left: 1em; padding-right: 1em;" title="Go to the next sentence">
                Next <span class="glyphicon glyphicon-arrow-right"></span>
              </button>
            <?php } else { ?>
              <button id="evalutionsavebutton" class="btn btn-primary btn-lg" style="padding-left: 1em; padding-right: 1em;" title="Save this evaluation and go to the next sentence">Next <span class="glyphicon glyphicon-arrow-right"></span></button>
            <?php } ?>
          </div>

          <div class="col-md-6 col-md-pull-6 col-xs-12 mt-4 mt-sm-0">
            <div class="row">
              <div class="col-md-12 col-xs-12 mt-1" style="display: flex; justify-content: center;">
                <form id="gotoform" method="get" action="/sentences/evaluate.php" class="col-md-5 col-xs-12">
                  <input type="hidden" name="p" value="1">
                  <input type="hidden" name="task_id" value="<?= $task->id ?>">
                  
                  <div class="input-group">
                      <input type="number" name="id" class="form-control current-page-control" aria-label="Current page" value="<?= $task_progress->current ?>" min="1" max="<?= $task_progress->total ?>" />
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
                            Only one type of error should be attributed to each pair of sentences.
                          </li>
                          <li>
                            When more than one error is present, please follow the hierarchy to indicate just one.
                          </li>
                          <li>
                            Sub-specifications for some errors are optional: mark them only if indicated by your PM.
                          </li>
                          <li>
                            The given translation must receive the benefit of the doubt.
                          </li>
                        </ul>
                      </div>
                      <div class="col-md-12 col-xs-12 quickreference">
                        <p class="h4"><strong class="label label-default">Error hierarchy</strong></p>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                            <span class="number-container number-container-small">1</span>
                            <strong>Wrong Language Identification</strong>
                          </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                            Automatic tools failed in identifying the right language or in providing a consistent encoding. <br />
                            Sub-specification: mark if source, target or both languages are wrongly identified or encoded.
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                            <span class="number-container number-container-small">2</span>
                            <strong>Incorrect Alignment</strong>
                          </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                            The segments have different content due to wrong alignment.
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                            <span class="number-container number-container-small">3</span>
                            <strong>Wrong Tokenization</strong>
                            </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                            Text in sentences has not been properly rendered or segmented: no separator between words, extra spaces near punctuation, more than two sentences, etc. <br />
                            Sub-specification: bad tokenized text can be found in source, target or both.
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                              <span class="number-container number-container-small">4</span>
                              <strong>MT-translated Content</strong>
                            </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                            Content identified as a translation through a machine translation system and wrongly translated. <br />
                            Sub-specification: machine translated content is in source, target or both.
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                            <span class="number-container number-container-small">5</span>
                            <strong>Translation Errors</strong>
                          </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                            Translation contains lexical mistakes, syntactic errors or poor use of language.
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                            <span class="number-container number-container-small">6</span>
                            <strong>Free Translation</strong>
                          </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                            Non-literal translation, that is, the content is completely reformulated in one language. <br />
                            Sub-specification: the sentence pairs should be kept or discarded from a parallel corpus.
                          </div>
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
                        Wrong Language Identification
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          You have no items in your shopping cart.
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          Нямате артикули в количката си.
                        </p>
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          You have no items in your shopping cart.
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          El carrito est&amp;#225; vac&amp;iacuteo
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                        Incorrect Alignment
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          We have booked two rooms at your hotel on Dec 11 for four nights.
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          En diciembre, pueden reservar dos habitaciones dobles al precio de una (mínimo cuatro noches).
                        </p>
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          its configuration could give the impression that its owners were not
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          podría dar la impresión de que sus poseedores no
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                        Wrong Tokenization
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          EnvironmentPleasant view, bright, in gated residential community.
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          EntornoVista agradable , luminoso , en una urbanizaci ón cerrada.
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                        MT-translated content
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
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          Steve Jobs co-founded Apple.
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          Steve Trabajos cofundó Manzana.
                        </p>
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          You can drink free beers here.
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          Puedes tomar cervezas libre aquí.
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                        Translation Errors
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                        <p class="col-xs-12 col-md-6">
                          <strong>English</strong> <br />
                          The Centre for Human Rights publishes its annual report 
                        </p>
                        <p class="col-xs-12 col-md-6">
                          <strong>Spanish</strong> <br />
                          El Centro de Derechos Umanos publica su banlace anual.
                        </p>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                        Free translation
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
                          Extra information is optional and you should only be taken into account if indicated by your PM. 
                        </p>
                        <p>
                          Options:
                          <ul>
                            <li>
                              <strong>Contains personal data:</strong> content includes proper names or other personal data that could be anonymised for data protection purposes.
                            </li>
                            <li>
                              <strong>Contains inappropriate language:</strong> content includes profane language. 
                            </li>
                          </ul>
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
    
    <script type="text/javascript" src="/js/timer.js"></script>
    <script type="text/javascript" src="/js/evaluation.js"></script>
    <script type="text/javascript" src="/js/val_evaluation.js"></script>
  </body>
</html>