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
    private function getCount($primary, $tables, $wheresql = "") {
        $query = $this->conn->prepare("
        SELECT count(distinct $primary) as c FROM $tables "
            . (($wheresql != "") ? "WHERE $wheresql " : "")
        . ";");
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
    function process($columns, $tables, $dt_params, $conditions = "", $group = "") {
        // We transform the array of columns to a string in the form "column as alias, column as alias, ..."
        $columnssql = array_reduce($columns, function($a, $b) {
            $a = $a . (($a != "") ? ", " : "") . $b[0] . (isset($b[1]) ? " as $b[1]" : "");
            return $a;
        });

        // We get the search filters ready creating the corresponding SQL
        $filter = "";
        if (isset($dt_params["search"]) && $dt_params["search"]["value"] != "") {
            foreach ($dt_params["columns"] as $column) {
                if ($column["searchable"] == "true") {
                    $filter = $filter . (($filter != "") ? " or " : "") . $columns[$column['data']][0] . "::text ILIKE '%" . $dt_params['search']['value'] . "%'";
                }
            }
            $filter = "($filter)";
        }

        // And we create a WHERE close combining the given conditions and the filter
        $conditionssql = ($conditions != "") ? $conditions : "";
        $filtersql = ($filter != "") ? (($conditionssql == "") ? $filter : (" AND " . $filter)) : "";
        $wheresql = $conditionssql . $filtersql;
        
        // SQL cluases for paging and grouping
        $limitsql = ((isset($dt_params["start"]) ? "OFFSET {$dt_params['start']} " : "") . ((isset($dt_params["length"]) ? "LIMIT {$dt_params['length']}" : "")));
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
        $filtered_count = $this->getCount($primary, $tables, $wheresql);
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