<?php
  declare(strict_types = 1);

  require_once(__DIR__ . '/../../classes/session.class.php');
  $session = new Session();

  if (!$session->isLoggedIn()) {
    die(header('Location: ../../pages/index.php'));
  }

  require_once(__DIR__ . '/../../database/connection.db.php');
  require_once(__DIR__ . '/../../classes/ticket.class.php');
  require_once(__DIR__ . '/../../utils/validation.php');

  $db = getDatabaseConnection();

  if (!valid_token($_POST['csrf'])) {
    die(header("Location: ../../pages/create_ticket.php"));
  }

  $tags = explode(',', htmlentities($_POST['chosen_tags']));

  try {
    $id = Ticket::registerTicket($db, $tags, htmlentities($_POST['title']), htmlentities($_POST['text']), "4-low", htmlentities($_POST['category']), htmlentities($_POST['visibility']), $session->getId());
    $session->addMessage('success', 'Ticket successfully created!');
    header("Location: ../../pages/ticket.php?id=" . $id);
  } 
  catch (PDOException $e) {
    $session->addMessage('error', 'Failed to create ticket due to foreign key constraint violation.');
    header("Location: ../../pages/create_ticket.php");
  }
?>