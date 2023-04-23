<?php
    declare(strict_types=1);

    require_once(__DIR__ . '/../classes/session.class.php');
    $session = new Session();

  if ($_FILES['imageToUpload']['tmp_name'][0] == "") {
    $session->addMessage('warning', 'Choose an image first!');
    die(header("Location: ../pages/profile.edit.php"));
  }

  $fileName = "../images/users/user" .  $session->getId() . ".png";
  move_uploaded_file($_FILES['imageToUpload']['tmp_name'], $fileName);
 
  $session->setPhoto($fileName);
 
  $session->addMessage('success', 'Profile photo sucessfully updated');
  header("Location: ../pages/profile.php");
?>
