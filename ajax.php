<?php

require "app.php";

$photo = $_REQUEST['photo'] ?? 'photo1.jpg';
$radius = $_REQUEST['radius'];
$n = $_REQUEST['n'];

$return = array();
$app = new app();

$app->showImage($app->aswlcFilter($photo, $radius, $n));
