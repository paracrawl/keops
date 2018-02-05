<?php
  require_once(RESOURCES_PATH . "/session.php");
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">KEOPS</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="/index.php">Tasks</a></li>
            <?php
            if ($ADMIN_VIEW_PERMISSIONS) {
            ?>
            <li><a href="/admin">Management</a></li>
            <?php
            }
            ?>
          </ul>
          <?php
          if ($SIGNEDIN) {
          ?>
          <ul class="nav navbar-nav navbar-right">

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $USER->name ?><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Account</li>
                <li><a href="/users/user_logout.php">Sign out <?= $USER->name ?></a></li>
              </ul>
            </li>
          </ul>
          <?php
          }
          else {
          ?>
          <form class="navbar-form navbar-right" action="users/user_login.php"  method="post" >
            <div class="form-group">
              <input type="text" id="email" name="email" placeholder="Email address" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" id="password" name="password" placeholder="Password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
            <a href="/signup.php" type="button" class="btn btn-primary" method="post">Sign up</a>
          </form>
          <?php
          }
          ?>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
