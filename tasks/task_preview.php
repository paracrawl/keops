<?php
/*
 * Page for the preview of the first sentences of a corpus
 */
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/task_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/sentence_task_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/sentence_task_dto.php");

  $PAGETYPE = "user";
  require_once(RESOURCES_PATH . "/session.php");
  
  $task_id = filter_input(INPUT_GET, "id");
  if (!isset($task_id)) {
    header("Location: /admin/index.php#corpora");
    die();
  }

  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);

  $sentence_task_dao = new sentence_task_dao();
  $label = filter_input(INPUT_GET, "label");
  $preview = $sentence_task_dao->getSentencesByTaskIdAndLabel($task_id, $label);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Task preview </title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="breadcrumb">
        <li><a href="/index.php">Tasks</a></li>
        <li><a href="/tasks/recap.php?id=<?= $task->id ?>">Recap of Task #<?= $task->id ?></a></li>
        <li class="active">Task preview</a></li>
      </ul>
      <div class="title-container">
        <h3>Task #<?= $task->id ?> preview</h3>
      </div>
      
      <?php if (count($preview) == 0) { ?>
          <div class="text-increase mt-4">
            Sorry, we could not find any sentences
          </div>

          <p>
                <a class="btn btn-link pl-0" href="/tasks/recap.php?id=<?= $task->id ?>"><span class="glyphicon glyphicon-stats"></span> Go to recap</a>
          </p>
      <?php } else { ?>

      <?php if (isset($label)) { ?>
        <span class="label label-primary">
            Showing 
            <?php foreach(sentence_task_dto::$labels as $label_data) {
                if ($label_data['value'] == $label) {
                    echo $label_data['label'];
                } 
            }
            ?> only
        </span>
        <?php } ?>

        <?php
        if ($task->mode == "VAL" || $task->mode == "FLU" || $task->mode == "VAL_MAC" || $task->mode == "MONO") {
          foreach ($preview as $line){
            ?>
            <div class="row mt-3">
                <div class="col-sm-12 col-xs-12">
                    <a class="btn btn-link" href="/sentences/evaluate.php?task_id=<?= $task->id ?>&id=<?= $line->id ?>">
                        <span class="glyphicon glyphicon-play-circle"></span> Go
                    </a>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <div class="row mx-0">
                        <div class="col-sm-12 col-xs-12 mb-2">
                            <strong>
                            <?php echo $line->source_text;?></strong>
                        </div>
                        <?php foreach ($line->target_text as $target_text) { ?>
                        <div class="col-sm-12 col-xs-12">
                            <?= $target_text->source_text ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
          <hr>
          <?php
          }
        } else if ($task->mode == "ADE") {
        ?>
          <input type="hidden" name="corpus_id" value="<?= $corpus->id ?>" />
          <table id="corpora-table-ade" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Reference</th>
                  <th>Candidate</th>
                  <th>Type</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <th>ID</th>
                  <th>Reference</th>
                  <th>Candidate</th>
                  <th>Type</th>
                </tr>
              </tfoot>
            </table>
        <?php } else if ($task->mode == "RAN") { ?>
          <div class="ran-preview">
            <?php foreach ($preview as $line) { ?>
              <div class="row same-height-sm">
                <div class="col-sm-6 same-height-column py-3 ran-preview-item">
                  <div class="row">
                    <div class="col-sm-12 col-xs-12">
                      <div class="text-increase">Source</div>
                      <p>
                        <?= $line->source_text ?>
                      </p>
                    </div>

                    <div class="col-sm-12 col-xs-12">
                      <div class="text-increase">Reference</div>
                      <p>
                        <?= $line->target_text[0]->source_text ?>
                      </p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 same-height-column py-3">
                  <div class="text-increase mb-2">Candidates</div>

                  <?php for($i = 1; $i < count($line->target_text); $i++) { ?>
                  <div class="row mb-4">
                    <div class="col-sm-2 col-xs-4">
                      <span class="label label-default w-100 d-block" style="line-height: inherit; white-space: normal;"><?= $line->target_text[$i]->system ?></span>
                    </div>

                    <div class="col-sm-10 col-xs-12 pl-sm-0">
                      <?= $line->target_text[$i]->source_text; ?>
                    </div>
                  </div>
                  <?php } ?>
                </div>
              </div>
            <?php } ?>
          </div>
        <?php } 
        } ?>
      </div>
    </div>
    <?php
    require_once(TEMPLATES_PATH . "/footer.php");
    ?>
    <?php
    require_once(TEMPLATES_PATH . "/admin_resources.php");
    ?>
  </body>
</html>