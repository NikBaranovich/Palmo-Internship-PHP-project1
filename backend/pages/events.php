<?php

use Palmo\Core\service\EventDBHandler;

function addQueryPage($page)
{
    $query = $_GET;
    $query['page'] = $page;
    return http_build_query($query);
}

$userId = $_SESSION['user_id'];

$events = [];

// Filter
$filter = 'all';
if (isset($_GET['repeat_mode'])) {
    $filter = $_GET['repeat_mode'];
}

// Search
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
}

// Sort
$sortBy = 'title';
if (isset($_GET['sort_by'])) {
    $sortBy = $_GET['sort_by'];
}

$perPage = 2;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$dbh = new EventDBHandler();
$paginatedEvents = $dbh->filterEvents($userId, $filter, $searchTerm, $sortBy, $perPage, $offset);
if(is_null($paginatedEvents)){
    header('Location: /404');
}
$totalEvents = $dbh->countFilteredEvents($userId, $filter, $searchTerm);
$totalPages = ceil($totalEvents / $perPage);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">

    <link rel="stylesheet" href="./css/navigation-panel.css">
    <link rel="stylesheet" href="./css/toggle-theme-switch.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Backend</title>
    <style>
        body {
            background-color: #f2f2f2;
        }

        .filter-bar,
        .search-bar {
            background-color: #3498db;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        button {
            background-color: #2980b9;
            color: #fff;
            border: none;
            padding: 8px 15px;
            margin-right: 5px;
            cursor: pointer;
            border-radius: 3px;
        }

        input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .event-list {
            list-style: none;
            padding: 0;
        }

        .event-item {
            border: 1px solid #3498db;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            background-color: #fff;
        }
    </style>
</head>

<body>
    <?php
    include_once "./components/NavigationPanel.php";
    ?>
    <form action="/events" method="GET">
        <div class="filter-bar">
            <input type="radio" name="repeat_mode" value="none" <?php echo !empty($filter) &&  ($filter === 'none') ? 'checked' : ''; ?>>Без повторения</button>
            <input type="radio" name="repeat_mode" value="monthly" <?php echo !empty($filter) &&  ($filter === 'monthly') ? 'checked' : ''; ?>>Ежемесячные</button>
            <input type="radio" name="repeat_mode" value="annually" <?php echo !empty($filter) &&  ($filter === 'annually') ? 'checked' : ''; ?>>Ежегодные</button>
            <input type="radio" name="repeat_mode" value="all" <?php echo (empty($filter)) || $filter === 'all' ? 'checked' : ''; ?>>Все события</button>
        </div>
        <div class="search-bar">
            <input name="search" type="search" id="searchInput" value =<?php echo !empty($sortBy) &&  ($sortBy === 'start_date') ? 'selected' : ''; ?> placeholder="Поиск...">
        </div>
        <div class="sort-bar">
            <label for="sortSelect">Сортировать по:</label>
            <select id="sortSelect" name="sort_by">
                <option value="start_date" <?php echo !empty($sortBy) &&  ($sortBy === 'start_date') ? 'selected' : ''; ?>>Дата начала</option>
                <option value="end_date" <?php echo !empty($sortBy) &&  ($sortBy === 'end_date') ? 'selected' : ''; ?>>Дата окончания</option>
                <option value="title" <?php echo (empty($sortBy) ||  ($sortBy === 'title')) ? 'selected' : ''; ?>>Название</option>
            </select>
        </div>
        <button type="submit">Применить</button>

    </form>
    <ul class="event-list">
        <?php foreach ($paginatedEvents as $event) : ?>
            <li class="event-item">
                <h3><?= $event['title']; ?></h3>
                <p>Description: <?= empty($event['description']) ? "none" : $event['description'] ?></p>
                <p>Repeat: <?= $event['repeat_mode']; ?></p>
                <p>Start date: <?= $event['start_date']; ?></p>
                <p>End date: <?= $event['end_date']; ?></p>

            </li>
        <?php endforeach; ?>
    </ul>

    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?= $i == $page ? "active" : "" ?>"><a class="page-link" href="?<?= addQueryPage($i) ?>"><?= $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>

</body>

</html>