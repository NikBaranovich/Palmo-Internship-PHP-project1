<?php
session_start();

use Palmo\Core\service\Db;

require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\UserDBHandler;

$textEmail = $_POST['textEmail'];

$userId = $_SESSION['user_id'];

$dbh = new UserDBHandler();
$users = $dbh->getUsersByEmailLike($textEmail);

$dbh = (new Db)->getHandler();
$sql = "SELECT email FROM users WHERE id = :id";

$query = $dbh->prepare($sql);
$query->bindParam(':id', $userId);
$query->execute();

$userEmail = $query->fetch(PDO::FETCH_ASSOC)['email'];
$filteredUsers = array_filter($users, function ($user) use ($userEmail) {
    return $user["email"] !== $userEmail;
});
print_r(json_encode(array_values($filteredUsers)));
