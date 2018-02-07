<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dao/user_dao.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/utils/utils.php");

$PAGETYPE = "admin";
require_once(RESOURCES_PATH . "/session.php");

$failedparams = checkPostParameters(["id", "name", "email", "role"]);

if (count($failedparams) == 0) {
  
  $id = $_POST["id"];
  $name = $_POST["name"];
  $email = $_POST["email"];
  $role = $_POST["role"];
  $active = "false";  
  
  if (isset($_POST["active"]) && $_POST["active"]=="on") {
    $active = "true";
  }
  
  $user_dao = new user_dao();

  $user_dto = user_dto::newUser($id, $name, $email, $role, $active);

 if ($user_dao->updateUser($user_dto)) {
      $_SESSION["error"] = null;
      //$_SESSION["userinfo"] = $user_dto;
      if ($_SESSION["userinfo"]->id == $user_dto->id) {
        $_SESSION["userinfo"] = $user_dto;
      }
      header("Location: /admin/index.php#users");
      die();      
 }
 else {
      $_SESSION["error"] = "userediterror";
      header("Location: /admin/index.php#users");
      die();
  }
}

else {
  $_SESSION["error"] = "missingparams";
      header("Location: /admin/index.php#users");
      die();
}

  
