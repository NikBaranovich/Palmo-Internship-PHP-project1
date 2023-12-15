<?php
session_start();
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\UserDBHandler;
use Palmo\Core\service\Validation;

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $newPassword = $_POST['password'];

    $errors['password'] =  Validation::validate('password', $_POST['password']);

    if ($errors['password']) {
        $_SESSION['previousData'] = $_POST;
        $_SESSION['errors'] = $errors;
        $_SESSION['modal_open']['password'] = true;
        header("Location: /userPage");
        exit();
    }
    $password = password_hash($newPassword, PASSWORD_DEFAULT);

    $id = $_SESSION['user_id'];

    $dbh = new UserDBHandler();
    $dbh->updatePassword($password, $id);

    header('Location: /userPage');
    exit();
}
