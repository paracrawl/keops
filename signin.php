<?php    
  // load up your config file
  require_once($_SERVER['DOCUMENT_ROOT'] ."/resources/config.php");

  require_once(TEMPLATES_PATH . "/header.php");
  
if(!isset($_SESSION)) { 
        session_start(); 
    } 
?>
    <div class="container signin">
      <div class="page-header">
        <h1>Sign in</h1>
        <p>Please enter your username and password to access the system.</p>
      </div>
      <form class="form-signin" role="form" data-toggle="validator">
        
        <div class="form-group">
          <label for="email" class="sr-only control-label">Email address</label>
          <input type="email" name="email" class="form-control" placeholder="Email address" required="" autofocus="">
          <span class="glyphicon form-control-feedback" aria-hidden="true"></span>
          <div class="help-block with-errors">Enter your email address</div>
        </div>
        <div class="form-group">
          <label for="password" class="sr-only control-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Password" required="">
          <div class="help-block with-errors">Enter your password</div>
        </div>
        <div class="form-group">
          <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        </div>
      </form>
    </div>
<?php
  require_once(TEMPLATES_PATH . "/footer.php");
?>
