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

function getUserId(){
  $id = null;
  if (isset($_SESSION["userinfo"])) {
    $user = $_SESSION["userinfo"];
    $id = $user->id;
  }
  return $id;
}

function isActive(){
  $active = null;
  if (isset($_SESSION["userinfo"])) {
    $user = $_SESSION["userinfo"];
    $active = $user->active;
  }
  return $active;
}

function canSeeAdminView() {
  $role = getUserRole();
  if ($role != null && ($role == user_dto::ADMIN || $role == user_dto::STAFF) && isActive()) {
    return true;
  }
}

$SIGNEDIN = isSignedIn();
$ADMIN_VIEW_PERMISSIONS = canSeeAdminView();

if (!isActive() && $PAGETYPE!="public"){  
  header("Location: /index.php");
  die();
}


if ($SIGNEDIN) {
  $USER = $SIGNEDIN ? $_SESSION["userinfo"] : null;
  
  if (isset($PAGETYPE) && $PAGETYPE == "admin" && !$ADMIN_VIEW_PERMISSIONS) {
    header("Location: /index.php");
    die();
  }
  else if (!isset($PAGETYPE)) {
    error_log("WARNING: We couldn't verify if the user has access to this page. Please check that PAGETYPE is set at the beginning of the page.");
  }
}
else if (!$SIGNEDIN && $PAGETYPE != "public") {

  header("Location: /signin.php");
  die();
}
