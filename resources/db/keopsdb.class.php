<?php
class keopsdb extends PDO{
  
 private $dbname = "keopsdb";
 private $host = "localhost";
 private $user = "keopsdb";
 private $pass = "PASSWORD_FOR_USER_KEOPS";
 private $port = 5432;
 private $dbh;
 
 public function __construct(){
    try {
      $this->dbh = parent::__construct("pgsql:host=$this->host;port=$this->port;dbname=$this->dbname;user=$this->user;password=$this->pass");
    } catch (PDOException $ex) {
      throw new Exception("DB connection error: " . $ex->getMessage());
    }
  }

  public function close_conn() {
    $this->dbh = null;
  }

}
