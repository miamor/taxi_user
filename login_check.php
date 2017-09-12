<?php
include 'config.php';
include 'objects/taxi.php';
$taxi = new Taxi();

if ($_SESSION['taxi']) {
    $taxi->username = $_SESSION['taxi'];
    $taxiData = $taxi->readOne();
    echo json_encode($taxiData, JSON_UNESCAPED_UNICODE)
}
else echo 0;
