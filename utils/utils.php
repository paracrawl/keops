<?php

function checkPostParameters($names){
  $failed = [];
  foreach ($names as $name){
    if (!(isset($_POST[$name]) && $_POST[$name]!=null && $_POST[$name]!=null)){
      array_push($failed, $name);
      error_log("Missing parameter: " . $name);
    }
  }
  return $failed;
}

function getFormattedDate($date, $format="Y-m-d") {
  return isset($date) && $date !== '' ? date($format, strtotime($date)) : "";
}

function in_array_field($needle, $needle_field, $haystack) {
  foreach ($haystack as $item) {
    if (isset($item->$needle_field) && $item->$needle_field === $needle) {
      return true;
    }
  }

  return false;
}
