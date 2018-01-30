<!DOCTYPE html>
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