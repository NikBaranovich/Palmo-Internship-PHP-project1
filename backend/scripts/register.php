<?php
session_start();
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\Db;

$dbh = (new Db())->getHandler();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

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
        echo "ERROR! $error";
    }
}
