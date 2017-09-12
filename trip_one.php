<?php
//echo hash('sha256', 'thuy123');
include 'config.php';
include 'objects/trip.php';

$trip = new Trip();
$trip->id = ($_POST['id']) ? $_POST['id'] : null;
if ($trip->id) $data = $trip->readOne();
else $data = array();

echo json_encode($data, JSON_UNESCAPED_UNICODE);
