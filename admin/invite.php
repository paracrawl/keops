<?php
  // load up your config file
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");

  require_once(RESOURCES_PATH . "/session.php");
  
  if (!$SIGNEDIN) {
    header("Location: /signin.php");
    die();
  }
  else if (!$ADMIN_VIEW_PERMISSIONS) {
    header("Location: /index.php");
    die();
  }
?>
<html>
  <head>
    <meta charset="UTF-8">
    <title></title>
  </head>
  <body>
<?php
  //CHECK ADMIN PERMISSIONS !!!
?>
    <form action="get_token.php" method="post">
      Name: <input type="text" name="name" id="name"><br>
      E-mail: <input type="text" name="email" id="email"><br>
      Campaigns: <input type="text" name="campaigns" id="campaigns" value="NONE"><br>
      <input type="submit">
    </form>

  </body>
</html>