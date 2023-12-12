<?php

require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\UserDBHandler;


$id = $_POST['id'];
$type = $_POST["type"];
$action = null;


if ($type == 'suggestion') {
    $action = $_POST['action'];
}
$dbh = new UserDBHandler();
$dbh->deleteMessage($id, $type, $action);
echo "success";
