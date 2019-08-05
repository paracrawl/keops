<?php
/**
 * Admin index page (Management)
 */
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/language_dao.php");
  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html lang="en">
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
<!--        <li class="active"><a data-toggle="tab" href="#dashboard">Dashboard</a></li>-->
        <li class="active"><a data-toggle="tab" href="#projects">Projects</a></li>
        <li><a data-toggle="tab" href="#users">Users</a></li>       
        <li><a data-toggle="tab" href="#languages">Languages</a></li>
        <li><a data-toggle="tab" href="#invitations">Invitations</a></li>
        <li><a data-toggle="tab" href="#corpora">Corpora</a></li>
      </ul>
      <div class="tab-content">
        <?php if (isset($_SESSION["error"])) { ?>
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
                    case "corpusediterror":
                    case "corpusremoveerror":
                    case "projectremoveerror":
                    case "taskremoveerror":
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
        <?php } ?>
<!--        <div id="dashboard" class="tab-pane fade in active">
          <h3>Dashboard</h3>
          <p>Number of users / projects / languages / tasks / tasks in progress / ...</p>
        </div>-->
    <div id="projects" class="tab-pane fade in active">
      <div class="title-container row vertical-align">
        <div class="col-xs-6 col-md-6">
          <h3>Projects</h3>
        </div>
        <div class="col-xs-6 col-md-6 text-right">
          <a href="/projects/projects_stats.php" class="btn btn-link" title="Recap of all the tasks which belong to your projects">
            <span class="glyphicon glyphicon-stats"></span>
            <span class="col-xs-12">Recap all</span>
          </a>
          <a href="/projects/project_new.php" class="btn btn-link" title="New project">
            <span class="glyphicon glyphicon-plus"></span>
            <span class="col-xs-12">New</span>
          </a>
        </div>
      </div>
        <hr>
        <table id="projects-table" style="width:100%;" class="table table-striped table-bordered display responsive wrap" cellspacing="0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Description</th>
              <th>Status</th>
              <th>Creation date</th>
              <th>Owner</th>
              <th>On?</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
          <tfoot>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Description</th>
              <th>Status</th>
              <th>Creation date</th>
              <th>Owner</th>
              <th>On?</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>

      <div id="users" class="tab-pane fade">
        <div class="title-container row vertical-align">
          <div class="col-xs-6 col-md-6">
            <h3>Users</h3>
          </div>
          <div class="col-xs-6 col-md-6 text-right">
            <a href="invite.php" class="btn btn-link" title="Send invitation">
              <span class="glyphicon glyphicon-plus"></span>
              <span class="col-xs-12">Send invitation</span>
            </a>
          </div>
        </div>
          <hr>          
          <table id="users-table" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Creation date</th>
                <th>Role</th>
                <th>On?</th>
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
                <th>On?</th>
              </tr>
            </tfoot>
          </table>
        </div>
        
        <div id="languages" class="tab-pane fade">
          <div class="title-container row vertical-align">
            <div class="col-xs-6 col-md-6">
              <h3>Languages</h3>
              Existing languages available for projects and users
            </div>
            <div class="col-xs-6 col-md-6 text-right">
              <a href="new_language.php" class="btn btn-link" title="Add language">
                <span class="glyphicon glyphicon-plus"></span>
                <span class="col-xs-12">Add language</span>
              </a>             
            </div>
          </div>
          <hr>
          <table id="languages-table" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
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
          <div class="title-container row vertical-align">
            <div class="col-xs-6 col-md-6">
              <h3>Invitations</h3>
            </div>
            <div class="col-xs-6 col-md-6 text-right">
              <a href="invite.php" class="btn btn-link">
                <span class="glyphicon glyphicon-plus" title="Send invitation"></span>
                <span class="col-xs-12">Send invitation</span>
              </a>
            </div>
          </div>
          <hr>
          <table id="invitations-table" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
            <thead>
              <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Date Sent</th>
                <th>Date Used</th>
                <th>Token</th>
                <th>Admin</th>
                <th>Actions</th>
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
                <th>Actions</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <div id="corpora" class="tab-pane fade">
          <div class="title-container row vertical-align">
            <div class="col-xs-6 col-md-6">
              <h3>Corpora</h3>
              Files uploaded to create evaluation tasks.
            </div>
            <div class="col-xs-6 col-md-6 text-right">
              <a href="#upload" type="submit" class="btn btn-link">
                <span class="glyphicon glyphicon-cloud-upload"></span>
                <span class="col-xs-12">Upload</span>
              </a>
            </div>
          </div>
            <hr>
            <table id="corpora-table" class="table table-striped table-bordered display responsive nowrap" cellspacing="0" style="width:100%;">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th title="Source language">SL</th>
                <th title="Target language">TL</th>
                <th title="Number of lines in the file">Lines</th>
                <th>Creation date</th>
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
                <th>On?</th>
                <th>Actions</th>
              </tr>
            </tfoot>
          </table>
          <hr>
          <h3 id="upload">Upload new corpora</h3>
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
<!--          <div id="custom-dz-template">
            <div class="dz-preview dz-file-preview">
              <div class="dz-details">
                <div class="dz-filename"><span data-dz-name></span></div>
                <div class="dz-size" data-dz-size></div>
                <img data-dz-thumbnail />
              </div>
              <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
              <div class="dz-success-mark"><span class="glyphicon glyphicon-ok-sign green big-icon"></span></div>
              <div class="dz-error-mark"><span class="glyphicon glyphicon-remove-sign red big-icon"></span></div>
              <div class="dz-error-message"><span data-dz-errormessage></span></div>
            </div>
          </div>-->
        </div>
      </div>
      <div id="invite_token_modal" class="modal fade" role="dialog" >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header" id="invitation-link-modal">
<!--              <button type="button" class="close" data-dismiss="modal">&times;</button>-->
              <h4 class="modal-title">Invitation link</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" role="form" >
                <div class="form-group">
                  <label for="email" class="col-sm-1 control-label">Email</label>
                  <div class="col-sm-9">
                    <input id="modal-email" type="email" readonly name="email" class="form-control" aria-describedby="helpEmail" placeholder="Email address" maxlength="200" required="" autofocus="">
                  </div>
                </div>
                <div class="form-group">
                  <div class="invitation_token">
                    <label for="token" class="col-sm-1 control-label">Invitation</label>
                    <div class="col-sm-9">
                      <div class="input-group">
                        <div class="input-group-addon"><span class="glyphicon glyphicon-copy" aria-hidden="true"></span></div>
                        <input id="token" class="form-control" readonly aria-describedby="helpInvitation" placeholder="The invitation URL will appear here" maxlength="200">
                      </div>
                      <div id="helpInvitation" class="help-block with-errors">Please copy this URL and send to the new user. They will be able to sign up with the token ID.</div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
