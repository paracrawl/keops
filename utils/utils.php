<?php
/**
 * Checks that each one of the strings in the array passed as parameter
 * is in the $_POST request 
 * 
 * @param array $names Names of the parameters 
 * @return array Array containing the missing parameters (empty array if none is missing)
 */
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

/**
 *  Formats a date
 * 
 * @param string $date Date (in the format returned by the DB)
 * @param string $format Output date format (default: d.m.Y)
 * @return type Formatted date
 */
function getFormattedDate($date, $format="d.m.Y") {
  return isset($date) && $date !== '' ? date($format, strtotime($date)) : "";
}

/**
 * Searches a given pair of field->value in a given array
 * 
 * @param string $needle Value 
 * @param string $needle_field Field
 * @param array $haystack Array to be searched in
 * @return boolean True if found, otherwise false
 */
function in_array_field($needle, $needle_field, $haystack) {
  foreach ($haystack as $item) {
    if (isset($item->$needle_field) && $item->$needle_field === $needle) {
      return true;
    }
  }

  return false;
}

/**
 * Adds an underline to the shortcut letter of each evaluation label
 * 
 * @param string $label Evaluation label
 * @param string  $value Evaluation shortcut
 * @return string Formatted string
 */
function underline($label, $value){
  $char = substr($value, 0, 1);
  $regex="/(^".$char."|\b".strtolower($char).")/";    
  preg_match("/(^".$char."|\b".strtolower($char).")/", $label, $matching_char);
  $formatted_string = preg_replace($regex, "<u>".$matching_char[0]."</u>", $label, 1);
  return $formatted_string;
}


// TODO: Docs

function standarize($sentences) {
  $standard_scores = array();
  $mean = mean($sentences);
  $deviation = standard_deviation($sentences);
  
  foreach ($sentences as $sentence) {
      $a = intval($sentence->evaluation) - $mean;
      $a = $a / $deviation;
      $standard_scores[$sentence->sentence_id] = $a;
  }

  return $standard_scores;
}

function standard_deviation($sentences) {
  $deviation = 1;
  $sum = 0;
  $mean = mean($sentences);
  $count = 0;

  foreach ($sentences as $sentence) {
      $count++;
      $sum += pow(intval($sentence->evaluation) - $mean, 2);
  }

  $deviation = $sum / $count;
  $deviation = sqrt($deviation);

  return $deviation;
}

function mean($sentences) {
  $count = 0;
  $mean = 0;

  foreach ($sentences as $sentence) {
      $count++;
      $mean += intval($sentence->evaluation);
  }

  return $mean / $count;
}