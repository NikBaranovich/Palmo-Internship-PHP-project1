<?php
$username = null;

if (isset($_SESSION['user_id'])) {
  $username = $_SESSION['username'];
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