<?php
  declare(strict_types = 1);

  require_once(__DIR__ . '/../../classes/session.class.php');
  require_once(__DIR__ . '/../../utils/validation.php');
  $session = new Session();

  if (!$session->isLoggedIn()) {
    die(header('Location: ../../pages/index.php'));
  }

  if (!valid_token($_POST['csrf'])) {
    die(header("Location: ../../pages/edit_profile.php"));
  }

  if ($_FILES['imageToUpload']['tmp_name'][0] == "" || !getimagesize($_FILES["imageToUpload"]["tmp_name"])) {
    $session->addMessage('warning', 'Choose an image first!');
    die(header("Location: ../../pages/edit_profile.php"));
  }

  $fileName = "../../images/users/user" . $session->getId() . ".png";
  move_uploaded_file($_FILES['imageToUpload']['tmp_name'], $fileName);

  $session->setPhoto($fileName);
  $session->addMessage('success', 'Profile photo sucessfully updated');
  
  header("Location: ../../pages/edit_profile.php");
?>