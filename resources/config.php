<?php

defined("RESOURCES_PATH")
  or define("RESOURCES_PATH", realpath(dirname(__FILE__)));

defined("TEMPLATES_PATH")
  or define("TEMPLATES_PATH", RESOURCES_PATH . '/templates');


defined("DB_CONNECTION")
  or define("DB_CONNECTION", RESOURCES_PATH . '/db/keopsdb.class.php');

// Used by DAO and UI to know how many search results to show in /sentences/search.php
defined("SENTENCES_SEARCH_MAX")
  or define("SENTENCES_SEARCH_MAX", 10);

