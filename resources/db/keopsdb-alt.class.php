<?php
class keopsdb extends PDO{
  
 private $dbname = getenv("KEOPS_DB_NAME");
 private $host = getenv("KEOPS_DB_HOST");
 private $user = getenv("KEOPS_DB_USER");
 private $pass = getenv("KEOPS_DB_PASS");
 private $port = getenv("KEOPS_DB_PORT");
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
