<?php

require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/resources/config.php");
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");

$PAGETYPE = "user";
require_once(RESOURCES_PATH . "/session.php");

$task_dao = new task_dao();
echo $task_dao->getDatatablesUserTasks($_GET, $USER->id);
