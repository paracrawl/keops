<?php
  /**
   * Page to create a new invitation
   */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");

  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>KEOPS | Upload corpora</title>
  <?php
  require_once(TEMPLATES_PATH . "/admin_head.php");
  ?>
</head>
<body>
  <div class="container">
    <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
    <ul class="breadcrumb">
      <li><a href="/admin/index.php">Management</a></li>
      <li><a href="/admin/index.php#invitations">Corpora</a></li>
      <li class="active">Upload corpora</li> 
    </ul>

    <div class="page-header mb-0 mt-2">
      <span class="h3">Upload corpora</span>
    </div>

    <ul class="nav nav-tabs" role="tablist" id="corpus-upload-tabs">
      <li role="presentation" class="active"><a href="#corpora_validation" data-toggle="tab">Validation</a></li>
      <li role="presentation"><a href="#corpora_adequacy" data-toggle="tab">Adequacy</a></li>
      <li role="presentation"><a href="#corpora_fluency" data-toggle="tab">Fluency</a></li>
      <li role="presentation"><a href="#corpora_ranking" data-toggle="tab">Ranking</a></li>
    </ul>

    <div class="tab-content pt-4">
      <div class="tab-pane active" id="corpora_validation">
        <div class="row">
          <div class="col-md-12 col-xs-12">
            <form action="/corpora/corpus_upload.php" class="form-horizontal dropzone dropzone-looks dz-clickable" id="dropzoneval" method="post" enctype="multipart/form-data">
              <input type="hidden" name="mode" value="VAL" />

              <div class="row">
                <div class="col-md-6 col-xs-12">
                  <div class="text-center-sm help-block with-errors">
                    1. Select your languages
                  </div>
                  <?php
                  $language_dao = new language_dao();
                  $languages = $language_dao->getLanguages();
                  ?>
                  <div class="form-group mx-0">
                    <label for="source_lang" class="control-label">Source language</label>
                    <select class="form-control source_lang" name="source_lang">
                      <?php foreach ($languages as $lang) { ?>
                      <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group mx-0">
                    <label for="target_lang" class="control-label">Target language</label>
                    <select class="form-control target_lang" name="target_lang">
                      <?php foreach ($languages as $lang) { ?>
                        <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-xs-12">
                  <div class="text-center-sm help-block with-errors">
                    2. Select your TSV files for this language pair
                  </div>
                  <div class="form-group dz-message">
                    <h2><span class="glyphicon glyphicon-upload" aria-hidden="true"></span></h2>
                    Click here or drag and drop files <br>
                    <small>(you can upload more than one file at once)</small>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="col-md-12 col-xs-12">
            <div class="h3 my-4">Uploaded corpora</div>
            <table class="corpora-table table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th title="Source language">SL</th>
                  <th title="Target language">TL</th>
                  <th title="Number of lines in the file">Lines</th>
                  <th>Creation date</th>
                  <th>Type</th>
                  <th>On?</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th title="Source language">SL</th>
                  <th title="Target language">TL</th>
                  <th title="Number of lines in the file">Lines</th>
                  <th>Creation date</th>
                  <th>Type</th>
                  <th>On?</th>
                  <th>Actions</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="tab-pane" id="corpora_adequacy">
        <form action="/corpora/corpus_upload.php" class="form-horizontal dropzone dropzone-looks dz-clickable" id="dropzoneade" method="post" enctype="multipart/form-data">
          <div class="row">
            <input type="hidden" name="mode" value="ADE" />
            <div class="col-md-6 col-xs-12">
              <div class="text-center-sm help-block with-errors">
                1. Select your languages
              </div>
              <?php
              $language_dao = new language_dao();
              $languages = $language_dao->getLanguages();
              ?>
              <div class="form-group mx-0">
                <label for="source_lang" class="control-label">Source language</label>
                <select class="form-control source_lang" name="source_lang">
                  <?php foreach ($languages as $lang) { ?>
                  <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group mx-0">
                <label for="target_lang" class="control-label">Target language</label>
                <select class="form-control target_lang" name="target_lang">
                  <?php foreach ($languages as $lang) { ?>
                    <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-md-6 col-xs-12">
              <div class="text-center-sm help-block with-errors">
                2. Select your TSV files for this language pair
              </div>
              <div class="form-group dz-message">
                <h2><span class="glyphicon glyphicon-upload" aria-hidden="true"></span></h2>
                Click here or drag and drop files <br>
                <small>(you can upload more than one file at once)</small>
              </div>
            </div>

            <div class="col-md-6 col-xs-12 col-md-offset-6 text-center">
              <div class="dropzone-previews"></div>
            </div>
          </div>
        </form>

        <div class="row mt-4">
          <div class="col-xs-12 col-md-12">
            <strong class="h3">HIT description</strong>
          </div>

          <div class="col-xs-12 col-md-12 mt-4">
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
          </div>
        </div>
      </div>

      <div class="tab-pane" id="corpora_fluency">
        <div class="row">
          <div class="col-md-12 col-xs-12">
            <form action="/corpora/corpus_upload.php" class="form-horizontal dropzone dropzone-looks dz-clickable" id="dropzoneflu" method="post" enctype="multipart/form-data">
              <input type="hidden" name="mode" value="FLU" />
              <input type="hidden" name="source_lang" value="NULL" />

              <div class="row">
                <div class="col-md-6 col-xs-12">
                  <div class="text-center-sm help-block with-errors">
                    1. Select your language
                  </div>
                  <?php
                  $language_dao = new language_dao();
                  $languages = $language_dao->getLanguages();
                  ?>
                  <div class="form-group mx-0">
                    <label for="target_lang" class="control-label">Target language</label>
                    <select class="form-control target_lang" name="target_lang">
                      <?php foreach ($languages as $lang) { ?>
                      <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-xs-12">
                  <div class="text-center-sm help-block with-errors">
                    2. Select your TSV files for this language pair
                  </div>
                  <div class="form-group dz-message">
                    <h2><span class="glyphicon glyphicon-upload" aria-hidden="true"></span></h2>
                    Click here or drag and drop files <br>
                    <small>(you can upload more than one file at once)</small>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="col-md-12 col-xs-12">
            <div class="h3 my-4">Uploaded corpora</div>
            <table class="corpora-table table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th title="Source language">SL</th>
                  <th title="Target language">TL</th>
                  <th title="Number of lines in the file">Lines</th>
                  <th>Creation date</th>
                  <th>Type</th>
                  <th>On?</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th title="Source language">SL</th>
                  <th title="Target language">TL</th>
                  <th title="Number of lines in the file">Lines</th>
                  <th>Creation date</th>
                  <th>Type</th>
                  <th>On?</th>
                  <th>Actions</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <div class="tab-pane" id="corpora_ranking">
        <div class="row">
          <div class="col-md-12 col-xs-12">
            <form action="/corpora/corpus_upload.php" class="form-horizontal dropzone dropzone-looks dz-clickable" id="dropzoneran" method="post" enctype="multipart/form-data">
              <input type="hidden" name="mode" value="RAN" />

              <div class="row">
                <div class="col-md-6 col-xs-12">
                  <div class="text-center-sm help-block with-errors">
                    1. Select your languages
                  </div>
                  <?php
                  $language_dao = new language_dao();
                  $languages = $language_dao->getLanguages();
                  ?>
                  <div class="form-group mx-0">
                    <label for="source_lang" class="control-label">Source language</label>
                    <select class="form-control source_lang" name="source_lang">
                      <?php foreach ($languages as $lang) { ?>
                      <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="form-group mx-0">
                    <label for="target_lang" class="control-label">Target language</label>
                    <select class="form-control target_lang" name="target_lang">
                      <?php foreach ($languages as $lang) { ?>
                        <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-xs-12">
                  <div class="text-center-sm help-block with-errors">
                    2. Select your TSV files for this language pair
                  </div>
                  <div class="form-group dz-message">
                    <h2><span class="glyphicon glyphicon-upload" aria-hidden="true"></span></h2>
                    Click here or drag and drop files <br>
                    <small>(you can upload more than one file at once)</small>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="col-md-12 col-xs-12">
            <div class="h3 my-4">Uploaded corpora</div>
            <table class="corpora-table table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th title="Source language">SL</th>
                  <th title="Target language">TL</th>
                  <th title="Number of lines in the file">Lines</th>
                  <th>Creation date</th>
                  <th>Type</th>
                  <th>On?</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th title="Source language">SL</th>
                  <th title="Target language">TL</th>
                  <th title="Number of lines in the file">Lines</th>
                  <th>Creation date</th>
                  <th>Type</th>
                  <th>On?</th>
                  <th>Actions</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
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