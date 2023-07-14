<?php
/*
 * Page for the preview of the first sentences of a corpus
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/corpus_dao.php");
//  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
//  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
  
  $corpus_id = filter_input(INPUT_GET, "id");
  if (!isset($corpus_id)) {
    header("Location: /admin/index.php#corpora");
    die();
  }
  $corpus_dao = new corpus_dao();
  $corpus = $corpus_dao->getCorpusById($corpus_id);
  $language_dao = new language_dao();
  $languages = $language_dao->getLanguages();
  $corpus_sl = "";
  $corpus_tl = "";
          
  $preview = $corpus_dao->getSentencesFromCorpus($corpus_id, 20);

  foreach ($languages as $lang){
    if ($lang->id == $corpus->source_lang){
      $corpus_sl = $lang->langname;
    }
    if ($lang->id == $corpus->target_lang){
      $corpus_tl = $lang->langname;
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Corpus preview </title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="breadcrumb">
        <li><a href="/admin/index.php">Management</a></li>
        <li><a href="/admin/index.php#corpora">Corpora</a></li>
        <li class="active"><?= $corpus->name?></a></li>
      </ul>
      <div class="title-container row vertical-align-sm">
        <div class="col-xs-12 col-sm-6 col-md-push-6 text-right-sm">
          <a href="/corpora/corpus_edit.php?id=<?php echo $corpus->id;?>" type="submit" title="Edit corpus" class="btn btn-link mr-4 mr-sm-0">
            <span class="glyphicon glyphicon-edit"></span>
            <span class="col-xs-12">Edit</span>
          </a> 
          <a href="/corpora/corpus_remove.php?id=<?php echo $corpus->id;?>" type="submit" title="Remove corpus" class="btn btn-link ml-sm-4">
            <span class="text-danger">
              <span class="glyphicon glyphicon-trash"></span>
              <span class="col-xs-12">Remove</span>
            </span>
          </a>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-pull-6">
          <h1><?= $corpus->name?></h1>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <p><strong>ID:</strong> <?=$corpus->id ?></p>
        </div> 
        <div class="col-md-3">
          <p><strong>Name:</strong> <?=$corpus->name ?></p>
        </div>
        <?php if ($corpus->mode != "FLU") { ?>
        <div class="col-md-3">
          <p><strong>Source language: </strong> <?= $corpus_sl?></p>
        </div>
        <?php } ?>
        <div class="col-md-3">
          <p><strong>Target language: </strong> <?= $corpus_tl?></p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
           <p><strong>Lines: </strong> <?= $corpus->lines?></p>
        </div>
        <div class="col-md-3">
           <p><strong>Creation date: </strong> <?= getFormattedDate($corpus->creation_date) ?></p>
        </div>
        <div class="col-md-3">
           <p><strong>Active?: </strong>  <input disabled type="checkbox" name="active"<?= $corpus->active ? " checked" : "" ?>></p>
        </div>
      </div>
      <div class="title-container">
        <h3>Corpus preview</h3>
      </div>
      
        <?php
        if (in_array($corpus->mode, ["VAL", "FLU", "PAR", "VAL_MAC", "MONO"])) {
          foreach ($preview as $line){
            ?>
            <div class="row">
              <div class="col-md-12 sentence-source"><?php echo $line->source_text;?></div>
              <?php foreach ($line->target_text as $target_text) { ?>
                <div class="col-md-12 sentence-target">
                  <?= $target_text->source_text ?>
                </div>
              <?php } ?>
            </div>
          <hr>
          <?php
          }
        } else if ($corpus->mode == "ADE") {
        ?>
          <input type="hidden" name="corpus_id" value="<?= $corpus->id ?>" />
          <table id="corpora-table-ade-preview" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
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
        <?php } else if ($corpus->mode == "RAN") { ?>
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
        <?php } ?>
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