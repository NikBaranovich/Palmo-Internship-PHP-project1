<?php
$email = null;
$userName = null;

if (isset($_SESSION['previousData'])) {
    
    $userName = $_SESSION['previousData']['username'];
    print_r($userName);
    $email = $_SESSION['previousData']['email'];
    $_SESSION['previousData'] = null;
}
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    $_SESSION['errors'] = null;
} else {
    $errors = null;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Backend</title>
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/register.css">
    <link rel="stylesheet" href="./css/event-modal.css">
    <link rel="stylesheet" href="./css/toggle-theme-switch.css">
    <link rel="stylesheet" href="./css/navigation-panel.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
    <?php
    include_once "./components/NavigationPanel.php";
    ?>
    <div class="registration">
        <div class="registration-form">
            <h2>Registration</h2>
            <form action="./scripts/register.php" method="POST" class="form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" <?= isset($userName) ? "value= '$userName'": '' ?> />
                    <?php if (isset($errors['username'])) {
                        echo "<div  class='invalid-input-error'>
                        {$errors['username']}
                    </div>";
                    }
                    ?>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" <?= isset($email) ? "value= '$email'" : '' ?> />
                    <?php if (isset($errors['email'])) {
                        echo "<div  class='invalid-input-error'>
                        {$errors['email']}
                    </div>";
                    }
                    ?>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password"/>
                    <?php if (isset($errors['password'])) {
                        echo "<div  class='invalid-input-error'>
                        {$errors['password']}
                    </div>";
                    }
                    ?>
                </div>
                <?php if (isset($errors['db'])) {
                    echo "<div  class='invalid-input-error'>
                        {$errors['db']}
                    </div>";
                }
                ?>
                <button type="submit" class="register-button">Register</button>
            </form>
            <div>
                Already have an account?
                <a href='/login'> Login</router-link>
            </div>
        </div>
        <modal-message v-if="isModalVisible">
            <template v-slot:header>
                <h2>Last one step</h2>
            </template>
            <template v-slot:content>
                <p>
                    Click the link we sent to {{ email }} to complete your account set-up.
                </p>
            </template>
            <template v-slot:buttons>
                <button class="message-button" @click="closeModal">Got It!</button>
            </template>
        </modal-message>
    </div>
</body>

</html>