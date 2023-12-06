<?php 
$eventId = $params['id'];
use Palmo\Core\service\Db;

$dbh = (new Db())->getHandler();

if (isset($_SESSION['user_id'])) {
  $userId = $_SESSION['user_id'];
  
  $sql = "SELECT * FROM `events`
  WHERE id = :id
  "; 
 $query = $dbh->prepare($sql);

 $query->bindParam(':id', $eventId);
 $query->execute();

 $event = $query->fetch(PDO::FETCH_ASSOC);
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
<?php
  include_once "./components/NavigationPanel.php";
  ?>
<div class="event-info" v-if="event">
    <div class="event-color" :style="{background: event.color}"></div>
    <div class="event-details">
      <h2 class="event-title"><?php echo $event['title'] ?></h2>
      <p class="event-time">Start: <?php echo $event['start_date'] ?></p>
      <p class="event-time">End: <?php echo $event['end_date'] ?></p>
      <p class="event-repeat">Repeat: <?php echo $event['repeat_mode'] ?></p>
      <p class="event-description">Description: <?php echo $event['description'] ?></p>
    </div>
    <div class="button-container">
      <button class="custom-button edit-button" @click="editClickHandler">
        Edit
      </button>
      <button
        class="custom-button delete-button"
        @click="isMessageModalVisible = true"
      >
        Delete
      </button>
    </div>
    <modal v-if="isModalVisible" @close="isModalVisible = false">
      <template v-slot:header>
        <h2>Edit event</h2>
      </template>
      <template v-slot:body>
        <div>
          <div class="form-group">
            <label for="event-title">Event title</label>
            <input
              class="form-input"
              type="text"
              id="event-title"
              v-model="editedEvent.title"
              @input="validateTitle"
            />
            <div v-color:red v-if="errors.title" class="invalid-input-error">
              {{ errors.title }}
            </div>
          </div>
          <div class="form-group">
            <label for="event-date">Start date</label>
            <custom-date-input
              class="form-input"
              @input="validateEndDate"
              v-model="editedEvent.startDate"
            />
          </div>
          <div class="form-group">
            <label for="event-date">End date</label>
            <custom-date-input
              class="form-input"
              @input="validateEndDate"
              v-model="editedEvent.endDate"
            />
            <div v-color:red v-if="errors.endDate" class="invalid-input-error">
              {{ errors.endDate }}
            </div>
          </div>
          <div class="form-group">
            <label for="event-color" class="mb-2">Event color</label>
            <input
              class="form-control form-control-color"
              type="color"
              v-model="editedEvent.color"
            />
          </div>
          <div class="form-group">
            <label for="event-repeat">Repeat</label>
            <custom-select
              v-model="editedEvent.repeat"
              class="form-input"
              id="event-repeat"
              :options="repeatOptions"
            />
          </div>
          <div class="form-group">
            <label for="event-description">Описание события</label>
            <textarea
              class="form-input"
              id="event-description"
              rows="4"
              v-model="editedEvent.description"
            ></textarea>
          </div>
        </div>
      </template>
      <template v-slot:footer>
        <div class="button-group">
          <button class="custom-button" @click="isModalVisible = false">
            Cancel
          </button>
          <button class="custom-button" @click="editEventHandler">Edit</button>
        </div>
      </template>
    </modal>
    <modal-message v-if="isMessageModalVisible">
      <template v-slot:header>
        <h2>Confirm deletion</h2>
      </template>
      <template v-slot:content>
        <p>Are you sure you want to delete this event?</p>
      </template>
      <template v-slot:buttons>
        <button class="message-button" @click="isMessageModalVisible = false">
          Cancel
        </button>

        <button class="message-button" @click="deleteEventHandler">Yes</button>
      </template>
    </modal-message>
  </div>
</body>
</html>