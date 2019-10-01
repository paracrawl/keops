<?php
/**
 * Session utils
 */
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dto/user_dto.php");

if(!isset($_SESSION)) { 
  session_start();
}

/**
 * Checks if the user is logged in
 * 
 * @return boolean True if the user is logged, otherwise false
 */
function isSignedIn(){
//if(isset($_SESSION["userinfo"])) {
//  echo "<pre>";
//  print_r($_SESSION["userinfo"]);
//  echo "</pre>";
//}
  return isset($_SESSION["userinfo"]);
}

/**
 * Retrieves the user role (ADMIN, PM or USER)
 * 
 * @return string User role
 */
function getUserRole(){
  $role = null;
  if (isset($_SESSION["userinfo"])) {
    $user = $_SESSION["userinfo"];
    $role = $user->role;
  }
  return $role;
}

/**
 * Retrieves the user id
 * 
 * @return int User ID or -1 if ROOT
 */
function getUserId(){
  if (isRoot()) return -1;

  $id = null;
  if (isset($_SESSION["userinfo"])) {
    $user = $_SESSION["userinfo"];
    $id = $user->id;
  }
  return $id;
}

/**
 * Checks if the user is deactivated
 * 
 * @return boolean True if the user is active, otherwise false
 */
function isActive(){
  $active = null;
  if (isset($_SESSION["userinfo"])) {
    $user = $_SESSION["userinfo"];
    $active = $user->active;
  }
  return $active;
}

/**
 * Checks if the user can be the Admin zone
 * 
 * @return boolean True if he/she can view the admin zone, otherwise false
 */
function canSeeAdminView() {
  $role = getUserRole();
  if ($role != null && ($role == user_dto::PM || $role == user_dto::ROOT) && isActive()) {
    return true;
  }
}

/**
 * Checks if the user is ROOT
 * 
 * @return boolean True if he/she can view the admin zone, otherwise false
 */
function isRoot() {
  $role = getUserRole();
  return ($role != null && $role == user_dto::ROOT && isActive());
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
