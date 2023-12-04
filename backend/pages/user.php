<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/user.css">
    <link rel="stylesheet" href="./css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>
<body>
<div class="user-profile" v-if="user">
    <div class="padding">
      <div class="col-md-8">
        <div class="card">
          <div class="card-img-top" alt="Card image cap"></div>
          <div class="card-body little-profile text-center">
            <div class="pro-img">
              <div class="image-container">
                <img
                  class="image"
                  referrerpolicy="no-referrer"
                  :src="
                    user.photoURL ||
                    'https://avatars.dicebear.com/api/adventurer-neutral/mail%40ashallendesign.co.uk.svg'
                  "
                  alt="User Photo"
                />
                <div class="overlay" @click="isImageModalOpen = true">
                  <img
                    class="camera-img"
                    src="https://www.gstatic.com/images/icons/material/system/2x/photo_camera_white_24dp.png"
                  />
                </div>
              </div>
            </div>
            <h3 class="m-b-0">
              {{ user.displayName }}
              <img
                class="edit-name-image"
                src="@/assets/images/pencil-solid.svg"
                @click="isNameModalOpen = true"
              />
            </h3>
            <p>{{ user.email }}</p>
          </div>
          <button class="custom-button" @click="isPasswordModalOpen = true">
            Change password
          </button>
        </div>
      </div>
    </div>
</body>
</html>