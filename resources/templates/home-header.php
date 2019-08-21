<?php
  require_once(RESOURCES_PATH . "/session.php");
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="home-container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <div navbar-brand>
            <a href="/"><img class="navbar-logo" src="/img/tiny-pyramids-white.png" alt="KEOPS logo"></a>
            <a href="/" class="navbar-logo-brand">KEOPS</a>
          </div>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">

          </ul>

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

        </div><!--/.nav-collapse -->
      </div>
    </nav>
