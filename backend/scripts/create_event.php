<?php
require __DIR__.'./../vendor/autoload.php';
session_start();
use Palmo\Core\service\Db;

$year = $_SESSION['year'];
$month = $_SESSION['month'];

$dbh = (new Db())->getHandler();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['event-title'];
    $description = $_POST['event-description'];
    $startDate = $_POST['event-start-date'];
    $endDate = $_POST['event-end-date'];
    $repeatMode = $_POST['event-repeat'];
    $color = $_POST['event-color'];
    $userId = $_SESSION['user_id'];

    $sql = "INSERT INTO `events` (`title`, `description`, `start_date`, `end_date`, `color`, `repeat_mode`, `user_id`)
    VALUES (:title, :description, :start_date, :end_date, :color, :repeat_mode, :user_id)";

    $query = $dbh->prepare($sql);
    $query->bindParam(':title', $title);
    $query->bindParam(':description', $description);
    $query->bindParam(':start_date', $startDate);
    $query->bindParam(':end_date', $endDate);
    $query->bindParam(':color', $color);
    $query->bindParam(':repeat_mode', $repeatMode);
    $query->bindParam(':user_id', $userId);

    $query->execute();
    unset($_POST);
    $_POST = [];

    header("Location: /?year=$year&month=$month");
    exit();
}
