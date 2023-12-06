<?php
$email = null;


if (isset($_SESSION['previousData'])) {
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
    <link rel="stylesheet" href="./css/login.css">
    <link rel="stylesheet" href="./css/navigation-panel.css">
    <link rel="stylesheet" href="./css/navigation-panel.css">
    <link rel="stylesheet" href="./css/toggle-theme-switch.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
    <?php
    include_once "./components/NavigationPanel.php";
    ?>
    <div class="sign-in">
        <div class="sign-in-form">
            <h2>Sign In</h2>
            <form action="./scripts/login_user.php" method="POST" class="form">
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
                    <input type="password" id="password" name="password" />
                    <?php if (isset($errors['password'])) {
                        echo "<div  class='invalid-input-error'>
                        {$errors['password']}
                    </div>";
                    }
                    ?>
                </div>
                <div class="form-group">
                    <input type="checkbox" id="remember" name="remember" />
                    <label for="remember" id="remember-label">Remember me</label>
                </div>
                <?php if (isset($errors['db'])) {
                    echo "<div  class='invalid-input-error'>
                        {$errors['db']}
                    </div>";
                }
                ?>
                <button type="submit" class="sign-in-button">Sign In</button>
            </form>
        </div>
    </div>
</body>

</html>