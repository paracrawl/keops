<?php
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/user_dto.php");

if(!isset($_SESSION)) { 
  session_start();
}

function isSignedIn(){
  return isset($_SESSION["userinfo"]);
}

function getUserRole(){
  $role = null;
  if (isset($_SESSION["userinfo"])) {
    $user = $_SESSION["userinfo"];
    $role = $user->role;
  }
  return $role;
}

$SIGNEDIN = isSignedIn();
$USER_ROLE = getUserRole();
$USER = $_SESSION["userinfo"];
