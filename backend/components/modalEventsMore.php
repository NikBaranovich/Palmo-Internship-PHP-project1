<?php
require './../vendor/autoload.php';
use Palmo\Core\service\Db;
session_start();

$userId = $_SESSION['user_id'];
$dateTime = $_POST['dateTime']; 
$date = date_format(date_create($dateTime), "l, d");
$dbh = (new Db())->getHandler();
    
$sql = "SELECT * FROM `events`
  WHERE user_id = :user_id
  AND (start_date <= :search_date AND end_date >= :search_date)

  "; 
 $query = $dbh->prepare($sql);

 $query->bindParam(':user_id', $userId);
 $query->bindParam(':search_date', $dateTime);
 $query->execute();

 $events = $query->fetchAll(PDO::FETCH_ASSOC);

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
?>