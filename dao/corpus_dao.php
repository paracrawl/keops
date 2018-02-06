<?php

require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/corpus_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/utils/datatables_helper.class.php' );

class corpus_dao {
  private $conn;
  public static $columns;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  function getCorpora() {
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
      $this->conn->close_conn();
      throw new Exception("Error in user_dao::getUsers : " . $ex->getMessage());
    }
  }
  
  function getDatatablesCorpora($request) {
    try {
      return json_encode(DatatablesProcessing::simple( $request, $this->conn,
              "corpora as c left join langs as l1 on c.source_lang = l1.id left join langs as l2 on c.target_lang = l2.id",
              "c.id",
              self::$columns ));
    } catch (Exception $ex) {
      throw new Exception("Error in user_dao::getDatatablesLanguages : " . $ex->getMessage());
    }
  }
}
corpus_dao::$columns = array(
    array( 'db' => 'c.id', 'alias' => 'id', 'dt' => 0 ),
    array( 'db' => 'c.name', 'alias' => 'name', 'dt' => 1 ),
    array( 'db' => 'l1.langcode', 'alias' => 'source_lang', 'dt' => 2 ),
    array( 'db' => 'l2.langcode', 'alias' => 'target_lang', 'dt' => 3 ),
    array( 'db' => 'c.lines', 'alias' => 'lines', 'dt' => 4 ),
    array( 'db' => 'c.creation_date', 'alias' => 'creation_date', 'dt' => 5,
        'formatter' => function( $d, $row ) {
            return date( 'd/m/Y', strtotime($d));
        } ),
    array( 'db' => 'c.active', 'alias' => 'active', 'dt' => 6 ),
    array( 'db' => 'l1.langname', 'alias' => 'nsource_lang', 'dt' => 7 ),
    array( 'db' => 'l2.langname', 'alias' => 'ntarget_lang', 'dt' => 8 )
);
