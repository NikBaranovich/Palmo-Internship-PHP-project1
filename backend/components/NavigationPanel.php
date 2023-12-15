<?php

use Palmo\Core\service\UserDBHandler;

$username = null;

if (isset($_SESSION['user_id'])) {
  $username = $_SESSION['username'];

  $dbh = new UserDBHandler();
  $messagesCount = $dbh->getMessagesCount($_SESSION['user_id'])['message-count'];
}

?>

<nav class="navbar">
  <div class="navbar-left">
    <a href="/" class='link'>Calendar</a>
    <a href="/events" class='link'>Events</a>
  </div>
  <div class="navbar-right">
    <?php
    if ($username) {
      echo "<div>
        <a href='/messages' class='link'>Messages";
      if ($messagesCount) {
        echo "<span class='position-absolute translate-middle badge rounded-pill bg-danger'>
          $messagesCount
        </span>";
      }
      echo "</a>
        <a href='/userPage' class='link user-info'>
          Welcome, $username
        </a>
        <a class='logout-button' href='./scripts/logout.php'>Logout</a>
      </div>";
    } else {
      echo "<div>
            <a href='/login' class='link'>Login</a>

            <a href='/register' class='link'>Register</a>

        </div>";
    }
    ?>
    <?php
    include_once "./components/ToggleThemeSwitch.php";
    ?>
  </div>
</nav>