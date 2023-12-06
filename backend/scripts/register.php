<?php
session_start();
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\Db;
use Palmo\Core\service\Validation;


$dbh = (new Db())->getHandler();

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
    $password = password_hash($password, PASSWORD_DEFAULT);
    $dbh = (new Db)->getHandler();
    $sql = "INSERT INTO `users` (`username`, `email`, `password`) VALUES (:username, :email, :password)";

    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username);
    $query->bindParam(':email', $email);
    $query->bindParam(':password', $password);

    try {
        $query->execute();
        $_SESSION['user_id'] = $dbh->lastInsertId();
        $_SESSION['username'] = $username;
        header('Location: /');
        exit();
    } catch (Exception $error) {
        $errors['db'] = "This email is already in use";
        $_SESSION['errors'] = $errors;
        header('Location: /register');
        exit();
    }
}
