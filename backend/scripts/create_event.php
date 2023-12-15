<?php
require __DIR__ . './../vendor/autoload.php';
session_start();

use Palmo\Core\service\EventDBHandler;
use Palmo\Core\service\UserDBHandler;
use Palmo\Core\service\Validation;


$year = $_SESSION['year'];
$month = $_SESSION['month'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $errors['event-title'] =  Validation::validate('title', $_POST['event-title']);
    $errors['event-end-date'] =  Validation::validate('endDate', $_POST['event-start-date'], $_POST['event-end-date']);

    $recepientEmails = isset($_POST['selected-users']) ? $_POST['selected-users'] : null;
    $recepients = [];
    if ($recepientEmails) {
        for ($i = 0; $i < count($recepientEmails); $i++) {
            $userdbh = new UserDBHandler();
            $recepient =  $userdbh->getUserByEmail($recepientEmails[$i]);
            if (!$recepient) {
                $wrongRecepientEmails[] = $recepientEmails[$i];
                unset($recepientEmails[$i]);
            } else {
                $recepients[] = $recepient;
            }
        }
        if (isset($wrongRecepients)) {
            $errors['event-user-send'] = "User with this email(s) does not exist: " . join(",", $wrongRecepientEmails);
        }
        $_POST['selected-users'] = json_encode(array_values($recepientEmails));
    }



    if (count(array_filter($errors))) {
        $_SESSION['previousData'] = $_POST;
        $_SESSION['errors'] = $errors;
        $_SESSION['is_modal_open'] = true;
        header("Location: /?year=$year&month=$month");
        exit();
    }
    $eventdbh = new EventDBHandler();
    $data = array_merge($_POST, ['user_id' => $_SESSION['user_id']]);
    $eventId = $eventdbh->createEvent(
        $data['event-title'],
        $data['event-description'],
        $data['event-start-date'],
        $data['event-end-date'],
        $data['event-color'],
        $data['event-repeat'],
        $data['user_id']
    );
    $time = date('Y-m-d H:i:s', time());

    foreach ($recepients as $recepient) {
        $suggestionId = $eventdbh->sendEventToUser($_SESSION['user_id'], $recepient['id'], $eventId, $time);
        $userdbh->createMessage($recepient['id'], "New event suggestion!", "User {$_SESSION['username']} suggested an event for you ({$data['event-title']})", "suggestion", $time, $suggestionId);
    }

    header("Location: /?year=$year&month=$month");
    exit();
}
