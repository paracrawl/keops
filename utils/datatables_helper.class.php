<?php
class DatatablesProcessing {
    private $conn;

    function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Counts the records in a query with distinct primary key
     * 
     * @param   string $primary   Primary key
     * @param   string $tables    SQL statement of FROM clause
     * @param   string $wheresql  SQL statement of WHERE clause 
     * @return  int    Value of count()
     */
    private function getCount($primary, $tables, $wheresql = "", $conditionsValues = array(), $filter_count = 0, $search_term = "") {
        $query = $this->conn->prepare("
        SELECT count(distinct $primary) as c FROM $tables "
            . (($wheresql != "") ? "WHERE $wheresql" : "")
        . ";");

        if ($wheresql != "") {
            for ($i = 0; $i < count($conditionsValues); $i++) {
                $query->bindParam($i + 1, $conditionsValues[$i]);
            }
    
            for ($j = $i; $j < ($i + $filter_count); $j++) {
                $query->bindParam($j + 1, $filter_term);
            }
        }

        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $count = 0;
        while($row = $query->fetch()){
            $count = $row['c'];
        }
        return intval($count);
    }

    /**
     * Perform the SQL queries needed for an server-side processing request.
	 *
	 *  @param  array   $columns    An array containing pairs (column name, column alias)
	 *  @param  string  $tables     The SQL statement of the FROM clause
     *  @param  array   $dt_params  Parameters passed by Datatables
	 *  @param  string  $conditions The SQL statement of the WHERE clause
	 *  @param  array   $group      The SQL statement of the GROUP BY clause
	 *  @return array   Server-side processing response array
     */
    function process($columns, $tables, $dt_params, $group = "", $conditionsStatement = "", $conditionsValues = array()) {
        // We transform the array of columns to a string in the form "column as alias, column as alias, ..."
        $columnssql = array_reduce($columns, function($a, $b) {
            $a = $a . (($a != "") ? ", " : "") . $b[0] . (isset($b[1]) ? " as $b[1]" : "");
            return $a;
        });

        // We get the search filters ready creating the corresponding SQL
        $filter = "";
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

        $filter_term = '%' . $dt_params["search"]["value"] . '%';

        // And we create a WHERE clause combining the given conditions and the filter
        $wheresql = $conditionsStatement . (($filter != "") ? (($conditionsStatement != "") ? "and $filter" : "$filter") : "");
        
        // SQL cluases for paging and grouping
        $limitsql = ((isset($dt_params["start"]) ? "OFFSET ? " : "") . ((isset($dt_params["length"]) ? "LIMIT ?" : "")));
        $groupsql = (($group != "") ? "GROUP BY " . $group : "");

        // Order SQL clause
        $ordersql = (isset($dt_params['order'][0]['column']) ? ("ORDER BY " . $columns[$dt_params['order'][0]['column']][0] . " " . $dt_params['order'][0]['dir']) : "");

        $query = $this->conn->prepare("
            SELECT $columnssql FROM $tables "
            . (($wheresql != "") ? "WHERE $wheresql " : "")
            . (($groupsql != "") ? "$groupsql " : "")
            . (($ordersql != "") ? "$ordersql ": "")
            . (($limitsql != "") ? "$limitsql " : "")
        . ";");

        // We start binding the values
        if ($wheresql != "") {
            for ($i = 0; $i < count($conditionsValues); $i++) {
                $query->bindParam($i + 1, $conditionsValues[$i]);
            }

            for ($j = $i; $j < ($i + $filter_count); $j++) {
                $query->bindParam($j + 1, $filter_term);
            }
        } else {
            $j = 0;
        }

        if (isset($dt_params["start"])) $j++; $query->bindParam($j, $dt_params["start"]);
        if (isset($dt_params["length"])) $j++; $query->bindParam($j, $dt_params["length"]);

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
        $total_count = $this->getCount($primary, $tables);

        return array(
			"draw"            => isset ($request['draw']) ? intval($request['draw']) :0,
			"recordsTotal"    => $total_count,
			"recordsFiltered" => $filtered_count,
			"data"            => $data
		);
    }
}
?>