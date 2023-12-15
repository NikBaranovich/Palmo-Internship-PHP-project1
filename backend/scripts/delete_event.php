<?php
require __DIR__ . './../vendor/autoload.php';
session_start();

use Palmo\Core\service\EventDBHandler;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $eventdbh = new EventDBHandler();
    $eventdbh->deleteEvent(
        $_POST['event-id']
    );

    $year = $_SESSION['year'];
    $month = $_SESSION['month'];
    header("Location: /?year=$year&month=$month");
    exit();
}

