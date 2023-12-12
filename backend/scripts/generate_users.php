<?php
require __DIR__ . './../vendor/autoload.php';

use Palmo\Core\service\EventDBHandler;
use  Palmo\Core\service\UserDBHandler;

$faker = Faker\Factory::create();
$eventdbh = new EventDBHandler();
$userdbh = new UserDBHandler();

$sum = 0;
for ($i = 0; $i < 20; $i++) {

    $username = $faker->name();
    $email = $faker->email();
    $password = $username;
    $userId = $userdbh->createUser($username, $email, $password);

    $imgName = "avatar_" . time() . ".jpg";

    $imgfileUrl = "https://via.placeholder.com/200x200.jpg/004464?text={$username[0]}";
    $imageData = file_get_contents($imgfileUrl);

    $saveFolderPath  = "./storage/images/users/$userId/";
    $saveFolderUserPath = $saveFolderPath . "avatars/";

    if (!is_dir($saveFolderPath)) {
        mkdir($saveFolderPath);
    }
    if (!is_dir($saveFolderUserPath)) {
        mkdir($saveFolderUserPath);
    }
    $saveFolderUserPath = $saveFolderPath . "avatars/";
    $localImagePath = $saveFolderUserPath . $imgName;
    file_put_contents($localImagePath,  $imageData);

    $avatarPath = "users/$userId/avatars/$imgName";
    (new UserDBHandler)->saveImageToDatabase($userId, $avatarPath);

    $repeat_mode = ["none", "monthly", "annually"];
    for ($j = 0; $j < rand(10, 30); $j++) {
        $startDate = date_format($faker->dateTimeBetween('2021-01-01', '2024-12-01'), "Y-m-d H:i:s");
        $endDate =  date_format(date_modify(date_create($startDate), "+" . rand(0, 3) . " days"), "Y-m-d H:i:s");

        $data = [
            'event-title' => $faker->word(),
            'event-description' => $faker->text(),
            'event-start-date' => $startDate,
            'event-end-date' => $endDate,
            'event-color' => $faker->hexColor(),
            'event-repeat' => $repeat_mode[rand(0, 2)],
            'user_id' => $userId
        ];
        $eventdbh->createEvent($data);
    }
}
