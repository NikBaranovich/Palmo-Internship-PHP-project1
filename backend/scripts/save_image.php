<?php
session_start();
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\Db;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $userId = $_SESSION['user_id'];

    $saveFolderPath  = "./../storage/images/users/$userId/";
    $saveFolderUserPath = $saveFolderPath . "avatars/";
    $avatarPath = "";
    $imageData =  $_FILES['image-file'];

    if (!empty($imageData['size'])) {
        if (!is_dir($saveFolderPath)) {
            mkdir($saveFolderPath);
        }
        if (!is_dir($saveFolderUserPath)) {
            mkdir($saveFolderUserPath);
        }
        $ext = pathinfo($imageData['name'], PATHINFO_EXTENSION);
        $fileName = 'avatar_' . time() . ".{$ext}";

        if (move_uploaded_file($imageData['tmp_name'], $saveFolderUserPath . $fileName)) {
            $avatarPath = "users/$userId/avatars/$fileName";
            saveImageToDatabase($userId, $avatarPath);
        }
    }
}
header("Location: /userPage");

function saveImageToDatabase($userId, $avatarPath)
{
    $dbh = (new Db)->getHandler();
    $sql = "SELECT * FROM users WHERE id = :id";

    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $userId);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user['image']) {
        unlink("./../" . $user['image']);
    }

    $sql = "UPDATE users SET image = :image WHERE id = :id";

    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $userId);
    $query->bindParam(':image', $avatarPath);
    $query->execute();
}
