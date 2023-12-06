<?php
require __DIR__ . './../vendor/autoload.php';

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
                                                                    $faker->imageUrl(360, 360, $username, false);
                                                                    ?> alt="User Photo" />
                <div class="overlay" onclick="openImageModal()">
                  <img class="camera-img" src="https://www.gstatic.com/images/icons/material/system/2x/photo_camera_white_24dp.png" />
                </div>
              </div>
            </div>
            <h3 class="m-b-0">
              <?php echo $username ?>
              <img class="edit-name-image" src="@/assets/images/pencil-solid.svg" @click="isNameModalOpen = true" />
            </h3>
            <p><?php echo $userEmail ?></p>
          </div>
          <button class="custom-button" @click="isPasswordModalOpen = true">
            Change password
          </button>
        </div>
      </div>
    </div>
    <div class="custom-modal hidden">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Edit Username</h2>
        </div>
        <div class="modal-body">
          <div>
            <div class="form-group">
              <label for="username">New username:</label>
              <input class="form-input" type="text" id="username" v-model="username" @input="validateUsername" />
              <div v-color:red v-if="usernameError" class="invalid-input-error">
                {{ usernameError }}
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="button-group">
            <button type="button" @click="isNameModalOpen = false">Cancel</button>
            <button @click="handleUpdateUserName">Update</button>
          </div>
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
                <input class="form-control" type="file" name="image-file" id="image-file" @change="getFile" />
                <div v-color:red v-if="fileError" class="invalid-input-error">
                  {{ fileError }}
                </div>
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
    <div class="custom-modal hidden">
      <div class="modal-content">
        <div class="modal-header">
          <h2>Edit Password</h2>
        </div>
        <div class="modal-body">
          <div>
            <div class="form-group">
              <label for="username">New password:</label>
              <input class="form-input" type="text" id="username" v-model="password" @input="validatePassword" />
              <div v-color:red v-if="passwordError" class="invalid-input-error">
                {{ passwordError }}
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <div class="button-group">
            <button type="button" @click="isPasswordModalOpen = false">
              Cancel
            </button>
            <button @click="handleUpdatePassword">Update</button>
          </div>
        </div>
      </div>
</body>

</html>

<script>
  function openImageModal() {
    const imageModal = document.getElementById("modal-image");
    imageModal.classList.remove("hidden");
  }

  function closeImageModal() {
    const imageModal = document.getElementById("modal-image");
    imageModal.classList.add("hidden");
  }
</script>