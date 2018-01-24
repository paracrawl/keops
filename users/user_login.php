<?php

if (isset($_POST['email']) && isset($_POST['password'])){
  $email = $_POST["email"];
  $password = $_POST["password"];
  
  echo $email . " - ". $password;
}
else{
echo "<pre>";
print_r($_POST);
echo "</pre>";
  
}