<?php
session_start();
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\UserDBHandler;
use Palmo\Core\service\Validation;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $userId = $_SESSION['user_id'];

    $saveFolderPath  = "./../storage/images/users/$userId/";
    $saveFolderUserPath = $saveFolderPath . "avatars/";
    $avatarPath = "";
    $imageData =  $_FILES['image-file'];

    $errors['image'] =  Validation::validate('image', $imageData);

    if ($errors['image']) {
        $_SESSION['errors'] = $errors;
        $_SESSION['modal_open']['image'] = true;
        header("Location: /userPage");
        exit();
    }
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
        (new UserDBHandler)->saveImageToDatabase($userId, $avatarPath);
    }
}
header("Location: /userPage");
exit();

