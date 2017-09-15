<?php
include 'config.php';
include 'objects/trip.php';

$trip = new Trip();

$trip->user_phone = ($_POST['user_phone']) ? $_POST['user_phone'] : '01665135866';

$data = $trip->readAll();

echo json_encode($data, JSON_UNESCAPED_UNICODE);
