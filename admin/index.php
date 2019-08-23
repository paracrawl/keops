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
              <a href="upload.php" type="submit" class="btn btn-link">
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

        <div id="invite_token_modal" class="modal fade" role="dialog" >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header" id="invitation-link-modal">
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
