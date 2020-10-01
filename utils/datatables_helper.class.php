<?php
class DatatablesProcessing {
    private $conn;

    function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Counts the records in a query with distinct primary key
     * 
     * @param   string  $primary             Primary key
     * @param   string  $tables              SQL statement of FROM clause
     * @param   string  $wheresql            SQL statement of WHERE clause 
     * @param   string  $conditionsValues    Array of values, in order, to replace the placeholders
     * @param   int     $filter_count         Amount of filter placeholders
     * @param   int     $filter_term          The search term
     * @return  int     Value of count()
     */
    private function getCount(&$primary, &$tables, &$wheresql = "", &$conditionsValues = array(), &$filter_count = 0, &$filter_term = "") {
        $query = $this->conn->prepare("
        SELECT count(distinct $primary) as c FROM $tables "
            . (($wheresql != "") ? "WHERE $wheresql" : "")
        . ";");

        for ($i = 0; isset($conditionsValues) && $i < count($conditionsValues); $i++) {
            $query->bindParam($i + 1, $conditionsValues[$i]);
        }

        for ($j = $i; $j < ($i + $filter_count); $j++) {
            $query->bindParam($j + 1, $filter_term);
        }

        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);

        $count = 0;
        if($row = $query->fetch()) $count = $row['c'];
        
        return intval($count);
    }

    /**
     * Perform the SQL queries needed for an server-side processing request.
	 *
	 *  @param  array   $columns                An array containing pairs (column name, column alias)
	 *  @param  string  $tables                 The SQL statement of the FROM clause
     *  @param  array   $dt_params              Parameters passed by Datatables
	 *  @param  array   $group                  The SQL statement of the GROUP BY clause
	 *  @param  string  $conditionsStatement    The SQL statement of the WHERE clause, with placeholders
     *  @param  string  $conditionsValues       Array of values, in order, to replace the placeholders
     * 
	 *  @return array   Server-side processing response array
     */
    function process($columns, $tables, $dt_params, $group = "", $conditionsStatement = "", $conditionsValues = array()) {
        // We transform the array of columns to a string in the form "column as alias, column as alias, ..."
        $columnssql = rtrim(array_reduce($columns, function($a, $b) {
            $a = $a . $b[0] . (isset($b[1]) ? " as $b[1]" : "") . ", ";
            return $a;
        }), ", ");

        // We get the search filters ready creating the corresponding SQL
        $filter = "";
        $filter_term = '%' . $dt_params["search"]["value"] . '%';
        $filter_count = 0;
        if (isset($dt_params["search"]) && $dt_params["search"]["value"] != "") {
            foreach ($dt_params["columns"] as $column) {
                if ($column["searchable"] == "true") {
                    $filter = $filter . (($filter != "") ? " or " : "") . $columns[$column['data']][0] . "::text ILIKE ?";
                    $filter_count++;
                }
            }
            $filter = "($filter)";
        }

        // And we create a WHERE clause combining the given conditions and the filter
        $wheresql = $conditionsStatement . (($filter_count != 0) ? (($conditionsStatement != "") ? "and $filter" : $filter) : "");
        
        // SQL cluases for paging and grouping
        $limitsql = ((isset($dt_params["start"]) ? "OFFSET ? " : "") . ((isset($dt_params["length"]) ? "LIMIT ?" : "")));
        $groupsql = (($group != "") ? "GROUP BY " . $group : "");

        // Order SQL clause
        $ordersql = "";
        if (isset($dt_params['order'][0]['column'])) {
            $ordersql = "ORDER BY lower_if_text(" . $columns[$dt_params['order'][0]['column']][0] . ") " . $dt_params['order'][0]['dir'];
        }

        $query = $this->conn->prepare("
            SELECT $columnssql FROM $tables "
            . (($wheresql != "") ? "WHERE $wheresql " : "")
            . (($groupsql != "") ? "$groupsql " : "")
            . (($ordersql != "") ? "$ordersql ": "")
            . (($limitsql != "") ? "$limitsql " : "")
        . ";");
        
        // We start binding the values of the conditions
        for ($i = 0; isset($conditionsValues) && $i < count($conditionsValues); $i++) {
            $query->bindParam($i + 1, $conditionsValues[$i]);
        }

        // And then the values of the user filter
        for ($j = $i; $j < ($i + $filter_count); $j++) {
            $query->bindParam($j + 1, $filter_term);
        }

        // We bind offset and limit
        if (isset($dt_params["start"])) $j++; $query->bindParam($j, $dt_params["start"]);
        if (isset($dt_params["length"])) $j++; $query->bindParam($j, $dt_params["length"]);

        // And we run the query
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);

        $data = array();
        while($row = $query->fetch()){
          $values = array();
          foreach ($row as $column) {
              $values[] = $column;
          }
          $data[] = $values;
        }

        // We consider the first given column as primary
        $primary = $columns[0][0];
        $filtered_count = $this->getCount($primary, $tables, $wheresql, $conditionsValues, $filter_count, $filter_term);
        $total_count = $this->getCount($primary, $tables, $conditionsStatement, $conditionsValues);

        return array(
	"draw"            => isset ($dt_params['draw']) ? intval($dt_params['draw']) :0,
	"recordsTotal"    => $total_count,
	"recordsFiltered" => $filtered_count,
	"data"            => $data
	);
    }
}
?>