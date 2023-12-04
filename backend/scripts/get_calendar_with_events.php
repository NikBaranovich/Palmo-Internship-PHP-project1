<?php

function getEvents($pdo, $userId, $startDate, $endDate)
{
    $sql = "SELECT * FROM `events` WHERE 
            user_id = :user_id
            AND ((start_date <= :endDate
            AND end_date >= :startDate)
            OR repeat_mode = 'monthly'
            OR repeat_mode = 'annually')";

    $query = $pdo->prepare($sql);
    $userId = $userId;
    $startDateString = date_format($startDate, 'Y-m-d');
    $endDateString = date_format($endDate, 'Y-m-d');
    $query->bindParam(':user_id', $userId);
    $query->bindParam(':endDate', $endDateString);
    $query->bindParam(':startDate', $startDateString);

    $query->execute();

    $events = $query->fetchAll(PDO::FETCH_ASSOC);
    return $events;
}

function getEventsForDay($date, $currentEvents)
{
    $filteredEvents = array_filter($currentEvents, function ($event) use ($date) {
        $eventStartDate = date_create($event['start_date']);
        $eventEndDate = date_create($event['end_date']);

        if (
            date_format($date, "d-m-Y") >=  date_format($eventStartDate, "d-m-Y") &&
            date_format($date, "d-m-Y") <= date_format($eventEndDate,  "d-m-Y")
        ) {
            return true;
        }

        if ($event['repeat_mode'] === "annually") {
            $targetMonthDay = intval(date_format($date, 'nd'));
            $eventStartMonthDay = intval(date_format($eventStartDate, 'nd'));
            $eventEndMonthDay = intval(date_format($eventEndDate, 'nd'));

            return (
                $targetMonthDay >= $eventStartMonthDay &&
                $targetMonthDay <= $eventEndMonthDay
            );
        }

        if ($event['repeat_mode'] === "monthly") {
            if (date_format($eventStartDate, 'd') > date_format($eventEndDate, 'd')) {
                if (date_format($date, 'd') >= date_format($eventStartDate, 'd')) {
                    return true;
                }
                if (date_format($date, 'd') <= date_format($eventEndDate, 'd')) {
                    return true;
                }
            }
            return (
                date_format($date, 'd') >= date_format($eventStartDate, 'd') &&
                date_format($date, 'd') <= date_format($eventEndDate, 'd')
            );
        }

        return false;
    });
    $filteredEvents =  array_values($filteredEvents);
    return $filteredEvents;
}


class Day
{
    private $dayDate;
    private $events;

    public function __construct($dayDate, $events = [])
    {
        $this->dayDate = $dayDate;
        $this->events = $events;
    }

    public function getDate()
    {
        return $this->dayDate;
    }

    public function addEvent($event)
    {
        $this->events[] = $event;
    }

    public function setEvents($events)
    {
        $this->events = $events;
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function __clone()
    {
        $this->dayDate = clone $this->dayDate;
        $this->events = array_map(function ($event) {
            return clone $event;
        }, $this->events);
    }
}


function getWeeks($pdo, $displayedMonth)
{
    $firstDay = date_create(date_format($displayedMonth, 'Y-m-1'));
    $lastDay =  date_create(date_format($displayedMonth, 'Y-m-t'));

    $daysInMonth = date_format($displayedMonth, 't');
    $firstDayOfWeek = date_format($firstDay, 'N') - 1;

    $previousMonth = date_create(date_format($displayedMonth, 'Y-m-0'));
    $previousMonthLastDay = date_format($previousMonth, 'd');
    $prevMonthDay = $previousMonthLastDay - $firstDayOfWeek + 1;
    $calendar = [];
    $currentWeek = [];

    $nextMonth = date_modify($firstDay, "+1 month");
    $nextMonthDay = date_modify($nextMonth, "+" . (6 - date_format($lastDay, "N")) . " days");
    $nextMonthDay = date_format($nextMonthDay, 'd');
    $firstDisplayedDate = date_create(date_format($previousMonth, 'Y-m') . "-$prevMonthDay");
    $lastDisplayedDate = date_create(date_format($nextMonth, 'Y-m') . "-$nextMonthDay");


    for ($j = 0; $j < $firstDayOfWeek; $j++) {
        $date = date_create(date_format($previousMonth, "Y-m-$prevMonthDay"));
        $currentWeek[] = new Day($date);
        $prevMonthDay++;
    }

    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = date_create(date_format($displayedMonth, "Y-m-$day"));

        $currentWeek[] = new Day($date);
        if (count($currentWeek) === 7) {
            $calendar[] = $currentWeek;
            $currentWeek = [];
        }
    }

    $nextMonthDay = 1;
    while (count($currentWeek) < 7) {
        $date = date_modify(date_create(date_format($displayedMonth, "Y-m-$nextMonthDay")), '+1 month');
        $currentWeek[] = new Day($date);
        $nextMonthDay++;
    }

    $calendar[] = $currentWeek;
    return $calendar;
}

function getWeeksWithEvents($pdo, $userId, $displayedMonth)
{
    $weeks = getWeeks($pdo, $displayedMonth);
    $startDate = ($weeks[0][0])->getDate();
    $endDate = end($weeks)[6]->getDate();
    $currentEvents = getEvents($pdo, $userId, $startDate, $endDate);
    $weeksWithEvents = array_map(function ($week) use ($currentEvents) {
        return array_map(function ($day) use ($currentEvents) {
            $day->setEvents(getEventsForDay($day->getDate(), $currentEvents));
            return $day;
        }, $week);
    }, $weeks);
    return $weeksWithEvents;
}

$daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
