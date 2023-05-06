<?php
  declare(strict_type = 1);
  require_once(__DIR__ . '/../classes/user.class.php');
?>

<?php function drawProfile(User $user) { ?>
  <div class="user-profile">
    <div class="user-reputation vert-flex">
      <span id="reputation" class="title center">Reputation</span>
      <span class="reputation-value circle-border gradient center"> <?= $user->reputation ?>% </span>
    </div>
    <div class="user-details center">
      <span id="about" class="title">About me</span>
      <span class="field round-border center"> Name </span>
      <span class="info round-border center"> <?= $user->name ?> </span>
      <span class="field round-border center"> Username </span>
      <span class="info round-border center"> <?= $user->username ?> </span>
      <span class="field round-border center"> Email </span>
      <span class="info round-border center">  <?= $user->email ?> </span>
      <span class="field round-border center"> Role </span>
      <span class="info round-border center"> <?= $user->type ?> </span>
    </div>
    <div class="profile-picture round-wrap vert-flex center">
      <img src=<?= $user->getPhoto() ?> alt="user-profile" class="gradient circle-border">
      <span> <?= $user->username ?> </span>
      <div class="button-wrap gradient round-border">
      <a href="../pages/edit_profile.php"><button>Edit profile</button></a>
    </div>
    </div>
  </div>
<?php } ?>

<?php function drawEditUserForm(User $user) { ?>
  <div class="edit-profile center">
    <div class="edit-fields">
      <h2 class="auth-text">Edit profile</h2>
      </h2>
      <form action="../actions/user_actions/action_edit_profile.php" method="post" class="authentication-form">
        <div class="input-box round-border">
          <input type="text" name="name" required="required" placeholder="Name">
          <img src="../images/icons/user.png" class="icon" alt="user">
        </div>
        <div class="input-box round-border">
          <input type="username" name="username" required="required" placeholder="Username">
          <img src="../images/icons/username.png" class="icon" alt="username">
        </div>
        <div class="input-box round-border">
          <input type="email" name="email" required="required" placeholder="Email">
          <img src="../images/icons/email.png" class="icon" alt="email">
        </div>
        <div class="input-box round-border">
          <input type="password" name="old-password" required="required" placeholder="Old password">
          <img src="../images/icons/password.png" class="icon" alt="password">
        </div>
        <div class="input-box round-border">
          <input type="password" name="new-password" required="required" placeholder="New password">
          <img src="../images/icons/password.png" class="icon" alt="password">
        </div>
        <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
        <div class="button-wrap gradient round-border auth-button"> <button type="submit">Save</button> </div>       
      </form>
    </div>
    <div class="profile-picture center vert-flex">
      <form action="../actions/user_actions/action_upload_image.php" method="post" class="upload-form round-wrap center vert-flex" enctype="multipart/form-data">
        <img src=<?= $user->getPhoto() ?> alt="user-profile" id="user-image-preview" class="circle-border">
        <input type="file" id="user-image" name="imageToUpload">
        <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
        <div class="button-wrap gradient round-border auth-button" id="upload"> <button type="submit">Upload photo</button> </div>
      </form>
    </div>
  </div>
<?php } ?>

<?php function drawUsers($users) { ?>
  <div class="search-bar">
    <div class="search-box">
      <input id="search-user" type="text" placeholder="search">
      <img src="../images/icons/search.png">
    </div>
    <select name="" class="filter-select" id="filter-user">
      <option value="users"> All users </option>
      <option value="client"> Clients </option>
      <option value="agent"> Agents </option>
      <option value="admin"> Admins </option>
    </select>
    <div class="order-condition">
      <span> Order by </span>
      <select name="" class="order-select" id="order-user">
        <option value="name"> Name </option>
        <option value="reputation"> Reputation </option>
        <option value="type"> Role </option>
      </select>
    </div>
  </div>
  <div class="user-cards" id="users">
    <?php foreach ($users as $user):
      drawUserCard($user);
    endforeach; ?>
  </div>
  <div class="modal"> </div>
<?php } ?>


<?php function drawUserCard($user) { ?>
  <div class="user-card" data-name="<?= $user->name ?>" data-type="<?= $user->type ?>"
    data-username="<?= $user->username ?>">
    <div class="card-type">
      <span class="type <?= $user->type ?>-card-type"><?= $user->type ?></span>
      <span class="rep"> <?= $user->reputation ?> </span>
    </div>
    <img src="<?= $user->getPhoto() ?>" alt="profile" class="<?= $user->type ?>-card-border card-img"></img>
    <div class="card-details">
      <span class="card-name"> <?= $user->name ?> </span>
      <span class="span-username"> <?= $user->username ?> </span>
    </div>
    <div class="card-buttons">
      <?php if ($user->type == "client") { drawClientCardButtons();} 
            else if ($user->type == "agent") { drawAgentCardButtons($user);} 
            else { drawAdminCardButtons($user);} ?>
    </div>
    <input type='hidden' value=<?= $user->userId ?> id='card-userId'>
  </div>
<?php } ?>


<?php function drawClientCardButtons() { ?>
  <div class="button-wrap gradient round-border"> <button>upgrade</button> </div>
<?php } ?>

<?php function drawAgentCardButtons($user) { ?>
  <div class="two-button-wrap button-wrap gradient round-border"> <button> upgrade </button> </div>
  <div class="two-button-wrap button-wrap gradient round-border"> <button> assign </button> </div>
<?php } ?>

<?php function drawAdminCardButtons($user) { ?>
  <div class="button-wrap gradient"> <button>assign</button> </div>
<?php } ?>