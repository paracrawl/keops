<?php
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
  
  $PAGETYPE = "user";
  require_once(RESOURCES_PATH . "/session.php");
  
  $task_id = filter_input(INPUT_GET, "id");
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);

  switch ($task->mode) {
    case 'VAL':
      header("Location: /tasks/recap/val.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'ADE':
      header("Location: /tasks/recap/ade.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'FLU':
      header("Location: /tasks/recap/flu.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'RAN':
      header("Location: /tasks/recap/ran.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'PAR':
      header("Location: /tasks/recap/par.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'VAL_MAC':
      header("Location: /tasks/recap/val_mac.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'MONO':
      header("Location: /tasks/recap/mono.php?".$_SERVER['QUERY_STRING']);
      break;      
  }
?>

