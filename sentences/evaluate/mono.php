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

$filtered = isset($search_term);

if (isset($task_id)) {
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);
  if ($task->id == $task_id && $task->assigned_user == $USER->id && $task->mode == "MONO") {
    
    $sentence_task_dao = new sentence_task_dao();
    $paginate = filter_input(INPUT_GET, "p");
    if (isset($paginate) && $paginate == "1") {
      if ($filtered) {
        $sentence = $sentence_task_dao->gotoSentenceByTaskAndFilters($sentence_id, $task_id, $search_term);
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
      $task_progress = $sentence_task_dao->getCurrentProgressByIdAndTaskAndFilters($sentence->id, $task->id, $search_term);
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

    <link rel="stylesheet" href="/css/slider.css" />
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
                <form action="" class="form-inline col-sm-12 col-md-8 col-md-offset-4 search-form mt-1 mt-sm-0 pl-md-0" style="justify-content: flex-end;">
                  <input type="hidden" name="task_id" value="<?= $task->id ?>" />
                  <input type="hidden" name="p" value="1" />
                  <input type="hidden" name="id" value="1" />

                  <div class="form-group pr-sm-4">
                    <input class="form-control" id="search-term" name="term" value="<?php if (isset($search_term)) { echo $search_term; } ?>" placeholder="Search through sentences" aria-label="Search through sentences">
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
        <div class=" col-xs-12">
          <form id="evaluationform" action="/sentences/sentence_save.php" role="form" method="post" data-toggle="validator">
              <div class="row ">
                  <input type="hidden" name="task_id" value="<?= $task->id ?>">
                  <input type="hidden" name="sentence_id" value="<?= $sentence->id ?>">
                  <input type="hidden" name="p_id" value="<?= $task_progress->current ?>">
                  <input type="hidden" name="time" value="<?php $date = new DateTime(); echo $date->getTimestamp(); ?>" />

                  <?php if ($filtered) { ?>
                  <input type="hidden" name="term" value="<?= $search_term ?>" />
                  <input type="hidden" name="label" value="<?= $filter_label ?>" />
                  <?php } ?>

                  <div class="col-sm-6 "style="width:60%;">
                      <div class="text-increase mb-2 ">How would you rate this sentence in <?= $task->target_lang_object->langname ?> ?</div>
                      <div class="well"><?=  $sentence->source_text ?></div>
                  </div>
                  

                  <div class="col-sm-6 my-3 d-flex" style="display:flex;flex-direction:column;width: 40%;">
                    <!-- <div class="row">
                      <div class="col-md-12 col-xs-12 slider-component mt-3"> 
                        <div class="custom-range slider">
                            <input type=range id="evaluation" name="evaluation" min="0" max="100" value="<?= ($sentence->evaluation == "P") ? 50 : $sentence->evaluation ?>" />
                            <div class="custom-range-control">
                              <div class="pre"></div>
                              <div class="post"></div>
                              <div class="handle"></div>
                            </div>
                          </div>
                      </div> --><!-- .slider-component -->
                   

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
                   
                      <div class=" col-xs-12">
                        <div class="btn-group btn-group-annotation w-100 mb-1" role="group" >
                          <span class="btn btn-danger disabled outline col-md-2 col-xs-2 px-0">0</span>
                          <label class="btn btn-annotation outline btn-danger col-md-10 col-xs-10 mb-2">
                            <input type="radio" name="evaluation" autocomplete="off" style="display: none;" type="radio" value="WLN"> Wrong language or no language
                          </label>
                        </div>
                      </div>
                    
               
                      <div class=" col-xs-12">
                        <div class="btn-group btn-group-annotation w-100 mb-1" role="group" >
                          <span class="btn btn-warning disabled outline col-md-2 col-xs-2 px-0">1</span>
                          <label class="btn btn-annotation outline btn-warning col-md-10 col-xs-10 mb-2">
                            <input type="radio" name="evaluation" autocomplete="off" style="display: none;" type="radio" value="NRT"> Not running text 
                          </label>
                        </div>
                      </div>
                    
                      <div class=" col-xs-12">
                        <div class="btn-group btn-group-annotation w-100 mb-1" role="group" >
                          <span class="btn btn-info disabled outline col-md-2 col-xs-2 px-0">2</span>
                          <label class="btn btn-annotation outline btn-info col-md-10 col-xs-10 mb-2">
                            <input type="radio" name="evaluation" autocomplete="off" style="display: none;" type="radio" value="PRT"> Partially running text 
                          </label>
                        </div>
                      </div>
                      <div class="col-xs-12">
                        <div class="btn-group btn-group-annotation w-100 mb-1" role="group" >
                          <span class="btn btn-primary disabled outline col-md-2 col-xs-2 px-0">3</span>
                          <label class="btn btn-annotation outline btn-primary col-md-10 col-xs-10 mb-2">
                            <input type="radio" name="evaluation" autocomplete="off" style="display: none;" type="radio" value="RTE"> Running text 
                          </label>
                        </div>
                      </div>
                      <div class=" col-xs-12">
                        <div class="btn-group btn-group-annotation w-100 mb-1" role="group" >
                          <span class="btn btn-success disabled outline col-md-2 col-xs-2 px-0">4</span>
                          <label class="btn btn-annotation outline btn-success col-md-10 col-xs-10 mb-2">
                            <input type="radio" name="evaluation" autocomplete="off" style="display: none;" type="radio" value="PT"> Publishable running text 
                          </label>
                        </div>
                      </div>
                    
              </div>
          </form>
        </div>
        <div class="col-md-12 col-xs-12 mt-sm-5 mt-3">
            <div class="row same-height-sm">
                <div class="col-md-6 same-height-column">
                </div>
            </div>
        </div>
      </div>

      <div class="row">
        <hr />
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
      <?php } ?>
    </div>

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
              </ul>

              <div class="modal-body-content">
                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane active" id="quickreference">
                    <div class="row">
                      <div class="col-md-12 col-xs-12">
                        <p class="h4"><strong class="label label-info pt-3">Keep in mind</strong></p>
                        <ul class="arrow-list">
                          <li>
                            Only one type of error should be attributed to each sentence.
                          </li>
                          <li>
                            Sub-specifications for some errors are optional: mark them only if indicated by your PM.
                          </li>
                        
                        </ul>
                      </div>
                      <div class="col-md-12 col-xs-12 quickreference">
                        <p class="h4"><strong class="label label-default pt-3">Error hierarchy</strong></p>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                            <span class="number-container number-container-small p-1">0</span>
                            <strong> Wrong Language / Not Language</strong>
                          </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                          The text is not in the correct language (e.g. Icelandic instead of English), or the text is not language: no words, just HTML tags, links, or emojis.

                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                            <span class="number-container number-container-small pt-1">1</span>
                            <strong>Not running text</strong>
                          </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                          Text makes no sense, it’s just a concatenation or bunch of words together, less than 50% is running text. Note that short sentences can still be running text, but might often not be.

                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                            <span class="number-container number-container-small pt-1">2</span>
                            <strong>Partially running text</strong>
                            </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                          More than 50% is running text, but some parts are not. For example, the text is cut-off or has additional elements in brackets. A substantial part of the text should be cut-off for this to apply. Titles, headers and bullet points probably fit better in the next category.

                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                              <span class="number-container number-container-small pt-1">3</span>
                              <strong>Running text, but (slightly) non-standard</strong>
                            </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                          More than 90% is running text, but the text contains small mistakes, such as grammatical errors, typo’s, and missing punctuation. This category includes titles, headers and bullet points.

                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-xs-12">
                            <span class="number-container number-container-small pt-1">4</span>
                            <strong>Publishable text</strong>
                          </div>
                          <div class="col-md-8 col-xs-11 mt-1 mt-sm-0">
                          100% running text which is of publishable quality and contains no (formatting) mistakes. You could read this in a blog post, news article, recipe, magazine, etc. Note that the content itself does not have to be formal for this to apply.

                          </div>
                          </div>

                          <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                      <strong>Wrong Language / Not Language</strong>
                      </div>
                      <div class="col-xs-12 col-md-12 row pt-3 pb-3">
                        <ul><li>STRAND 240 242 {ECO:0000244|PDB:1FMK}.</li>
                        <li>box-shadow: 1px 1px 3px 2px #121e03;</li>
                        <li>このテキストは完全に間違った言語で書かれています</li></ul>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                       <strong>Not running text</strong>
                      </div>
                      <div class="col-xs-12 col-md-12 row">
                      <div class="col-xs-12 col-md-12 row pt-3 pb-3">
                        <ul><li>Archives Select Month December 2022 (2) November 2022 (11) October 2022 (14) September 2022 [...]</li>
                        <li>#fish #koi #carpe #carpekoi #poisson #japon</li>
                        <li>September 23, 2018</li>
                        <li>Sheraton Signature Sleep Experience® | Sheraton Tirana Hotel | Official Website</li>
                      </ul>
                      </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                        <strong>Partially running text</strong>
                      </div>
                      <div class="col-xs-12 col-md-12 row pt-3 pb-3">
                        <ul>
                        <li>bomb blew up in her face on Christmas Eve. Police refused to speculate about the</li>
                        <li>in approximately 13,000 women every year in the United States, and kills almost 5,000 American</li>
                        <li>Complete Your Bachelors Degree or Associate Degree | Charter …</li>
                        <li>The premium is the amount you'll pay for the huge benefits protected</li>
                      </ul>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                      <strong>Running text, but (slightly) non-standard</strong>
                      </div>
                      <div class="col-xs-12 col-md-12 row pt-3 pb-3">
                        <ul><li>What The Riga Elections Say About Latvian Politics – Analysis – Eurasia Review</li>
                        <li>Demonstrated critical thinking and decision-making competencies</li>
                        <li>Friday.4th: At Noon, the Detachment of Marines fired 3 vollies in honour of the Day.</li>
                        <li>I get a long line of numbers (where you can extract the windspeed from), but the outcome is strange. This is shown in my log file:</li>
                        <li>HOW DO I WRITE A BUSINESS REPORT?</li>
                        <li>(c) It is because of God, then, that we have language and words</li>
                      </ul>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-12 col-md-12 h5">
                      <strong>Publishable text</strong>
                      </div>
                      <div class="col-xs-12 col-md-12 row pt-3 pb-3">
                        <ul><li>The Caucasian rugs are made in the regions located in the mountain chain of the Caucasus, an area situated between the Black Sea and the Caspian Sea. The area is spanned across Georgia, Russian, Armenia and Azerbaijan.</li>
                        <li>You don't mean that, said Bones hoarsely.</li>
                        <li>Friday.4th: At Noon, the Detachment of Marines fired 3 vollies in honour of the Day.</li>
                        <li>Sounds delicious. My daughter makes it with a puréed jalapeño swirl that looks and tastes amazing.</li>
                        <li>Does your business need an interactive website or app?</li>
                      </ul>
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
    <script type="text/javascript" src="/js/slider.js"></script>
    <script type="text/javascript" src="/js/slider_keyboard.js"></script>
  </body>
</html>