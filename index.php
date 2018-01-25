<?php
// load up your config file
require_once($_SERVER['DOCUMENT_ROOT'] . "/resources/config.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/dao/user_dao.php");
require_once(TEMPLATES_PATH . "/header.php");

session_start();

?>
<div class="container">
  <div id="header">
    <h1>Welcome to KEOPS</h1>
  </div>
  hola
  <?= (new DateTime())->format('d/m/Y') ?>
  <?= "HOLA\n" ?>
  <?php
  $user_dao = new user_dao();
  $user = new user_dto();
  //$user = $user_dao->getUser("mbanon");
  echo "<pre>";
  print_r($user);
  echo "</pre>";
  ?>
  <?php echo "ADIOS\n" ?>
</div>
<?php
require_once(TEMPLATES_PATH . "/footer.php");
?>

