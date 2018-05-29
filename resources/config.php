<?php

defined("RESOURCES_PATH")
  or define("RESOURCES_PATH", realpath(dirname(__FILE__)));

defined("TEMPLATES_PATH")
  or define("TEMPLATES_PATH", RESOURCES_PATH . '/templates');


defined("DB_CONNECTION")
  or define("DB_CONNECTION", RESOURCES_PATH . '/db/keopsdb.class.php');

