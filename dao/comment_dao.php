<?php
/**
 * Methods to work with Comment objects in the DB
 */
require_once(DB_CONNECTION);
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/dto/comment_dto.php");

class comment_dao {
    private $conn;
    public static $columns;

    public function __construct(){
        $this->conn = new keopsdb();
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Retrieves a comment given its name
     * and the pair it belongs to
     * 
     * @param int $pair ID of the pair of sentences the comment belongs to
     * @return \task_dto Comment object or FALSE if not accessible
     */
    function getCommentById($pair, $name) {
        try {
            $comment = new comment_dto();
            
            $query = $this->conn->prepare("SELECT * FROM comments WHERE pair = ? and name = ? ;");
            $query->bindParam(1, $pair);
            $query->bindParam(2, $name);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);

            $count = 0;
            while($row = $query->fetch()){
                $count++;
                $comment->pair = $row['pair'];
                $comment->name = $row['name'];
                $comment->value = $row['value'];
            }

            $this->conn->close_conn();
            return ($count > 0) ? $comment : false;
        } catch (Exception $ex) {
            $this->conn->close_conn();
            return false;
        }

        return false;
    }

    /**
     * Retrieves a list of comments belonging to sentences of a given task
     * 
     * @param int $pair_id ID of the pair of sentences to retreive comments from
     * @return array List of Comment objects
     */
    function getCommentsByPair($pair_id) {
        try {            
            $query = $this->conn->prepare("
                select st.id as pair, c.name, c.value from sentences_tasks as st 
                join comments as c on (c.pair = st.id)
                where st.id = ?;
            ");
            $query->bindParam(1, $pair_id);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);

            $comments = array();
            while($row = $query->fetch()){
                $comment = new comment_dto();
                $comment->pair = $row['pair'];
                $comment->name = $row['name'];
                $comment->value = $row['value'];

                $comments[] = $comment;
            }

            $this->conn->close_conn();
            return $comments;
        } catch (Exception $ex) {
            $this->conn->close_conn();
            return false;
        }

        return false;
    }

     /**
     * Inserts a new comment in the DB
     * 
     * @param object $comment_dto Comment object
     * @return boolean True if succeeded, otherwise false
     * @throws Exception
     */
    function insertComment($comment_dto) {
        try {
            $query = $this->conn->prepare("INSERT INTO comments (pair, name, value) VALUES (?, ?, ?);");
            $query->bindParam(1, $comment_dto->pair);
            $query->bindParam(2, $comment_dto->name);
            $query->bindParam(3, $comment_dto->value);      
            $query->execute();
            $this->conn->close_conn();
            return true;
        } catch (Exception $ex) {
            $this->conn->close_conn();
            throw new Exception("Error in comment_dao::insertComment : " . $ex->getMessage());
        }
        
        return false;
    }

    /**
     * Updates a comment in the DB
     * 
     * @param object $comment_dto Comment object
     * @return boolean True if succeeded, otherwise false
     * @throws Exception
     */
    function updateComment($comment_dto) {
        try {
            $query = $this->conn->prepare("update comments set value = ? where pair = ? and name = ?;");
            $query->bindParam(1, $comment_dto->value);      
            $query->bindParam(2, $comment_dto->pair);
            $query->bindParam(3, $comment_dto->name);
            $query->execute();
            $this->conn->close_conn();
            return true;
        } catch (Exception $ex) {
            $this->conn->close_conn();
            throw new Exception("Error in comment_dao::updateComment : " . $ex->getMessage());
        }

        return false;
    }

    /**
     * Inserts a comment but updates it if it already exists
     * 
     * @param \comment_dto $comment_dto Comment object
     * 
     * @return boolean True if succeeded, False otherwise
     */
    function upsertComment($comment_dto) {
        try {
            $query = $this->conn->prepare("
                INSERT INTO comments (pair, name, value) VALUES (?, ?, ?)
                on conflict (pair, name) do update set value = ?;
            ");
            $query->bindParam(1, $comment_dto->pair);
            $query->bindParam(2, $comment_dto->name);
            $query->bindParam(3, $comment_dto->value);
            $query->bindParam(4, $comment_dto->value); 
            $query->execute();
            $this->conn->close_conn();
            return true;
        } catch (Exception $ex) {
            $this->conn->close_conn();
            return false;
        }

        return false;
    }


    /**
     * Deletes all comments of a pair of sentences
     * 
     * @param int $pair $ID of the pair to clear
     * 
     * @return boolean True if succeeded, False otherwise
     */
    function clearComments($pair) {
        try {
            $query = $this->conn->prepare("
                DELETE FROM comments WHERE pair = ?;
            ");

            $query->bindParam(1, $pair);
            $query->execute();
        } catch (Exception $ex) {
            $this->conn->close_conn();
            return false;
        }

        return false;
    }
}