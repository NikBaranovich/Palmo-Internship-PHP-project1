<?php

session_start();
session_destroy();
require '../vendor/autoload.php';

use Palmo\Core\service\Db;

if (isset($_COOKIE['SES'])) {
    $dbh = (new Db())->getHandler();
    $sql = "DELETE FROM user_tokens WHERE token_id = :token_id";
    $parts = explode(':', $_COOKIE['SES']);
    $tokenId = $parts[0];
    $query = $dbh->prepare($sql);
    $query->bindParam(':token_id', $tokenId);
    $query->execute();
    unset($_COOKIE['SES']); 
    setcookie('SES', '', -1, '/'); 
} 

header('Location: /');
exit();