<?php

require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\UserDBHandler;


$id = $_POST['id'];
$type = $_POST["type"];
$action = null;

$dbh = new UserDBHandler();

if ($type == 'suggestion') {
    $action = $_POST['action'];
    if ($action == 'accept') {
        $dbh->acceptSuggestion($id);
    } elseif ($action == 'decline') {
        $dbh->declineSuggestion($id);
    }
} else {
    $dbh->readMessage($id);
}
