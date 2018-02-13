<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/language_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class language_dao {
  private $conn;
  public static $columns;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  function getLanguages() {
    try {
      $languages = array();
      $query = $this->conn->prepare("SELECT id, langcode, langname FROM langs order by langcode");
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $language = new language_dto();
        $language->id = $row['id'];
        $language->langcode = $row['langcode'];
        $language->langname = $row['langname'];
        $languages[] = $language;
      }
      $this->conn->close_conn();
      return $languages;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in language_dao::getLanguages : " . $ex->getMessage());
    }
  }
  
  
  function addLanguage($language){
    try {
      $query = $this->conn->prepare("INSERT INTO langs (langcode, langname) VALUES (?, ?);");
      $query->bindParam(1, $language->langcode);
      $query->bindParam(2, $language->langname);

      $query->execute();
      //$query->setFetchMode(PDO::FETCH_ASSOC);
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in language_dao::addLanguage : " . $ex->getMessage());
    }
  }
  
  function getLanguageById($lang_id){
    try {
      $language= new language_dto();
      $query = $this->conn->prepare("SELECT id, langcode, langname FROM langs WHERE id = ?;");
      $query->bindParam(1, $lang_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){        
        $language->id = $row['id'];
        $language->langcode = $row['langcode'];
        $language->langname = $row['langname'];        
      }
      $this->conn->close_conn();
      return $language;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in language_dao::getLanguageById : " . $ex->getMessage());
    } 
  }
  
  function existsLangCode($langcode){
    try {
      $query = $this->conn->prepare("SELECT COUNT(*) as count FROM langs WHERE langcode = ?;");
      $query->bindParam(1, $langcode);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $count= $row['count'];
      }
      $this->conn->close_conn();
      if ($count == 0) {
        return false;
      }
      else {
        return true;
      }
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in language_dao::existsLangCode : " . $ex->getMessage());
    }
  }
  
  function getLangByLangCode($langcode){
    try {
      $query = $this->conn->prepare("SELECT * FROM langs WHERE langcode = ?;");
      $query->bindParam(1, $langcode);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $id= $row['id'];
        $lc= $row['langcode'];
        $ln= $row['langname'];  
      }
      $this->conn->close_conn();
      if (isset($id)) {
      $lang = language_dto::newLanguage($id, $lc, $ln);
      }
      else {
        $lang = new language_dto;
      }
      
      return $lang;
      
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in language_dao::getLangByLangCode : " . $ex->getMessage());
    }
  }
  
  
  function existsLangName($langname){
    try {
      $query = $this->conn->prepare("SELECT COUNT(*) as count FROM langs WHERE langname = ?;");
      $query->bindParam(1, $langname);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $count= $row['count'];
      }
      $this->conn->close_conn();
      if ($count == 0) {
        return false;
      }
      else {
        return true;
      }
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in language_dao::existsLangName : " . $ex->getMessage());
    }
  }
  
    function getLangByLangName($langname){
    try {
      $query = $this->conn->prepare("SELECT * FROM langs WHERE langname = ?;");
      $query->bindParam(1, $langname);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $id= $row['id'];
        $lc= $row['langcode'];
        $ln= $row['langname'];  
      }
      $this->conn->close_conn();
      if (isset($id)) {
      $lang = language_dto::newLanguage($id, $lc, $ln);
      }
      else {
        $lang = new language_dto;
      }
      
      return $lang;
      
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in language_dao::getLangByLangName : " . $ex->getMessage());
    }
  }
  
  function updateLanguage($language){
    try {
      $query = $this->conn->prepare("UPDATE langs SET langcode=?, langname =? WHERE id = ?;");
      $query->bindParam(1, $language->langcode);
      $query->bindParam(2, $language->langname);
      $query->bindParam(3, $language->id);   
      $query->execute();
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in language_dao::updateLanguage : " . $ex->getMessage());
    }
  }
  
  function getDatatablesLanguages($request) {
    try {
      return json_encode(DatatablesProcessing::simple( $request, $this->conn, "langs", "id", self::$columns ));
    } catch (Exception $ex) {
      throw new Exception("Error in language_dao::getDatatablesLanguages : " . $ex->getMessage());
    }
  }
}
language_dao::$columns = array(
  array( 'db' => 'id', 'dt' => 0 ),
  array( 'db' => 'langcode', 'dt' => 1 ),
  array( 'db' => 'langname', 'dt' => 2 )
);
