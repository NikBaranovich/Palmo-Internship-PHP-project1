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
        $sql = "INSERT INTO `events` (`title`, `description`, `start_date`, `end_date`, `color`, `repeat_mode`, `user_id`)
        VALUES (:title, :description, :start_date, :end_date, :color, :repeat_mode, :user_id)";

        $query = $this->dbHandler->prepare($sql);
        $query->bindParam(':title', $title);
        $query->bindParam(':description', $description);
        $query->bindParam(':start_date', $startDate);
        $query->bindParam(':end_date', $endDate);
        $query->bindParam(':color', $color);
        $query->bindParam(':repeat_mode', $repeat);
        $query->bindParam(':user_id', $userId);

        try {
            $query->execute();
            return $this->dbHandler->lastInsertId();
        } catch (Exception $error) {
            return $error;
        }
    }
    function getEventsInRange($userId, $startDate, $endDate)
    {
        $sql = "SELECT * FROM `events` WHERE 
            user_id = :user_id
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
        $sql = "SELECT *
        FROM events
        WHERE user_id = :user_id 
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

    function filterEvents($userId, $filter, $searchTerm, $sortBy, $perPage, $offset)
    {
        $sql = "SELECT * FROM events WHERE user_id = :id ";
        $params = [':id' => $userId];
        if ($filter != 'all') {
            $sql .= "AND repeat_mode = :repeat_mode ";
            $params[':repeat_mode'] = $filter;
        }
        if (!empty($searchTerm)) {
            $sql .= "AND title LIKE :search_title ";
            $params[':search_title'] = '%' . $searchTerm . '%';
        }
        $sql .= "ORDER BY $sortBy LIMIT $perPage OFFSET $offset";

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
        $sql = "SELECT COUNT(*) AS events_count FROM events WHERE user_id = :id ";
        $params = [':id' => $userId];
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
        WHERE id = :id
        ";
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
}
