<?php    
  // load up your config file
  require_once("resources/config.php");

  require_once(TEMPLATES_PATH . "/header.php");
?>
    <div class="container">
      <div class="page-header">
        <h1>Sign in</h1>
        <p>Please enter your username and password to access the system.</p>
      </div>
      <form class="form-signin">
        <label for="username" class="sr-only">Email address</label>
        <input type="text" name="username" class="form-control" placeholder="Email address" required="" autofocus="">
        <label for="password" class="sr-only">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Password" required="">
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>
    </div>
<?php
  require_once(TEMPLATES_PATH . "/footer.php");
?>
