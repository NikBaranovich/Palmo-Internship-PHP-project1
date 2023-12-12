<?php

require './../vendor/autoload.php';
use Palmo\Core\service\EventDBHandler;
session_start();

$userId = $_SESSION['user_id'];
$dateTime = $_POST['dateTime']; 
$date = date_format(date_create($dateTime), "l, d");
$events = (new EventDBHandler())->getEventsForDay($userId, $dateTime);
    

echo "<div class='event-modal-content'>
<div class='event-header'>
<span class='event-date'>{$date}</span>
<button id='more-events-close-button' onclick='closeEventsModal()' class='close-button'>
✕
</button>
</div>
<div class='events'>";
foreach($events as $event){
    echo "<a class='event' href='events/". $event['id']."' style='background-color: {$event["color"]}'>
    <div class='event-title' style='background-color: {$event["color"]}'>{$event['title']}</div>
    </a>";
}
echo "</div>
</div>";
exit();
?>