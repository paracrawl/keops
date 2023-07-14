<?php
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/resources/config.php");
  require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/dao/task_dao.php");
  
  $PAGETYPE = "user";
  require_once(RESOURCES_PATH . "/session.php");
  
  $task_id = filter_input(INPUT_GET, "task_id");
  $task_dao = new task_dao();
  $task = $task_dao->getTaskById($task_id);

  switch ($task->mode) {
    case 'VAL':
      header("Location: /sentences/evaluate/val.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'ADE':
      header("Location: /sentences/evaluate/ade.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'FLU':
      header("Location: /sentences/evaluate/flu.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'RAN':
      header("Location: /sentences/evaluate/ran.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'PAR':
      header("Location: /sentences/evaluate/par.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'VAL_MAC':
      header("Location: /sentences/evaluate/val_mac.php?".$_SERVER['QUERY_STRING']);
      break;
    case 'MONO':
      header("Location: /sentences/evaluate/mono.php?".$_SERVER['QUERY_STRING']);
      break;

  }
?>