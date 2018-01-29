<?php

function checkPostParameters($names){
  $failed = [];
  foreach ($names as $name){
    if (!(isset($_POST[$name]) && $_POST[$name]!=null && $_POST[$name]!=null)){
      array_push($failed, $name);
    }
  }
  return $failed;
}


function getSignUpURL($token) {
  if (isset($token)) {
    return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . "/signup.php?token=" . token;
  } else {
    return "";
  }
}
