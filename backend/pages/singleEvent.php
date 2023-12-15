<?php
$eventId = $params['id'];
if (isset($_SESSION['user_id'])) {
  $userId = $_SESSION['user_id'];
}
if (!is_numeric($eventId)) {
  header("Location: /404");
  exit();
}

use Palmo\Core\service\EventDBHandler;



if (isset($_SESSION['user_id'])) {
  $eventdbh = new EventDBHandler();
  $event = $eventdbh->getSingleEvent($eventId);

  if (is_null($event)) {
    header("Location: /404");
    exit();
  }
  $isAuthor = $eventdbh->checkIsAnthor($userId, $eventId);
}
if (isset($_SESSION['errors'])) {
  $errors = $_SESSION['errors'];
  $_SESSION['errors'] = null;
} else {
  $errors = null;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./../css/style.css">
  <link rel="stylesheet" href="./../css/single-event.css">
  <link rel="stylesheet" href="./../css/navigation-panel.css">
  <link rel="stylesheet" href="./../css/toggle-theme-switch.css">
  <link rel="stylesheet" href="./../css/event-modal.css">
  <link rel="stylesheet" href="./../css/modal-message.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Document</title>
</head>

<body>
  <?php
  include_once "./components/NavigationPanel.php";
  ?>
  <div class="event-info" v-if="event">
    <div class="event-color" style="background-color: <?= $event['color'] ?>;"></div>
    <div class="event-details">
      <h2 class="event-title"><?php echo $event['title'] ?></h2>
      <p class="event-time">Start: <?php echo $event['start_date'] ?></p>
      <p class="event-time">End: <?php echo $event['end_date'] ?></p>
      <p class="event-repeat">Repeat: <?php echo $event['repeat_mode'] ?></p>
      <p class="event-description">Description: <?php echo $event['description'] ?></p>
    </div>
    <div class="button-container">
      <?php
      if ($isAuthor) {
        echo '<button class="custom-button edit-button">
        Edit
      </button>
      <button class="custom-button delete-button">
        Delete
      </button>';
      }
      else{
        echo '<button class="custom-button unsubscribe-button">
        Unsubscribe
      </button>';
      }
      ?>
      
    </div>
  </div>
  <div id="modal-edit-event" class="custom-modal hidden" @close="isModalVisible = false">
    <form action="./../scripts/edit_event.php" enctype="multipart/form-data" method="POST" class="modal-content">
      <div class="modal-header">
        <h2>Edit event</h2>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="event-title">Event title</label>
          <input class="form-input" type="text" id="event-title" name="event-title" value="<?= $event['title'] ?>" />
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
          <label for="event-start-date">Start date</label>
          <input type="datetime-local" class="form-input" name="event-start-date" id="event-start-date" value="<?= $event['start_date'] ?>" />
        </div>
        <div class="form-group">
          <label for="event-end-date">End date</label>
          <input type="datetime-local" class="form-input" name="event-end-date" id="event-end-date" value="<?= $event['end_date'] ?>" />
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
            <option value="none" <?= $event['repeat_mode'] == "none" ? "selected" : "" ?>>None</option>
            <option value="monthly" <?= $event['repeat_mode'] == "monthly" ? "selected" : "" ?>>Monthly</option>
            <option value="annually" <?= $event['repeat_mode'] == "annually" ? "selected" : "" ?>>Annually</option>
          </select>
        </div>
        <div class="form-group">
          <label for="event-color" class="mb-2">Event color</label>
          <input class="form-control form-control-color" name="event-color" type="color" value="<?= $event['color'] ?>" />
        </div>
        <div class="form-group">
          <label for="event-description">Event description</label>
          <textarea class="form-input" name="event-description" id="event-description" rows="4"><?= $event['description'] ?></textarea>
        </div>
        <input name="event-id" id="event-id" type="hidden" value="<?= $event['id'] ?>" />
      </div>
      <div class="modal-footer">
        <div class="button-group">
          <button type="button" id="event-modal-close" class="custom-button">
            Cancel
          </button>
          <button type="submit" class="custom-button" >Edit event</button>
        </div>
    </form>
  </div>
  </div>
  <div class="custom-modal-message hidden" id="modal-message">
    <form action="./../scripts/delete_event.php" enctype="multipart/form-data" method="POST" class="modal-content">
      <div class="modal-message-overlay">
        <div class="modal-message-content">
          <div class="modal-message-header">
            <h2>Confirm deletion</h2>
          </div>
          <div class="modal-message-body">
            <p>Are you sure you want to delete this event?</p>
          </div>
          <div class="modal-message-footer">
            <input name="event-id" id="event-id" type="hidden" value="<?= $event['id'] ?>" />

            <button type="button" class="message-button" id="modal-message-close">
              Cancel
            </button>

            <button type="submit" class="message-button">Yes</button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="custom-modal-message hidden" id="modal-message-unsubscribe">
    <form action="./../scripts/unsubscribe_event.php" enctype="multipart/form-data" method="POST" class="modal-content">
      <div class="modal-message-overlay">
        <div class="modal-message-content">
          <div class="modal-message-header">
            <h2>Confirm unsubscribing</h2>
          </div>
          <div class="modal-message-body">
            <p>Are you sure you want to unsubscribe from this event?</p>
          </div>
          <div class="modal-message-footer">
            <input name="event-id" id="event-id" type="hidden" value="<?= $event['id'] ?>" />

            <button type="button" class="message-button" id="modal-message-close">
              Cancel
            </button>

            <button type="submit" class="message-button" @click="deleteEventHandler">Yes</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</body>

</html>
<script>
  const eventModalClose = document.getElementById("event-modal-close");
  const modalEditEvent = document.getElementById("modal-edit-event");
  const modalMessage = document.getElementById("modal-message");
  const modalUnsubscribeMessage = document.getElementById("modal-message-unsubscribe");
  const modalMessageClose = document.getElementById("modal-message-close");
 <?= $isAuthor? ' const deleteButton = document.getElementsByClassName("delete-button")[0];
  deleteButton.onclick = () => {
    modalMessage.classList.remove("hidden");
  }
  const editButton = document.getElementsByClassName("edit-button")[0];
  editButton.onclick = () => {
    modalEditEvent.classList.remove("hidden");
  };' : 
  'const unsubscribeButton = document.getElementsByClassName("unsubscribe-button")[0];
  unsubscribeButton.onclick = () => {
    modalUnsubscribeMessage.classList.remove("hidden");
  };';
  ?>
  let dateTime = "";
  <?php if (isset($_SESSION['is_modal_open']) && $_SESSION['is_modal_open']) {
    echo 'modalEditEvent.classList.remove("hidden");';
    $_SESSION['is_modal_open'] = false;
  }
  ?>
  eventModalClose.onclick = () => {
    modalEditEvent.classList.add("hidden");
  }
  modalEditEvent.onclick = (event) => {
    if (event.target == event.currentTarget) {
      event.currentTarget.classList.add("hidden");
    }
  }
  
  modalMessageClose.onclick = () => {
    modalMessage.classList.add("hidden");
  }
</script>