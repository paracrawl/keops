<?php

class task_stats_dto {
  public $total;
  public $array_type;
  
  public function __construct(){
    $this->total = 0;
    $this->array_type = array();
  }
}