<?php
session_start();
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\Db;
use Palmo\Core\service\Validation;

$dbh = (new Db())->getHandler();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $errors['email'] =  Validation::validate('email', $_POST['email']);
    $errors['password'] =  Validation::validate('password', $_POST['password']);


    $_SESSION['previousData']['email'] = $_POST['email'];

    if ($errors['email'] || $errors['password']) {
        $_SESSION['errors'] = $errors;
        header('Location: /login');
        exit();
    }

    $dbh = (new Db)->getHandler();
    $sql = "SELECT * FROM users WHERE email = :email";

    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $_POST['email']);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        if (password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            if (isset($_POST['remember'])) {
                $expires = time() + (((60 * 60 * 24) * 7) * 3);  //expires in 3 weeks
                $salt = '*&salt#@';
                $tokenValue = hash('sha256', ('Logged_in' . $salt));
                $tokenId = bin2hex(random_bytes(32));
                setcookie('SES', $tokenId . ':' . $tokenValue, $expires, '/');

                $sql = "INSERT INTO `user_tokens` ( `user_id`, `token_id`, `token_value`, `expires_at`)  VALUES (:user_id, :token_id, :token_value, FROM_UNIXTIME(:expires_at))";

                $query = $dbh->prepare($sql);
                $query->bindParam(':user_id', $user['id']);
                $query->bindParam(':token_id', $tokenId);
                $query->bindParam(':token_value',  $tokenValue);
                $query->bindParam(':expires_at',  $expires);
            }
            header('Location: /');
            exit();
        }
    }
    $errors['db'] = "Invalid username or password";
    $_SESSION['errors'] = $errors;
    header('Location: /login');
    exit();
}
