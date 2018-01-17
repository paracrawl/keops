<?php

function postgresql_connect($user, $pass, $host, $port, $bd) {
  $conn = null;
  try {
    $conn = pg_connect("user=" . $user . " " . "password=" . $pass . " " . "host=" . $host . " " . "port=" . $port. "dbname=" . $bd . " connect_timeout=5");
    if ($conn == false) {
      throw new Exception("DB connection error: " . pg_last_error());
    }
  } catch (Exception $ex) {
    throw $ex;
  }
  return $conn;
}

?>