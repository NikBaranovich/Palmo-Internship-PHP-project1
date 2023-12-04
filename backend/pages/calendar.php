<?php

use Palmo\Core\service\Db;

require './scripts/get_calendar_with_events.php';

$dbh = (new Db())->getHandler();
$displayedMonth = date_create();
$currentDate = date_create();
$year = date_format($currentDate, "y");
$month = date_format($currentDate, "n");

if (!isset($query['year']) && !isset($query['month'])) {
  $displayedMonth = date_create();
} else {
  $year = $query['year'];
  $month = $query['month'];
  $displayedMonth = date_create("{$year}-{$month}-1");
}

$_SESSION['year'] = $year;
$_SESSION['month'] = $month;

$previousMonth = $month;
$previousMonthYear = $year;

if ($previousMonth == 1) {
  $previousMonth = 12;
  $previousMonthYear = $year - 1;
} else {
  $previousMonth = $month - 1;
  $previousMonthYear = $year;
}

$nextMonth = $month;
$nextMonthYear = $year;
if ($nextMonth == 12) {
  $nextMonth = 1;
  $nextMonthYear = $year + 1;
} else {
  $nextMonth = $month + 1;
  $nextMonthYear = $year;
}

if (isset($_SESSION['user_id'])) {
  $userId = $_SESSION['user_id'];
  $weeks = getWeeksWithEvents($dbh, $userId, $displayedMonth);
} else {
  session_destroy();
  $weeks = getWeeks($dbh, $displayedMonth);
}


?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Backend</title>
  <base href="/">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/x-icon" href="./favicon.ico">
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/event-modal.css">
  <link rel="stylesheet" href="./css/navigation-panel.css">
  <link rel="stylesheet" href="./css/toggle-theme-switch.css">
  <link rel="stylesheet" href="./css/more-events-modal.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>

<body>
  <div id="test"></div>
  <?php
  include_once "./components/NavigationPanel.php";
  ?>
  <div class="month-navigation">
    <a href="/?year=<?php echo $previousMonthYear ?>&month=<?php echo $previousMonth ?>" class="custom-button" @click="changeDisplayedMonth(-1)">
      Previous month
    </a>
    <?php
    echo '<h2>' . date_format($displayedMonth, "F Y") . '</h2>';
    ?>

    <a href="/?year=<?php echo $nextMonthYear ?>&month=<?php echo $nextMonth ?>" class="custom-button" @click="changeDisplayedMonth(+1)">
      Next month
    </a>
  </div>

  <div class="calendar">
    <div class="week day-names">
      <?php
      foreach ($daysOfWeek as $dayOfWeek) :
      ?>
        <div class="day-name" :key="day">
          <?php
          echo $dayOfWeek;
          ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="month">
      <?php
      foreach ($weeks as $week) :
      ?>
        <div class="week">
          <?php
          foreach ($week as $day) :
          ?>
            <div class="day 
                <?php
                echo (date_format($day->getDate(), "m") != date_format($displayedMonth, "m")
                  ? 'outside-month' : '')
                ?>" data-date="<?php echo date_format($day->getDate(), "Y-m-d") ?>">
              <div class="day-number <?php echo (date_format($day->getDate(), "d-m-y") == date_format($currentDate, "d-m-y") ? 'current-day' : '') ?>">
                <?php
                echo date_format($day->getDate(), "d");
                ?>
              </div>
              <div class="events">
                <?php
                for ($i = 0; $i < (count($day->getEvents()) > 2 ? 2 : count($day->getEvents())); $i++) :
                ?>
                  <a href="events/<?php print_r($day->getEvents()[$i]['id']) ?>" class="event" style="background-color: <?php print_r($day->getEvents()[$i]['color'])  ?>">
                    <div class="event-title"><?php echo $day->getEvents()[$i]['title'] ?></div>
                  </a>
                <?php endfor; ?>
              </div>
              <?php
              if (count($day->getEvents()) > 2) {
                echo "<div class='more-events' onclick='openEventsModal()'>" .
                  (count($day->getEvents()) - 2) .
                  " More </div>";
              }
              ?>

            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div id="modal-events" class="custom-more-modal hidden">
    <div class="event-modal" :style="{left: coords.left, top: coords.top}">

    </div>
  </div>

  <div id="modal-create-event" class="custom-modal hidden" @close="isModalVisible = false">
    <form action="./scripts/create_event.php" enctype="multipart/form-data" method="POST" class="modal-content">
      <div class="modal-header">
        <h2>Add new event</h2>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="event-title">Event title</label>
          <input class="form-input" type="text" id="event-title" name="event-title" @input="validateTitle" />
          <div v-color:red v-if="errors.title" class="invalid-input-error">
            {{ errors.title }}
          </div>
        </div>
        <div class="form-group">
          <label for="event-start-date">Start date</label>
          <input type="datetime-local" class="form-input" name="event-start-date" id="event-start-date" v-model="newEvent.startDate" @input="validateEndDate" />
        </div>
        <div class="form-group">
          <label for="event-end-date">End date</label>
          <input type="datetime-local" class="form-input" name="event-end-date" id="event-end-date" v-model="newEvent.endDate" @input="validateEndDate" />
          <div v-color:red v-if="errors.endDate" class="invalid-input-error">
            {{ errors.endDate }}
          </div>
        </div>
        <div class="form-group">
          <label for="event-repeat">Repeat</label>
          <select class="form-input" name="event-repeat" id="event-repeat">
            <option value="none">None</option>
            <option value="monthly">Monthly</option>
            <option value="annually">Annually</option>
          </select>
        </div>
        <div class="form-group">
          <label for="event-color" class="mb-2">Event color</label>
          <input class="form-control form-control-color" name="event-color" type="color" v-model="newEvent.color" />
        </div>
        <div class="form-group">
          <label for="event-description">Event description</label>
          <textarea class="form-input" name="event-description" id="event-description" rows="4" v-model="newEvent.description"></textarea>
        </div>
        <input type="hidden" name="user_id" value="<?php echo $userId ?>" />
      </div>
      <div class="modal-footer">
        <div class="button-group">
          <button type="button" id="event-modal-close" class="custom-button" @click="isModalVisible = false">
            Cancel
          </button>
          <button type="submit" class="custom-button" @click="saveNewEvent">Add event</button>
        </div>

    </form>
  </div>
  </div>


</body>

</html>

<script>
  const month = document.getElementsByClassName("month")[0];
  const eventModalClose = document.getElementById("event-modal-close");
  const modalCreateEvent = document.getElementById("modal-create-event");
  let dateTime = "";

  eventModalClose.onclick = () => {
    modalCreateEvent.classList.add("hidden");
  }
  modalCreateEvent.onclick = (event) => {
    if (event.target == event.currentTarget) {
      event.currentTarget.classList.add("hidden");
    }
  }

  const modalEvents = document.getElementById("modal-events");
  const modalEventsContent = document.getElementsByClassName("event-modal")[0];


  function closeEventsModal() {
    const modalEvents = document.getElementById("modal-events");
    console.log("This:" + modalEvents);
    modalEvents.classList.add("hidden");

  }

  function openEventsModal(date) {
    modalEvents.classList.remove("hidden");
    let modalPositionX = event.pageX;
    let modalPositionY = event.pageY;
    const modalWidth = 320;

    const windowWidth = window.innerWidth;

    if (modalPositionX + modalWidth > windowWidth) {
      modalPositionX = windowWidth - modalWidth;
    }

    if (modalPositionX - modalWidth < 0) {
      modalPositionX = 0 + modalWidth;
    }

    modalEventsContent.style.left = modalPositionX + "px";
    modalEventsContent.style.top = modalPositionY + "px";

    const dateTime = event.target.closest(".day").dataset.date + "T00:00";

    $.ajax({
      type: 'POST',
      url: "modalEventsMore",
      data: {
        dateTime: dateTime
      },
      success: function(response) {
        modalEventsContent.innerHTML = response;
      },
      error: function(response) {}
    });


  }
  <?php if (!isset($_SESSION['user_id'])) {
    echo 'month.onclick = (event) => { 
        window.location.href = "/login";
    } ';
  } else
    echo 'month.onclick = (event) => {

      if (event.target.closest(".more-events")) {

        return;
      }
      if (event.target.closest(".event")) {

        return;
      }
      dateTime = event.target.closest(".day").dataset.date + "T00:00";

      modalCreateEvent.classList.remove("hidden");

      eventStartDate = document.getElementById("event-start-date");

      eventEndDate = document.getElementById("event-end-date");
      eventStartDate.value = dateTime;
      eventEndDate.value = dateTime;
    }';
  ?>
</script>