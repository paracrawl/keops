<?php
  require_once(RESOURCES_PATH . "/session.php");
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <?php if ($SIGNEDIN) { ?>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button> <?php } ?>
           <div navbar-brand>
            <a href="/"><img class="navbar-logo" src="/img/tiny-pyramids-white.png" alt="KEOPS logo"></a>
            <a href="/" class="navbar-logo-brand">KEOPS</a>
          </div>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <?php
            if ($SIGNEDIN) {
              ?>
              <li><a href="/index.php">Tasks</a></li>
              <?php
            }
            if ($ADMIN_VIEW_PERMISSIONS) {
            ?>
            <li><a href="/admin/index.php">Management</a></li>
            <?php
            }
            ?>
          </ul>
          <?php
          if ($SIGNEDIN) {
          ?>
          <ul class="nav navbar-nav navbar-right">
            <?php if (isset($project) && isset($project->owner_object)) { ?>
            <li>
              <a href="/contact.php?p=<?= $project->id  ?>" title="Contact Project Manager">Contact PM <span id="contact-mail-logo" class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a>
            </li>
            <?php } ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $USER->name ?><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Support</li>
                <li><a href="/contact.php">Contact support</a></li>
                <li class="dropdown-header">Account</li>
                <li><a href="/users/user_logout.php">Sign out</a></li>
              </ul>
            </li>
          </ul>
          <?php
          }
          ?>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
