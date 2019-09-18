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
      <div class="row">
      <div class="col-sm-4 col-sm-push-8 col-xs-12 text-right-sm mb-4 mb-sm-0">
          <a href="#" class="btn btn-link" data-toggle="modal" data-target="#upload_help_modal">
            <span class="glyphicon glyphicon-question-sign d-inline"></span> 
            First time uploading corpora?
          </a>
        </div>

        <div class="col-sm-8 col-sm-pull-4 col-xs-12">
          <span class="h3">Upload corpora</span>
        </div>
      </div>
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
            <table id="corpora-table-val" class="corpora-table table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
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
            <table id="corpora-table-flu" class="corpora-table table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
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
            <table id="corpora-table-ran" class="corpora-table table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
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

  <div class="modal fade" id="upload_help_modal" tabindex="-1" role="dialog" aria-labelledby="upload_help_modalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="upload_help_modalLabel">Uploading corpora</h4>
        </div>
        <div class="modal-body" id="examples">
          <p>
            Corpora is uploaded to KEOPS in TSV format. This format uses one line per record and a tab character to
            separate fields.
          </p>
          <p>
            The specific format of the TSV file is explained below for each of the evaluation modes.
            You can also download a template and use it to upload your data.
          </p>

          <div class="row">
            <div class="col-xs-12 col-md-12 h5 px-inherit py-3">
              Validation
              <a href="/corpora/templates/validation.tsv" class="pull-right"><span class="glyphicon glyphicon-download"></span> Template (.tsv)</a>
            </div>
            <div class="col-xs-12 col-md-12 px-inherit py-3">
              <table class="table table-no-left-padding">
                <thead>
                  <tr>
                    <th>Source text</th>
                    <th><kbd>Tab</kbd></th>
                    <th>Target text</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>You can contact our customer service department using the form below.</td>
                    <td><kbd>Tab</kbd></td>
                    <td>Puedes ponerte en contacto con nuestro departamento de servicio al cliente mediante el siguiente formulario.</td>
                  </tr>
                </tbody>
              </table>

              <p>You should only include one target text for each source text.</p>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-md-12 h5 px-inherit py-3">
              Adequacy
              <a href="/corpora/templates/adequacy.tsv" class="pull-right"><span class="glyphicon glyphicon-download"></span> Template (.tsv)</a>
            </div>
            <div class="col-xs-12 col-md-12 px-inherit py-3">
              <table class="table table-no-left-padding">
                <thead>
                  <tr>
                    <th>Source text</th>
                    <th><kbd>Tab</kbd></th>
                    <th>Candidate translation</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>You can contact our customer service department using the form below.</td>
                    <td><kbd>Tab</kbd></td>
                    <td>Puedes ponerte en contacto con nuestro departamento de servicio al cliente mediante el siguiente formulario.</td>
                  </tr>
                </tbody>
              </table>

              <p>You should only include one candidate translation for each source text.</p>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-md-12 h5 px-inherit py-3">
              Fluency
              <a href="/corpora/templates/fluency.tsv" class="pull-right"><span class="glyphicon glyphicon-download"></span> Template (.tsv)</a>
            </div>
            <div class="col-xs-12 col-md-12 px-inherit py-3">
              <table class="table table-no-left-padding">
                <thead>
                  <tr>
                    <th>Target text</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>You can contact our customer service department using the form below.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-12 col-md-12 h5 px-inherit py-3">
              Ranking
              <a href="/corpora/templates/ranking.tsv" class="pull-right"><span class="glyphicon glyphicon-download"></span> Template (.tsv)</a>
            </div>
            <div class="col-xs-12 col-md-12 px-inherit py-3">
              <div class="table-responsive w-100" style="border: 0;">
                <table class="table table-no-left-padding">
                  <thead>
                    <tr>
                      <th>Source text</th>
                      <th><kbd>Tab</kbd></th>
                      <th>Reference text</th>
                      <th><kbd>Tab</kbd></th>
                      <th>Name of system 1</th>
                      <th style="vertical-align:bottom;"><kbd>Tab</kbd></th>
                      <th>Name of system 2</th>
                      <th style="vertical-align:bottom;"><kbd>Tab</kbd></th>
                      <th>...</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Use and maintenance manual</td>
                      <td><kbd>Tab</kbd></td>
                      <td>Manual de uso y mantenimiento</td>
                      <td><kbd>Tab</kbd></td>
                      <td>Manual de empleo y manutención</td>
                      <td><kbd>Tab</kbd></td>
                      <td>Manual de empleo y manutención</td>
                      <td><kbd>Tab</kbd></td>
                      <td>...</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <p>Include as many systems as you want.</p>
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
  require_once(TEMPLATES_PATH . "/admin_resources.php");
  ?>
</body>
</html>