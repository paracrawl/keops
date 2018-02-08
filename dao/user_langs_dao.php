<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_langs_dto.php");

class user_langs_dao {
  private $conn;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
}