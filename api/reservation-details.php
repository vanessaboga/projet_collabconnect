<?php

header('Content-Type: application/json');

include('../config/config.php');
include('../classes/Database.php');
include('../classes/Reservation.php');

$db = new Database();
$conn = $db->connect();

$reservation = new Reservation($conn);

$reference = $_GET['reference'];

$data = $reservation->details($reference);

echo json_encode($data);