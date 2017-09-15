<?php
include 'config.php';
include 'objects/trip.php';

$trip = new Trip();

$trip->user_phone = ($_POST['user_phone']) ? $_POST['user_phone'] : null;

$num = $trip->countAll();

echo $num;
