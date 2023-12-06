<?php
// print_r($_POST);
http_build_query($_POST);
header("Location: /events?" . http_build_query($_POST));
