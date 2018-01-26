<?php
if(!isset($_SESSION)) { 
  session_start();
}

function isSignedIn(){
  $failed = [];
  foreach ($names as $name){
    if (!(isset($_POST[$name]) && $_POST[$name]!=null && $_POST[$name]!=null)){
      array_push($failed, $name);
    }
  }
  return $failed;
}