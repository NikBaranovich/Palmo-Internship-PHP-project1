<?php

use Palmo\Core\service\EventDBHandler;

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


function getWeeks($displayedMonth)
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

function getWeeksWithEvents($userId, $displayedMonth)
{
    $dbh = new EventDBHandler();

    $weeks = getWeeks($displayedMonth);
    $weeksWithEvents = array_map(function ($week) use ($dbh, $userId) {
        return array_map(function ($day) use ($dbh, $userId) {
            $day->setEvents($dbh->getEventsForDay($userId, date_format($day->getDate(), 'Y-m-dTH:i:s')));
            return $day;
        }, $week);
    }, $weeks);
    return $weeksWithEvents;
}

$daysOfWeek = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
