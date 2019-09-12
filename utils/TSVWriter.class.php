<?php
class TSVWriter {
  function __construct($delimiter = null) {
    $this->delimiter = isset($delimiter) ? $delimiter : chr(9);
  }

  function write($rows, $output = 'php://output') {
    $output = fopen($output, 'w');
    fputs($output, $bom = ( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
    foreach ($rows as $row) {
      for ($i = 0; $i < count($row); $i++) {
        $row[$i] = preg_replace("/\t+/", ' ', $row[$i]);
      }

      fputs($output, implode($row, $this->delimiter)."\n");
    }
    fclose($output);
  }
}
?>