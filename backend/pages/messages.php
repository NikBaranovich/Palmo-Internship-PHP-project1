<?php

use Palmo\Core\service\UserDBHandler;

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $dbh = new UserDBHandler();
    $messages = $dbh->getMessages($userId);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/messages.css">
    <link rel="stylesheet" href="./css/navigation-panel.css">
    <link rel="stylesheet" href="./css/toggle-theme-switch.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Уведомления</title>
</head>

<body>
    <?php
    include_once "./components/NavigationPanel.php";
    ?>
    <?php foreach ($messages as $message) {
        if ($message['type'] == "suggestion") {
            echo "<div class='notification' data-message-id = '{$message['id']}'>
            <p>{$message['title']}<strong>{$message['description']}</strong></p>
            <button class='accept-btn' onclick='acceptEvent({$message['id']})'>Принять</button>
            <button class='reject-btn' onclick='declineEvent({$message['id']})'>Отклонить</button>
            </div>";
        } elseif ($message['type'] == "info") {
            echo "<div class='notification' data-message-id = '{$message['id']}'>
            <p>{$message['title']}<strong>{$message['description']}</strong></p>
            <button class='ok-btn' onclick='readMessage({$message['id']})'>OK</button>
            </div>";
        }
    }
    ?>
</body>

</html>

<script>
    function acceptEvent(id) {
        $.ajax({
            type: 'POST',
            url: "./scripts/process_message.php",
            data: {
                id: id,
                type: "suggestion",
                action: "accept"
            },
            success: function(data) {
                console.log(data);
                document.querySelector(`.notification[data-message-id='${id}']`).remove()
            },
            error: function(response) {}
        });

    }

    function declineEvent(id) {
        $.ajax({
            type: 'POST',
            url: "./scripts/process_message.php",
            data: {
                id: id,
                type: "suggestion",
                action: "decline"
            },
            success: function(data) {
                console.log(data);
                document.querySelector(`.notification[data-message-id='${id}']`).remove()
            },
            error: function(response) {}
        });
    }

    function readMessage(id) {
        $.ajax({
            type: 'POST',
            url: "./scripts/process_message.php",
            data: {
                id: id,
                type: "info"
            },
            success: function(users) {
                document.querySelector(`.notification[data-message-id='${id}']`).remove()
            },
            error: function(response) {}
        });
    }
</script>