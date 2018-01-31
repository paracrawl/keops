<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/language_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class language_dao {
  private $conn;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  function getLanguages() {
    try {
      $languages = array();
      $query = $this->conn->prepare("SELECT id, langcode, langname FROM langs order by langname");
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $language = new language_dto();
        $language->id = $row['id'];
        $language->langcode = $row['langcode'];
        $language->langname = $row['langname'];
        $languages[] = $language;
      }
      return $languages;
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getUsers : " . $ex->getMessage());
    }
  }
  
  function getDatatablesLanguages($request) {
    try {
      $columns = array(
          array( 'db' => 'id', 'dt' => 0 ),
          array( 'db' => 'langcode', 'dt' => 1 ),
          array( 'db' => 'langname', 'dt' => 2 )
      );

      return json_encode(DatatablesProcessing::simple( $request, $this->conn, "langs", "id", $columns ));
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getDatatablesLanguages : " . $ex->getMessage());
    }
  }
}
