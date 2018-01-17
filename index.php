<?php    
    // load up your config file
    require_once("resources/config.php");
    require_once("dao/user_dao.php"); 
    
    
    require_once(TEMPLATES_PATH . "/header.php");
?>
    <div class="container">
      <div id="header">
          <h1>Welcome to KEOPS</h1>
      </div>
        hola
        <?= (new DateTime())->format('d/m/Y') ?>
        <?= "HOLA\n" ?>
        <?php echo "ADIOS\n" ?>
    </div>
<?php
    require_once(TEMPLATES_PATH . "/footer.php");
?>

