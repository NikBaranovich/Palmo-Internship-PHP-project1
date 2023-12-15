<?php
require __DIR__ . './../vendor/autoload.php';
session_start();

use Palmo\Core\service\EventDBHandler;
use Palmo\Core\service\Validation;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $errors['event-title'] =  Validation::validate('title', $_POST['event-title']);
    $errors['event-end-date'] =  Validation::validate('endDate', $_POST['event-start-date'], $_POST['event-end-date']);

    if (count(array_filter($errors))) {
        $_SESSION['previousData'] = $_POST;
        $_SESSION['errors'] = $errors;
        $_SESSION['is_modal_open'] = true;
        header("Location: /events/{$_POST['event-id']}");
        exit();
    }
    $eventdbh = new EventDBHandler();
    $eventdbh->editEvent(
        $_POST['event-id'],
        $_POST['event-title'],
        $_POST['event-description'],
        $_POST['event-start-date'],
        $_POST['event-end-date'],
        $_POST['event-color'],
        $_POST['event-repeat'],
    );

    
    header("Location: /events/{$_POST['event-id']}");
    exit();
}
