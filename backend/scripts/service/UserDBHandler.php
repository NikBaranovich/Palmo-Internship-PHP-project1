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
        $username = htmlspecialchars($username);
        $email = htmlspecialchars($email);
        $password = htmlspecialchars($password);
        
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
    public function getMessagesCount($userId)
    {
        $sql = "SELECT COUNT(*) AS 'message-count'FROM user_messages WHERE user_id = :user_id;";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':user_id', $userId);
        $query->execute();
        $count = $query->fetch(PDO::FETCH_ASSOC);
        return $count;
    }
    public function acceptSuggestion($messageId)
    {
        $sql = "SELECT
        event_suggestions.id AS id,
        recipient_id,
        sender_id,
        event_id,
        recipient.username AS recipient_username,
        recipient.email AS recipient_email,
        events.title AS event_title
        FROM
            event_suggestions
        INNER JOIN user_messages ON user_messages.event_suggestion_id = event_suggestions.id
        INNER JOIN users AS recipient ON recipient.id = recipient_id
        INNER JOIN events ON events.id = event_id
        WHERE
        user_messages.id = :id;";
        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':id', $messageId);
        $query->execute();
        $suggestion = $query->fetch(PDO::FETCH_ASSOC);

        $sql = "DELETE FROM event_suggestions WHERE id = :id";
        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':id', $suggestion['id']);
        $query->execute();

        $time = date('Y-m-d H:i:s', time());

        $dbh = new EventDBHandler();

        $dbh->subscribeToEvent($suggestion['event_id'], $suggestion['recipient_id']);
        $this->createMessage($suggestion['sender_id'], "Your event has been accepted!", "User {$suggestion['recipient_username']} ({$suggestion['recipient_email']}) accepted your event ({$suggestion['event_title']})!", "info", $time, null);
    }
    public function declineSuggestion($messageId)
    {
        $sql = "SELECT
        event_suggestions.id AS id,
        sender_id,
        recipient.username AS recipient_username,
        recipient.email AS recipient_email,
        events.title AS event_title
        FROM
            event_suggestions
        INNER JOIN user_messages ON user_messages.event_suggestion_id = event_suggestions.id
        INNER JOIN users AS recipient ON recipient.id = recipient_id
        INNER JOIN events ON events.id = event_id
        WHERE
        user_messages.id = :id;";
        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':id', $messageId);
        $query->execute();
        $suggestion = $query->fetch(PDO::FETCH_ASSOC);

        $sql = "DELETE FROM event_suggestions WHERE id = :id";
        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':id', $suggestion['id']);
        $query->execute();

        $time = date('Y-m-d H:i:s', time());

        $this->createMessage($suggestion['sender_id'], "Your event has been declined!", "User {$suggestion['recipient_username']} ({$suggestion['recipient_email']}) declined your event ({$suggestion['event_title']})!", "info", $time, null);
    }
    public function readMessage($messageId)
    {
        $sql = "DELETE FROM user_messages WHERE id = :id;";
        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':id', $messageId);
        $query->execute();
        return;
    }
    public function updatePassword($password, $id){
        $sql = "UPDATE users SET password = :password WHERE id = :id";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':password', $password);
        $query->bindParam(':id', $id);
        $query->execute();
        return;
    }
}
