<?php
session_start();

require __DIR__ . '/vendor/autoload.php';

use Palmo\Core\service\Db;

require_once "router.php";

if (isset($_COOKIE['SES'])) {
    $dbh = (new Db())->getHandler();
    $parts = explode(':', $_COOKIE['SES']);
    $tokenId = $parts[0];
    $tokenValue = $parts[1];
    $sql = "SELECT user_id,users.username FROM `user_tokens` 
    INNER JOIN  users ON users.id = user_tokens.user_id
    WHERE token_id = :token_id LIMIT 1;";
    $query = $dbh->prepare($sql);
    $query->bindParam(':token_id', $tokenId);
    $query->execute();
    $user = $query->fetchAll(PDO::FETCH_ASSOC)[0];
    if($user){
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
    }
    
}

function isAuthorized()
{
    return isset($_SESSION['user_id']);
}


route('/', function ($params, $query) {

    require "./pages/calendar.php";
});

route('/login', function ($params, $query) {

    if (isAuthorized()) {
        header("Location: /");
        return;
    }
    require "./pages/login.php";
});

route('/register', function ($params, $query) {
    if (isAuthorized()) {
        header("Location: /");
        return;
    }
    require "./pages/register.php";
});
route('/events/:id', function ($params, $query) {
    if (!isAuthorized()) {
        header("Location: /");
        return;
    }
    require "./pages/singleEvent.php";
});
route('/userPage', function ($params, $query) {
    if (!isAuthorized()) {
        header("Location: /");
        return;
    }
    require "./pages/user.php";
});
route('/modalEventsMore', function ($params, $query) {
    require "./components/modalEventsMore.php";
});


$action = $_SERVER['REQUEST_URI'];

dispatch($action);
