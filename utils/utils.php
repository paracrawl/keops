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

/**
 * Given a set of sentences, calculates the z-value
 * of their evaluation. The sentences must belong to a
 * task that uses numerical evaluations (like Adequacy)
 * 
 * @param array $sentences Array of \sentence_task_dto
 * @return array Array of z-values
 */
function standarize($sentences) {
  $standard_scores = array();
  $mean = mean_sentences($sentences);
  $deviation = standard_deviation_sentences($sentences);
  
  foreach ($sentences as $sentence) {
      $a = intval($sentence->evaluation) - $mean;
      $a = $a / $deviation;
      $standard_scores[$sentence->sentence_id] = $a;
  }

  return $standard_scores;
}


/**
 * Given a set of sentences, calculates the standard
 * deviation of their evaluation scores. Sentences must belong
 * to a task that uses numerical evaluation (like Adequacy)
 * 
 * @param array $sentences Array of \sentence_task_dto
 * @return float Standard deviation
 */
function standard_deviation_sentences($sentences) {
  $deviation = 1;
  $sum = 0;
  $mean = mean_sentences($sentences);
  $count = 0;

  foreach ($sentences as $sentence) {
      $count++;
      $sum += pow(intval($sentence->evaluation) - $mean, 2);
  }

  $deviation = $sum / $count;
  $deviation = sqrt($deviation);

  return $deviation;
}


/**
 * Given a set of sentences, calculates the mean
 * of their evaluation. The sentences must belong to a
 * task that uses numeric evaluations (like Adequacy)
 * 
 * @param array $sentences Array of \sentence_task_dto
 * @return float Mean of the scores
 */

function mean_sentences($sentences) {
  $count = 0;
  $mean = 0;

  foreach ($sentences as $sentence) {
      $count++;
      $mean += intval($sentence->evaluation);
  }

  return $mean / $count;
}

/**
 * Given a set of numbers, calculates their mean.
 * 
 * @param array $values Array of numbers
 * @return float Mean
 */
function mean($values) {
  if (count($values) == 0) return 0;

  $mean = 0;
  foreach ($values as $value) $mean += $value;
  return ($mean/count($values));
}

/**
 * Given a set of numbers, calculates their variance.
 * 
 * @param array $values Array of numbers
 * @return float Variance
 */
function variance($values) {
  $mean = mean($values);
  $variance = 0;
  foreach ($values as $value) {
      $variance += pow(($value - $mean), 2);
  }
  return ($variance / (count($values) - 1));
}

/**
 * Generate a random string, using a cryptographically secure 
 * pseudorandom number generator (random_int)
 *
 * This function uses type hints now (PHP 7+ only), but it was originally
 * written for PHP 5 as well.
 * 
 * For PHP 7, random_int is a PHP core function
 * For PHP 5.x, depends on https://github.com/paragonie/random_compat
 * 
 * https://stackoverflow.com/questions/4356289/php-random-string-generator/31107425#31107425
 * 
 * @param int $length      How many characters do we want?
 * @param string $keyspace A string of all possible characters
 *                         to select from
 * @return string
 */
function random_str(
  int $length = 64,
  string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
): string {
  if ($length < 1) {
      throw new \RangeException("Length must be a positive integer");
  }
  $pieces = [];
  $max = mb_strlen($keyspace, '8bit') - 1;
  for ($i = 0; $i < $length; ++$i) {
      $pieces []= $keyspace[random_int(0, $max)];
  }
  return implode('', $pieces);
}