<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
  $PAGETYPE = "admin";
  require_once(RESOURCES_PATH . "/session.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | Home</title>
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
            <a href="projects/project_new.php" class="btn btn-primary">New project</a>
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
              </tr>
            </tfoot>
          </table>
        </div>
        <div id="languages" class="tab-pane fade">
          <h3>Languages</h3>
          <p>Existing languages available for projects and users</p>
          <hr>
          TODO: add new languages
          <hr>
          <table id="languages-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Language name</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
              <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Language name</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <div id="invitations" class="tab-pane fade">
          <h3>Invitations</h3>
          <hr>
          <table id="users-table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Date Sent</th>
                <th>Date Used</th>
                <th>Token</th>
                <th>Admin</th>
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
