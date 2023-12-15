<?php

namespace Palmo\Core\service;

use Exception;
use PDO;
use Palmo\Core\service\Db;

class EventDBHandler
{
    private $dbHandler;

    public function __construct()
    {
        $this->dbHandler = (new Db)->getHandler();
    }

    public function createEvent($title, $description, $startDate, $endDate, $color, $repeat, $userId)
    {
        $title = htmlspecialchars($title);
        $description = htmlspecialchars($description);

        $sql = "INSERT INTO `events` (`title`, `description`, `start_date`, `end_date`, `color`, `repeat_mode`)
        VALUES (:title, :description, :start_date, :end_date, :color, :repeat_mode);";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':title', $title);
        $query->bindParam(':description', $description);
        $query->bindParam(':start_date', $startDate);
        $query->bindParam(':end_date', $endDate);
        $query->bindParam(':color', $color);
        $query->bindParam(':repeat_mode', $repeat);

        $this->dbHandler->beginTransaction();

        try {
            $query->execute();
        } catch (Exception $error) {
            $this->dbHandler->rollBack();
            return $error;
        }

        $eventId = $this->dbHandler->lastInsertId();

        $sql = "INSERT INTO `event_user` (`user_id`, `event_id`, `is_author`)
        VALUES (:user_id, :event_id, :is_author);";

        $isAuthor = 1;
        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':user_id', $userId);
        $query->bindParam(':event_id', $eventId);
        $query->bindParam(':is_author', $isAuthor);

        try {
            $query->execute();
            $this->dbHandler->commit();

            return $eventId;
        } catch (Exception $error) {
            $this->dbHandler->rollBack();
            return $error;
        }
    }
    public function subscribeToEvent($eventId, $userId)
    {
        $sql = "INSERT INTO `event_user` (`user_id`, `event_id`, `is_author`)
        VALUES (:user_id, :event_id, :is_author);";

        $isAuthor = 0;
        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':user_id', $userId);
        $query->bindParam(':event_id', $eventId);
        $query->bindParam(':is_author', $isAuthor);

        print_r($userId." ".$eventId);
        $query->execute();
        
        return $this->dbHandler->lastInsertId();
    }
    public function editEvent($eventId, $title, $description, $startDate, $endDate, $color, $repeat)
    {
        $title = htmlspecialchars($title);
        $description = htmlspecialchars($description);

        $sql = "UPDATE `events` SET `title` = :title, `description` = :description, `start_date` = :start_date, `end_date` = :end_date, `color` = :color, `repeat_mode` = :repeat_mode
        WHERE id = :event_id";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':event_id', $eventId);
        $query->bindParam(':title', $title);
        $query->bindParam(':description', $description);
        $query->bindParam(':start_date', $startDate);
        $query->bindParam(':end_date', $endDate);
        $query->bindParam(':color', $color);
        $query->bindParam(':repeat_mode', $repeat);

        try {
            $query->execute();
            return null;
        } catch (Exception $error) {
            return $error;
        }
    }
    public function deleteEvent($eventId)
    {
        $sql = "DELETE FROM `events`
        WHERE id = :event_id";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':event_id', $eventId);

        try {
            $query->execute();
            return null;
        } catch (Exception $error) {
            return $error;
        }
    }
    public function unsubscribeEvent($eventId, $userId)
    {
        $sql = "DELETE FROM `event_user`
        WHERE event_id = :event_id
        AND user_id = :user_id";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':event_id', $eventId);
        $query->bindParam(':user_id', $userId);

        try {
            $query->execute();
            return null;
        } catch (Exception $error) {
            return $error;
        }
    }
    function getEventsInRange($userId, $startDate, $endDate)
    {
        $sql = "SELECT * FROM events
            INNER JOIN event_user ON events.id = event_user.event_id
            WHERE event_user.user_id = :user_id 
            AND ((start_date <= :endDate
            AND end_date >= :startDate)
            OR repeat_mode = 'monthly'
            OR repeat_mode = 'annually')";

        $query = $this->dbHandler->prepare($sql);
        $userId = $userId;
        $startDateString = date_format($startDate, 'Y-m-d');
        $endDateString = date_format($endDate, 'Y-m-d');
        $query->bindParam(':user_id', $userId);
        $query->bindParam(':endDate', $endDateString);
        $query->bindParam(':startDate', $startDateString);

        try {
            $query->execute();
            $events = $query->fetchAll();

            return $events;
        } catch (Exception $error) {
            return $error;
        }
    }
    function getEventsForDay($userId, $dateTime)
    {
        $sql = "SELECT * FROM events
        INNER JOIN event_user ON events.id = event_user.event_id
        WHERE event_user.user_id = :user_id 
        AND (
              (
                    repeat_mode = 'none'
                    AND '$dateTime' BETWEEN start_date AND end_date
              )
            OR
            (
              repeat_mode = 'monthly'
              AND DAY(start_date) <= DAY('$dateTime')
              AND DAY(end_date) >= DAY('$dateTime')
            )
            OR
            (
              repeat_mode = 'annually'
              AND MONTH(start_date) = MONTH('$dateTime')
              AND DAY(start_date) <= DAY('$dateTime')
              AND DAY(end_date) >= DAY('$dateTime')
            )
          )";
        $query = $this->dbHandler->prepare($sql);

        $query->bindParam(':user_id', $userId);

        try {
            $query->execute();
            $events = $query->fetchAll();

            return $events;
        } catch (Exception $error) {
            return null;
        }
    }

    function filterEvents($userId, $filter, $searchTerm, $sortBy, $sortDesc, $perPage, $offset)
    {
        $sql = "SELECT * FROM events
        INNER JOIN event_user ON events.id = event_user.event_id
        WHERE event_user.user_id = :user_id ";
        $params = [':user_id' => $userId];
        if ($filter != 'all') {
            $sql .= "AND repeat_mode = :repeat_mode ";
            $params[':repeat_mode'] = $filter;
        }
        if (!empty($searchTerm)) {
            $sql .= "AND title LIKE :search_title ";
            $params[':search_title'] = '%' . $searchTerm . '%';
        }
        $sql .= "ORDER BY $sortBy ";
        $sql .= $sortDesc ? "DESC" : "";
        $sql .= " LIMIT $perPage OFFSET $offset";

        try {
            $query = $this->dbHandler->prepare($sql);

            $query->execute($params);
            $filteredEvents = $query->fetchAll();
            return $filteredEvents;
        } catch (Exception $error) {
            return null;
        }
    }

    function countFilteredEvents($userId, $filter, $searchTerm)
    {
        $sql = "SELECT COUNT(*) AS events_count FROM events 
        INNER JOIN event_user ON events.id = event_user.event_id
        WHERE event_user.user_id = :user_id ";
        $params = [':user_id' => $userId];
        if ($filter != 'all') {
            $sql .= "AND repeat_mode = :repeat_mode ";
            $params[':repeat_mode'] = $filter;
        }
        if (!empty($searchTerm)) {
            $sql .= "AND title LIKE :search_title ";
            $params[':search_title'] = '%' . $searchTerm . '%';
        }
        $query = $this->dbHandler->prepare($sql);

        try {
            $query->execute($params);
            $filteredEvents = $query->fetch(PDO::FETCH_ASSOC);
            return $filteredEvents['events_count'];
        } catch (Exception $error) {
            return null;
        }
    }
    function getSingleEvent($eventId)
    {
        $sql = "SELECT * FROM `events`
        WHERE id = :id";
        $query = $this->dbHandler->prepare($sql);

        $query->bindParam(':id', $eventId);

        try {
            $query->execute();
            $event = $query->fetch(PDO::FETCH_ASSOC);
            return $event;
        } catch (Exception $error) {
            return null;
        }
    }
    public function sendEventToUser($senderId, $recipientId, $eventId, $time)
    {
        $sql = "INSERT INTO event_suggestions (sender_id, recipient_id, event_id, suggested_at) VALUES (:sender_id, :recipient_id, :event_id, :suggested_at)";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':sender_id', $senderId);
        $query->bindParam(':recipient_id', $recipientId);
        $query->bindParam(':event_id', $eventId);
        $query->bindParam(':suggested_at', $time);
        $query->execute();
        return $this->dbHandler->lastInsertId();
    }
    public function checkIsAnthor($userId, $eventId){
        $sql = "SELECT is_author FROM `event_user`
        WHERE event_id = :event_id
        AND user_id = :user_id";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':event_id', $eventId);
        $query->bindParam(':user_id', $userId);

        try {
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC)['is_author'];
        } catch (Exception $error) {
            return $error;
        }
    }
}
