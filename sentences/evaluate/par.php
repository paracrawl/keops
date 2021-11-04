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

$filtered = isset($search_term);

if (isset($task_id)) {
    $task_dao = new task_dao();
    $task = $task_dao->getTaskById($task_id);
    if ($task->id == $task_id && $task->assigned_user == $USER->id && $task->mode == "PAR") {

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
                        <form action="" class="form-inline col-sm-12 col-md-8 col-md-offset-4 search-form mt-1 mt-sm-0 pl-md-0" style="display: flex; align-items: center; justify-content: end;">
                            <input type="hidden" name="task_id" value="<?= $task->id ?>" />
                            <input type="hidden" name="p" value="1" />
                            <input type="hidden" name="id" value="1" />

                            <div class="form-group pr-sm-4" style="margin-right: 0.5rem; width: 100%;">
                                <input class="form-control" style="margin-bottom: 0; width: 100%;" id="search-term" name="term" value="<?php if (isset($search_term)) { echo $search_term; } ?>" placeholder="Search through sentences" aria-label="Search through sentences">
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
        <div class="col-md-12 col-xs-12">
            <form id="evaluationform" action="/sentences/sentence_save.php" role="form" method="post" data-toggle="validator">
                <div class="row">
                    <input type="hidden" name="task_id" value="<?= $task->id ?>">
                    <input type="hidden" name="sentence_id" value="<?= $sentence->id ?>">
                    <input type="hidden" name="p_id" value="<?= $task_progress->current ?>">
                    <input type="hidden" name="time" value="<?php $date = new DateTime(); echo $date->getTimestamp(); ?>" />

                    <?php if ($filtered) { ?>
                        <input type="hidden" name="term" value="<?= $search_term ?>" />
                        <input type="hidden" name="label" value="<?= $filter_label ?>" />
                    <?php } ?>

                    <div class="col-md-6">
                        <div class="text-increase">Paraphrasing</div>
                        <p>
                            Please confirm if the two texts below are equivalent in meaning (paraphrases) according to the guidelines
                        </p>

                        <p style="margin-top: 2rem;">
                            <a href="#" data-toggle="modal" data-target="#evaluation-help">
                                <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> Paraphrasing guidelines
                            </a>
                        </p>
                    </div>

                    <div class="col-md-6 my-3">
                        <div class="row">
                            <div class="col-md-12 col-xs-12 slider-component mt-3"> <!-- .slider-component -->
                                <div class="custom-range slider <?= ($task->status == "DONE") ? "disabled" : "" ?>">
                                    <input type=range id="evaluation" name="evaluation" min="0" max="100" value="<?= ($sentence->evaluation == "P") ? 50 : $sentence->evaluation ?>" />
                                    <div class="custom-range-control">
                                        <div class="pre"></div>
                                        <div class="post"></div>
                                        <div class="handle"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-6">
                                <strong>Non-valid</strong>
                            </div>

                            <div class="col-md-6 col-xs-6 text-right">
                                <strong>Valid</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12 col-xs-12 mt-sm-5 mt-3">
            <div class="row same-height-sm">
                <div class="col-md-6 same-height-column">
                    <div class="text-increase mb-2">Source text</div>
                    <div class="well h-100"><?=  $sentence->source_text ?></div>
                </div>

                <div class="col-md-6 same-height-column">
                    <div class="text-increase mb-2">Candidate paraphrase</div>
                    <div class="well h-100"><?=  $sentence->target_text[0]->source_text ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <hr />

        <div class="col-md-6 col-md-push-6 col-xs-12 text-right" style="display: flex; align-items: baseline; justify-content: space-between;">
            <div style="display: flex; align-items: center;">
                <a class="btn btn-lg btn-previous <?= ($task_progress->current-1 == 0) ? "disabled" : "" ?>" style="padding-left: 0em;" href="/sentences/evaluate.php?task_id=<?= $task->id ?>&p=1&id=<?= $task_progress->current-1 ?><?php if (isset($search_term)) { echo "&term=".$search_term; } ?><?php if (isset($filter_label)) { echo "&label=".$filter_label; } ?>" title="Go to the previous sentence"><span class="glyphicon glyphicon-arrow-left"></span> Previous</a>
                <a href="/sentences/evaluate.php?task_id=<?= $task->id ?>" class="btn btn-link" title="Go to the first pending sentence" style="padding-left: 0px;">First pending</a>
            </div>

            <div style="display: flex; align-items: center; justify-content: end;">
                <a href="/sentences/evaluate.php?task_id=<?= $task->id ?>&p=1&id=<?= $task_progress->current + 1 ?>"
                   class="btn btn-link" id="skipBtn">Skip</a>

                <?php if ($task->status == "DONE") { ?>
                    <button id="evalutionsavebutton" data-next="/sentences/evaluate.php?task_id=<?= $task->id ?>&p=1&id=<?= $task_progress->current+1 ?><?php if (isset($search_term)) { echo "&term=".$search_term; } ?><?php if (isset($filter_label)) { echo "&label=".$filter_label; } ?>" class="btn btn-primary btn-lg" style="padding-left: 1em; padding-right: 1em;" title="Go to the next sentence">
                        Next <span class="glyphicon glyphicon-arrow-right"></span>
                    </button>
                <?php } else { ?>
                    <button id="evalutionsavebutton" class="btn btn-primary btn-lg" style="padding-left: 1em; padding-right: 1em;" title="Save this evaluation and go to the next sentence">Next <span class="glyphicon glyphicon-arrow-right"></span></button>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-6 col-md-pull-6 col-xs-12">
            <div class="row" style="display: flex; justify-content: center;">
                <div class="col-md-5 col-xs-12 mt-3 mt-md-0">
                    <form id="gotoform" method="get" action="/sentences/evaluate.php">
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
</div>

<div id="evaluation-help" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Guidelines</h4>
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
                                    <p class="h4"><strong class="label label-success">Valid paraphrases</strong></p>
                                    <ul class="arrow-list">
                                        <li>
                                            A sentence can be replaced by the other one
                                        </li>
                                        <li>
                                            The candidate sentence is a generalization of the source text
                                        </li>
                                        <li>
                                            The candidate sentence is a circumlocution of the source text
                                        </li>
                                        <li>
                                            The candidate sentence is the same as the source text, but with a different
                                            order of words
                                        </li>
                                        <li>
                                            The meanings of both sentences are similar in any context, even if they
                                            have different items (in different tense, person, number or gender)
                                        </li>
                                        <li>
                                            The meanings of both sentences are similar in any context, even if they
                                            contain any stop word which makes the meanings not equivalent
                                        </li>
                                        <li>
                                            The meanings of both sentences are similar in any context, even if they
                                            are expressed in different variants of the same language
                                        </li>
                                    </ul>

                                    <p class="h4"><strong class="label label-danger">Non-valid paraphrases</strong></p>
                                    <ul class="times-list">
                                        <li>
                                            The meanings of both sentences are not clearly related
                                        </li>
                                        <li>
                                            The candidate sentence contains just the same verbs or nouns but
                                            in different tense, gender or number
                                        </li>
                                        <li>
                                            The meanings of both sentences are similar, but the candidate is missing
                                            some important items
                                        </li>
                                        <li>
                                            The candidate sentence is specifying the meaning of the source text
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="examples">
                            <p>
                                These examples use Spanish as both the source and target language.
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

<?php
require_once(TEMPLATES_PATH . "/footer.php");
?>
<?php
require_once(TEMPLATES_PATH . "/resources.php");
?>

<script type="text/javascript" src="/js/evaluation.js"></script>
<script type="text/javascript" src="/js/par_evaluation.js"></script>
<script type="text/javascript" src="/js/slider.js"></script>
<script type="text/javascript" src="/js/slider_keyboard.js"></script>
</body>
</html>