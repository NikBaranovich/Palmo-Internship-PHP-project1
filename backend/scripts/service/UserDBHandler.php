<?php

namespace Palmo\Core\service;

use Exception;
use PDO;
use Palmo\Core\service\Db;
use Palmo\Core\service\EventDBHandler;

class UserDBHandler
{
    private $dbHandler;

    public function __construct()
    {
        $this->dbHandler = (new Db)->getHandler();
    }

    public function createUser($username, $email, $password)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO `users` (`username`, `email`, `password`) VALUES (:username, :email, :password)";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':username', $username);
        $query->bindParam(':email', $email);
        $query->bindParam(':password', $password);

        try {
            $query->execute();
            return $this->dbHandler->lastInsertId();
        } catch (Exception $error) {
            return null;
        }
    }
    public function saveImageToDatabase($userId, $avatarPath)
    {
        $sql = "SELECT * FROM users WHERE id = :id";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':id', $userId);
        $query->execute();

        $user = $query->fetch(PDO::FETCH_ASSOC);
        if ($user['image']) {
            unlink("./../storage/images/" . $user['image']);
        }

        $sql = "UPDATE users SET image = :image WHERE id = :id";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':id', $userId);
        $query->bindParam(':image', $avatarPath);
        $query->execute();
    }
    public function getUsersByEmailLike($text)
    {
        $sql = "SELECT email FROM users WHERE email LIKE :email";

        $query = $this->dbHandler->prepare($sql);
        $text = '%' . $text . '%';
        $query->bindParam(':email', $text);
        $query->execute();

        $users = $query->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }
    public function getUserByEmail($email)
    {
        $sql = "SELECT id, email, username FROM users WHERE email = :email";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':email', $email);
        $query->execute();

        $users = $query->fetch(PDO::FETCH_ASSOC);
        return $users;
    }
    public function createMessage($userId, $title, $description, $type, $time, $suggestionId)
    {
        if (!$suggestionId) {
            $sql = "INSERT INTO user_messages (user_id, title, description, type, sent_at) VALUES (:user_id, :title, :description, :type, :sent_at)";
        } else {
            $sql = "INSERT INTO user_messages (user_id, title, description, type, event_suggestion_id, sent_at) VALUES (:user_id, :title, :description, :type, $suggestionId, :sent_at)";
        }
        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':user_id', $userId);
        $query->bindParam(':title', $title);
        $query->bindParam(':description', $description);
        $query->bindParam(':type', $type);
        $query->bindParam(':sent_at', $time);
        $query->execute();
    }
    public function getMessages($userId)
    {
        $sql = "SELECT * FROM user_messages WHERE user_id = :user_id;";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':user_id', $userId);
        $query->execute();
        $messages = $query->fetchAll(PDO::FETCH_ASSOC);
        return $messages;
    }
    public function deleteMessage($messageId, $type, $action)
    {
        if ($type == "suggestion") {
            $sql = "SELECT * FROM user_messages WHERE id = :id;";
            $query = $this->dbHandler->prepare($sql);
            $query->bindParam(':id', $messageId);
            $query->execute();
            $message = $query->fetch(PDO::FETCH_ASSOC);

            $sql = "SELECT * FROM event_suggestions WHERE id = :id;";
            $query = $this->dbHandler->prepare($sql);
            $query->bindParam(':id', $message['event_suggestion_id']);
            $query->execute();
            $suggestion = $query->fetch(PDO::FETCH_ASSOC);

            $sql = "DELETE FROM event_suggestions WHERE id = :id";
            $query = $this->dbHandler->prepare($sql);
            $query->bindParam(':id', $suggestion['id']);
            $query->execute();


            $time = date('Y-m-d H:i:s', time());
            if ($action == "accept") {
                $sql = "SELECT 
                title,
                description,
                start_date,
                end_date,
                color,
                repeat_mode
                FROM events WHERE id = :id;";
                $query = $this->dbHandler->prepare($sql);
                $query->bindParam(':id', $suggestion['event_id']);
                $query->execute();

                $event = $query->fetch(PDO::FETCH_ASSOC);
                $event['user_id'] = $suggestion['recipient_id'];

                print_r($event);

                $dbh = new EventDBHandler();
                $id = $dbh->createEvent(
                    $event['title'],
                    $event['description'],
                    $event['start_date'],
                    $event['end_date'],
                    $event['color'],
                    $event['repeat_mode'],
                    $event['user_id']
                );
                print_r("eventID:" . $id);
                $this->createMessage($suggestion['sender_id'], "Your event has been accepted!", "Your event has been accepted!", "info", $time, null);
                return;
            }
            $this->createMessage($suggestion['sender_id'], "Your event has been declined!", "Your event has been declined!", "info", $time, null);
            return;
        }
        if ($type == "info") {
            $sql = "DELETE FROM user_messages WHERE id = :id;";
            $query = $this->dbHandler->prepare($sql);
            $query->bindParam(':id', $messageId);
            $query->execute();
            return;
        }
    }
}
