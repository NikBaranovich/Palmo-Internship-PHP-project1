<?php
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\UserDBHandler;

$textEmail = $_POST['textEmail'];
$dbh = new UserDBHandler();
$users = $dbh->getUsersByEmailLike($textEmail);
print_r(json_encode($users));
