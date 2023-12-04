<?php
session_start();
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\Requests\LoginPostRequest;

use Palmo\Core\service\Db;

$dbh = (new Db())->getHandler();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $requestData = [
        'email' => $_POST['email'],
        'password' => $_POST['password'],
        'remember' => $_POST['remember'] ?? null,
    ];

    // $validator = new LoginPostRequest();
    // $errors = $validator->validate($requestData);
    // if (empty($errors)) {
    //   echo "Validation passed!";
    // } else {
    //   echo "Validation failed:";
    //   print_r($errors);
    // }

    $dbh = (new Db)->getHandler();
    $sql = "SELECT * FROM users WHERE email = :email";

    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $requestData['email']);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        if (password_verify($requestData['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            if ($requestData['remember']) {
                $expires = time() + (((60 * 60 * 24) * 7) * 3);  //expires in 3 weeks
                $salt = '*&salt#@';
                $tokenValue = hash('sha256', ('Logged_in' . $salt));
                $tokenId = password_hash($requestData['password'], PASSWORD_DEFAULT);
                setcookie('SES', $tokenId . ':' . $tokenValue, $expires, '/');

                $sql = "INSERT INTO user_tokens (`user_id`, `token_id`,`token_value`) VALUES (:user_id, :token_id, :token_value);";

                $query = $dbh->prepare($sql);
                $query->bindParam(':user_id', $user['id']);
                $query->bindParam(':token_id', $tokenId);
                $query->bindParam(':token_value',  $tokenValue);
                $query->execute();
            }
            header('Location: /');
            exit();
        }
    }
    header('Location: /login');
    exit();
}
