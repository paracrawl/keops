<?php    
  // load up your config file
  require_once($_SERVER['DOCUMENT_ROOT'] ."/resources/config.php");

  require_once(TEMPLATES_PATH . "/header.php");
  
if(!isset($_SESSION)) { 
        session_start(); 
    } 
?>
    <div class="container">
      <div class="page-header">
        <h1>Sign in</h1>
        <p>Please enter your username and password to access the system.</p>
      </div>
      <form class="form-signin" action="users/user_login.php" method="post">
        <label for="email" class="sr-only">Email address</label>
        <input type="text" name="email" class="form-control" placeholder="Email address" required="" autofocus="">
        <label for="password" class="sr-only">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Password" required="">
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>
    </div>
<?php
  require_once(TEMPLATES_PATH . "/footer.php");
?>
