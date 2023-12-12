<?php
session_start();
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\UserDBHandler;
use Palmo\Core\service\Validation;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $errors['username'] =  Validation::validate('username', $_POST['username']);
    $errors['email'] =  Validation::validate('email', $_POST['email']);
    $errors['password'] =  Validation::validate('password', $_POST['password']);

    if ($errors['email'] || $errors['password']) {
        $_SESSION['previousData']['username'] = $_POST['username'];
        $_SESSION['previousData']['email'] = $_POST['email'];

        $_SESSION['errors'] = $errors;
        header('Location: /register');
        exit();
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $userId = (new UserDBHandler)->createUser($username, $email, $password);
    if (is_null($userId)) {
        $errors['db'] = "This email is already in use";
        $_SESSION['errors'] = $errors;
        header('Location: /register');
        exit();
    }
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    header('Location: /');
    exit();
}
