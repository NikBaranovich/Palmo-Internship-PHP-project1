<?php

require './scripts/get_calendar_with_events.php';

if (isset($_SESSION['previousData'])) {
  $previousData = $_SESSION['previousData'];
  unset($_SESSION['previousData']);
}
if (isset($_SESSION['errors'])) {
  $errors = $_SESSION['errors'];
  $_SESSION['errors'] = null;
} else {
  $errors = null;
}

$displayedMonth = date_create();
$currentDate = date_create();
$year = date_format($currentDate, "Y");
$month = date_format($currentDate, "n");

if (!isset($_GET['year']) && !isset($_GET['month'])) {
  $displayedMonth = date_create();
} else {
  if (is_numeric($_GET['year']) && $_GET['year'] >= 1970 && $_GET['year'] <= 2030) {
    $year = $_GET['year'];
  }
  if (is_numeric($_GET['month']) && $_GET['month'] >= 1 && $_GET['month'] <= 12) {
    $month = $_GET['month'];
  }
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
  $weeks = getWeeksWithEvents($userId, $displayedMonth);
} else {
  session_destroy();
  $weeks = getWeeks($displayedMonth);
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
  <?php
  include_once "./components/NavigationPanel.php";
  ?>
  <div class="month-navigation">
    <a href="/?year=<?php echo $previousMonthYear ?>&month=<?php echo $previousMonth ?>" class="custom-button">
      Previous month
    </a>
    <?php
    echo '<h2>' . date_format($displayedMonth, "F Y") . '</h2>';
    ?>

    <a href="/?year=<?php echo $nextMonthYear ?>&month=<?php echo $nextMonth ?>" class="custom-button">
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
    <div class="event-modal">

    </div>
  </div>

  <div id="modal-create-event" class="custom-modal hidden">
    <form action="./scripts/create_event.php" enctype="multipart/form-data" method="POST" class="modal-content">
      <div class="modal-header">
        <h2>Add new event</h2>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="event-title">Event title</label>
          <input class="form-input" type="text" id="event-title" name="event-title" <?= isset($previousData['event-title']) ? "value= '{$previousData['event-title']}'" : '' ?> />
          <div class="invalid-input-error">
            <?php if (isset($errors['event-title'])) {
              echo "<div  class='invalid-input-error'>
                        {$errors['event-title']}
                    </div>";
            }
            ?>
          </div>
        </div>
        <div class="form-group">
          <label for="event-user-send">Send event to user</label>
          <fieldset class="position-relative">
            <input type="text" class="form-control" autocomplete="off" list="" name="event-user-send" id="event-user-send" />
            <datalist class="visually-hidden" id="user-options"> </datalist>
            <?php if (isset($errors['event-user-send'])) {
              echo "<div  class='invalid-input-error'>
                        {$errors['event-user-send']}
                    </div>";
            }
            ?>
            <div id="selected-users"></div>
            <select name="selected-users[]" id="input-selected-users" multiple style="display: none;">  
            </select>
          </fieldset>
        </div>
        <div class="form-group">
          <label for="event-start-date">Start date</label>
          <input type="datetime-local" class="form-input" name="event-start-date" id="event-start-date" <?= isset($previousData['event-start-date']) ? "value= '{$previousData['event-start-date']}'" : '' ?> />
        </div>
        <div class="form-group">
          <label for="event-end-date">End date</label>
          <input type="datetime-local" class="form-input" name="event-end-date" id="event-end-date" <?= isset($previousData['event-end-date']) ? "value= '{$previousData['event-end-date']}'" : '' ?> />
          <div class="invalid-input-error">
            <?php if (isset($errors['event-end-date'])) {
              echo "<div  class='invalid-input-error'>
                        {$errors['event-end-date']}
                    </div>";
            }
            ?>
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
          <input class="form-control form-control-color" name="event-color" type="color" <?= isset($previousData['event-color']) ? "value= '{$previousData['event-color']}'" : '' ?> />
        </div>
        <div class="form-group">
          <label for="event-description">Event description</label>
          <textarea class="form-input" name="event-description" id="event-description" rows="4"><?= isset($previousData['event-description']) ? $previousData['event-description'] : '' ?></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <div class="button-group">
          <button type="button" id="event-modal-close" class="custom-button">
            Cancel
          </button>
          <button type="submit" class="custom-button">Add event</button>
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
  <?php if (isset($_SESSION['is_modal_open']) && $_SESSION['is_modal_open']) {
    echo 'modalCreateEvent.classList.remove("hidden");';
    $_SESSION['is_modal_open'] = false;
  }
  ?>
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

  const userInput = document.getElementById("event-user-send");
  const datalistUsers = document.getElementById("user-options");

  function fetchUsers(textEmail) {
    $.ajax({
      type: 'POST',
      url: "/scripts/fetch_users.php",
      data: {
        textEmail: textEmail
      },
      success: function(users) {
        users = JSON.parse(users);
        datalistUsers.innerHTML = users.reduce(
          (layout, user) =>
          (layout += `
    <option value="${user.email}" >${user.email}</option>`),
          ``
        );
      },
      error: function(response) {}
    });


  }

  function debounce(func, timeout = 300) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => {
        func.apply(this, args);
      }, timeout);
    };
  }
  userInput.oninput = debounce(() => {
    const users = fetchUsers(userInput.value)
  });

  const hideElement = (element) => {
    element.classList.add("visually-hidden");
  };
  const showElement = (element) => {
    element.classList.remove("visually-hidden");
  };
  const hiddenInput = document.getElementById("input-selected-users");
  const selectedUsersContainer = document.getElementById("selected-users");

  const selectedUsers = JSON.parse(`<?= isset($previousData['selected-users']) ? $previousData['selected-users'] : "[]"; ?>`);

  updateSelectedUsers();
  
  function handleUserInputClick(event) {
    const target = event.target;

    if (target.tagName === "OPTION" && !selectedUsers.includes(target.value)) {
      selectedUsers.push(target.value);
      updateSelectedUsers();
      updateHiddenInput();
    }

    userInput.value = "";
    hideElement(datalistUsers);
  }

  function updateSelectedUsers() {
    selectedUsersContainer.innerHTML = "";
    selectedUsers.forEach(user => {
      const userBlock = document.createElement("div");
      userBlock.textContent = user;
      userBlock.classList.add("selected-user");
      userBlock.addEventListener("click", () => removeSelectedUser(user));
      userBlock.style.cursor = "pointer";
      selectedUsersContainer.appendChild(userBlock);
    });
  }

  function removeSelectedUser(user) {
    const userIndex = selectedUsers.indexOf(user);
    if (userIndex !== -1) {
      selectedUsers.splice(userIndex, 1);
      updateSelectedUsers();
      updateHiddenInput();
    }
  }

  function updateHiddenInput() {
    hiddenInput.innerHTML = selectedUsers.reduce((layout, user)=>{
      layout += `<option value="${user}" selected>${user}</option>`;
      return layout;
    }, "");
  }

  datalistUsers.addEventListener("click", handleUserInputClick);

  userInput.onfocus = () => {
    showElement(datalistUsers);
  };

  userInput.addEventListener("focusout", () => {
    handleTranslationInputFocusOut(datalistUsers);
  });

  function handleTranslationInputFocusOut(datalistUsers) {
    setTimeout(() => {
      hideElement(datalistUsers);
    }, 150);
  }

  function closeEventsModal() {
    const modalEvents = document.getElementById("modal-events");
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
      url: "/components/ModalEventsMore.php",
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