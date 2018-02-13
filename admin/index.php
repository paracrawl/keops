<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Management</title>
    <?php
    require_once(TEMPLATES_PATH . "/admin_head.php");
    ?>
  </head>
  <body>
    <div class="container">
      <?php require_once(TEMPLATES_PATH . "/header.php"); ?>
      <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#dashboard">Dashboard</a></li>
        <li><a data-toggle="tab" href="#users">Users</a></li>
        <li><a data-toggle="tab" href="#projects">Projects</a></li>
        <li><a data-toggle="tab" href="#languages">Languages</a></li>
        <li><a data-toggle="tab" href="#invitations">Invitations</a></li>
        <li><a data-toggle="tab" href="#corpora">Corpora</a></li>
      </ul>
      <div class="tab-content">
                         <?php
          if (isset($_SESSION["error"])) {
            ?>
            <div class="panel panel-danger">
              <div class="panel-heading">
                <h3 class="panel-title"><strong>Oops!</strong></h3>
              </div>
              <div class="panel-body">
                <p>
                  <?php
                  switch ($_SESSION["error"]) {
                    case "missingparams":
                    case "userediterror":
                    case "pojectediterror":
                    case "error":
                    default:
                      echo "Sorry, your request could not be processed. Please, try again later.";                     
                      $_SESSION["error"] = null;
                      break;
                  }
                  ?>
                </p>
              </div>
            </div>
            <?php
          }
          ?>
        <div id="dashboard" class="tab-pane fade in active">
          <h3>Dashboard</h3>
          <p>Number of users / projects / languages / tasks / tasks in progress / ...</p>
        </div>
        <div id="users" class="tab-pane fade">
          <h3>Users</h3>
          <p class="text-right">
            <a href="invite.php" class="btn btn-primary">Send invitation</a>
          </p>

          <hr>          
          <table id="users-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Creation date</th>
                <th>Role</th>
                <th>Active?</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Creation date</th>
                <th>Role</th>
                <th>Active?</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <div id="projects" class="tab-pane fade">
          <h3>Projects</h3>
          <p class="text-right">
            <a href="/projects/project_new.php" class="btn btn-primary">New project</a>
          </p>
          
          <hr>
          <table id="projects-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th title="Source language">SL</th>
                <th title="Target language">TL</th>
                <th>Description</th>
                <th>Creation date</th>
                <th>Owner</th>
                <th>Active?</th>
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
                <th>Description</th>
                <th>Creation date</th>
                <th>Owner</th>
                <th>Active?</th>
                <th>Actions</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <div id="languages" class="tab-pane fade">
          <h3>Languages</h3>
          <p>Existing languages available for projects and users</p>
          <p class="text-right">
            <a href="new_language.php" class="btn btn-primary">Add language</a>
          </p>
          <hr>
          <table id="languages-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Language name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Language name</th>
                <th>Actions</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <div id="invitations" class="tab-pane fade">
          <h3>Invitations</h3>
          <p class="text-right">
            <a href="invite.php" class="btn btn-primary">Send invitation</a>
          </p>
          <hr>
          <table id="invitations-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Date Sent</th>
                <th>Date Used</th>
                <th>Token</th>
                <th>Admin</th>
                <th>Revoke</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Date Sent</th>
                <th>Date Used</th>
                <th>Token</th>
                <th>Admin</th>
                <th>Revoke</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <div id="corpora" class="tab-pane fade">
          <h3>Corpora</h3>
          <p>Files uploaded to create evaluation tasks</p>
          <hr>
          <table id="corpora-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th title="Source language">SL</th>
                <th title="Target language">TL</th>
                <th title="Number of lines in the file">Lines</th>
                <th>Creation date</th>
                <th>Active?</th>
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
                <th>Active?</th>
              </tr>
            </tfoot>
          </table>
          <hr>
          <h3>Upload new corpora</h3>
          <div class="col-xd-10 col-xd-offset-2 text-center">
            <form action="/corpora/corpus_upload.php" class="form-horizontal dropzone dropzone-looks dz-clickable" id="dropzone" method="post" enctype="multipart/form-data">
              <div class="text-left help-block with-errors">1. Select your languages</div>
              <?php
              $language_dao = new language_dao();
              $languages = $language_dao->getLanguages();
              ?>
              <div class="form-group">
                <label for="source_lang" class="control-label col-sm-2">Source language</label>
                <div class="col-sm-4">
                  <select class="form-control" name="source_lang" id="source_lang">
                    <?php foreach ($languages as $lang) { ?>
                      <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
                    <?php } ?>
                  </select>
                </div>
                <label for="target_lang" class="control-label col-sm-2">Target language</label>
                <div class="col-sm-4">
                  <select class="form-control" name="target_lang" id="target_lang">
                    <?php foreach ($languages as $lang) { ?>
                      <option value="<?= $lang->id?>"><?= $lang->langcode . " - " . $lang->langname ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="text-left help-block with-errors">2. Select your TSV files* for this language pair</div>
              <div class="form-group dz-message">
                <h2><span class="glyphicon glyphicon-upload" aria-hidden="true"></span></h2>
                Click here or drag and drop files*<br>
                <small>(you can upload more than one file at once)</small>
              </div>
              <div class="text-left help-block with-errors">3. Repeat steps 1 and 2 for new language pairs as many as you want</div>
            </form>
          </div>
          <p>* Currently, only tab-separated-value files with one pair of sentences per line are supported.</p>
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
