<?php
session_start();
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\Db;
use Palmo\Core\service\Validation;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $dbh = (new Db)->getHandler();

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

    $sql = "UPDATE users SET password = :password WHERE id = :id";

    $query = $dbh->prepare($sql);
    $query->bindParam(':password', $password);
    $query->bindParam(':id', $_SESSION['user_id']);
    $query->execute();
    header('Location: /userPage');
    exit();
}
