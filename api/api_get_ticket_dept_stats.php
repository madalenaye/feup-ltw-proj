<?php
  declare(strict_type = 1);

  require_once(__DIR__ . '/../classes/session.class.php');
  $session = new Session();

  require_once(__DIR__ . '/../database/connection.db.php');
  require_once(__DIR__ . '/../classes/ticket.class.php');
  $db = getDatabaseConnection();

  $deptStats = Ticket::getDeptStats($db);

  echo json_encode($deptStats);

?>