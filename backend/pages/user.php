<?php


use Palmo\Core\service\Db;

$username = null;

if (isset($_SESSION['user_id'])) {
  $username = $_SESSION['username'];
  $userId = $_SESSION['user_id'];
  $dbh = (new Db)->getHandler();
  $sql = "SELECT * FROM users WHERE id = :id";

  $query = $dbh->prepare($sql);
  $query->bindParam(':id', $userId);
  $query->execute();
  $user = $query->fetch(PDO::FETCH_ASSOC);

  $userEmail = $user['email'];
  $userImage = './storage/images/' . $user['image'];
  if (!isset($user['image'])) {
    $userImage = null;
  }
}

if (isset($_SESSION['previousData'])) {
  $previousData = $_SESSION['previousData'];
  $_SESSION['previousData'] = null;
}
if (isset($_SESSION['errors'])) {
  $errors = $_SESSION['errors'];
  $_SESSION['errors'] = null;
} else {
  $errors = null;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="./css/user.css">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/modal.css">
  <link rel="stylesheet" href="./css/navigation-panel.css">
  <link rel="stylesheet" href="./css/toggle-theme-switch.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
  <?php
  include_once "./components/NavigationPanel.php";
  ?>
  <div class="user-profile" v-if="user">
    <div class="padding">
      <div class="col-md-8">
        <div class="card">
          <div class="card-img-top" alt="Card image cap"></div>
          <div class="card-body little-profile text-center">
            <div class="pro-img">
              <div class="image-container">
                <img class="image" referrerpolicy="no-referrer" src=<?= isset($userImage) ? $userImage :
                                                                      "https://via.placeholder.com/240x240.png/004466?text=$username"
                                                                    ?> alt="User Photo" />
                <div class="overlay" onclick="openImageModal()">
                  <img class="camera-img" src="https://www.gstatic.com/images/icons/material/system/2x/photo_camera_white_24dp.png" />
                </div>
              </div>
            </div>
            <h3 class="m-b-0">
              <?php echo $username ?>
            </h3>
            <p><?php echo $userEmail ?></p>
          </div>
          <button class="custom-button" onclick="openPasswordModal()">
            Change password
          </button>
        </div>
      </div>
    </div>
    
    <div id="modal-image" class="custom-modal hidden">
      <form action="./scripts/save_image.php" method="POST" enctype="multipart/form-data">
        <div class="modal-content">

          <div class="modal-header">
            <h2>Edit Image</h2>
          </div>
          <div class="modal-body">
            <div>
              <div class="">
                <label class="form-label" for="image-file">Select a new image</label>
                <input class="form-control" type="file" name="image-file" id="image-file" />
                <?php if (isset($errors['image'])) {
                  echo "<div  class='invalid-input-error'>
                        {$errors['image']}
                    </div>";
                }
                ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="button-group">
              <button type="button" onclick="closeImageModal()">
                Cancel
              </button>
              <button type="submit">Update</button>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div id="modal-pasword" class="custom-modal hidden">
      <form action="./scripts/change_user_password.php" method="POST" enctype="multipart/form-data">

        <div class="modal-content">
          <div class="modal-header">
            <h2>Edit Password</h2>
          </div>
          <div class="modal-body">
            <div>
              <div class="form-group">
                <label for="username">New password:</label>
                <input class="form-input" type="text" id="username" name="password" <?= isset($previousData['password']) ? "value= '{$previousData['password']}'" : '' ?> />
                <?php if (isset($errors['password'])) {
                  echo "<div  class='invalid-input-error'>
                        {$errors['password']}
                    </div>";
                }
                ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <div class="button-group">
              <button type="button" onclick="closePasswordModal()">
                Cancel
              </button>
              <button type="submit">Update</button>
            </div>
          </div>
      </form>
    </div>
</body>

</html>

<script>
  const imageModal = document.getElementById("modal-image");
  const passwordModal = document.getElementById("modal-pasword");

  <?php if (isset($_SESSION['modal_open']['password']) && $_SESSION['modal_open']['password']) {
    echo 'passwordModal.classList.remove("hidden");';
    $_SESSION['modal_open']['password'] = null;
  }
  ?>

<?php if (isset($_SESSION['modal_open']['image']) && $_SESSION['modal_open']['image']) {
    echo 'imageModal.classList.remove("hidden");';
    unset($_SESSION['modal_open']['image']);
  }
  ?>

  function openImageModal() {
    imageModal.classList.remove("hidden");
  }

  function closeImageModal() {
    imageModal.classList.add("hidden");
  }

  function openPasswordModal() {
    passwordModal.classList.remove("hidden");
  }

  function closePasswordModal() {
    passwordModal.classList.add("hidden");
  }
</script>