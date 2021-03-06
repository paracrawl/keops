<?php
/**
 * Methods to interact with UserLang objects and the DB
 */
require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_langs_dto.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/user_dto.php");

class user_langs_dao {
  private $conn;
  
  public function __construct(){
      $this->conn = new keopsdb();
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  
  /**
   * Inserts into the DB the languages associated to an user
   * 
   * @param array $user_langs Array of  UserLangs objects
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  public function addLanguages($user_langs){
    try {
      $insert_values = array();
      foreach ($user_langs as $user_lang) {
        $question_marks[] = '(?, ?)';
        //error_log(print_r($question_marks));        
       $insert_values = array_merge($insert_values, [$user_lang->user_id, $user_lang->lang_id]);
      }
//      error_log(print_r($insert_values));
      $query = $this->conn->prepare("INSERT INTO user_langs (user_id, lang_id) VALUES " . implode(',', $question_marks));
      $query->execute($insert_values);
//      error_log($query);
      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_langs_dao::addLanguages : " . $ex->getMessage());
    }
    return false;
  }
  
  /**
   * Removes from the DB the languages for a given user
   * 
   * @param int $user_id User ID
   * @return boolean True if succeeded, otherwise false
   * @throws Exception
   */
  public function removeUserLangs($user_id){
    try {
      $query = $this->conn->prepare("DELETE FROM user_langs WHERE user_id = ?;");
      $query->bindParam(1, $user_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);

      $this->conn->close_conn();
      return true;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_langs_dao::removeUserLangs : " . $ex->getMessage());
    }
  }
  
  /**
   * Removes from the DB all the languages for a given user, and then inserts the new ones
   * 
   * @param array $user_langs Array of UserLangs objects, containing the new ones
   * @return type True if succeeded, otherwise false
   */
  public function updateUserLangs($user_langs){
    return ($this->removeUserLangs($user_langs[0]->user_id) && $this->addLanguages($user_langs));
  }

  /**
   * Retrieves from the DB the languages associated to a given user
   * 
   * @param int $user_id User ID
   * @return array Array of UserLangs objects
   * @throws Exception
   */
  public function getUserLangs($user_id){
    try {
      $user_langs = array();
      $query = $this->conn->prepare("SELECT * FROM user_langs WHERE user_id = ?;");
      $query->bindParam(1, $user_id);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user_lang = new user_langs_dto();
        $user_lang->id = $row['id'];
        $user_lang->user_id = $row['user_id'];
        $user_lang->lang_id = $row['lang_id'];
        array_push($user_langs, $user_lang);
      }
      $this->conn->close_conn();
      return $user_langs;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_langs_dao::getUserLangs : " . $ex->getMessage());
    }
  }
  
  /**
   * Retrieves from the DB a list of users that have two given languages associated
   * 
   * @param int $lang1 Language ID for the first language
   * @param int $lang2 Language ID for the second language
   * @return array List of users IDs
   * @throws Exception
   */
  public function getUserIdsByLangPair($lang1, $lang2){   
    try {
      $user_ids = array();
      $query = $this->conn->prepare("select  ul1.user_id from  user_langs ul1 inner join user_langs ul2 on ul1.user_id = ul2.user_id where ul1.lang_id=? and ul2.lang_id=?;");
      $query->bindParam(1, $lang1);
      $query->bindParam(2, $lang2);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user_id = $row['user_id'];
        array_push($user_ids, $user_id);
      }
      $this->conn->close_conn();
      return $user_ids;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_langs_dao::getUserIdsByLangPair : " . $ex->getMessage());
    }
  }


  /**
   * Retrieves from the DB a list of users that have two given languages associated
   * 
   * @param int $lang1 Language Code for the first language
   * @param int $lang2 Language Code for the second language
   * @return array List of users IDs
   * @throws Exception
   */
  public function getUserIdsByLangcodePair($lang1, $lang2){   
    try {
      $user_ids = array();
      $query = $this->conn->prepare("
        select  ul1.user_id from  user_langs ul1 
        inner join user_langs ul2 on ul1.user_id = ul2.user_id
        join langs as l1 on (ul1.lang_id = l1.id)
        join langs as l2 on (ul2.lang_id = l2.id)
        where l1.langcode = ? and l2.langcode = ? ;
      ");
      $query->bindParam(1, $lang1);
      $query->bindParam(2, $lang2);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      while($row = $query->fetch()){
        $user_id = $row['user_id'];
        array_push($user_ids, $user_id);
      }
      $this->conn->close_conn();
      return $user_ids;
    } catch (Exception $ex) {
      $this->conn->close_conn();
      throw new Exception("Error in user_langs_dao::getUserIdsByLangPair : " . $ex->getMessage());
    }
  }
}