<?php

header('Content-Type: application/json');

include('../config/config.php');
include('../classes/Database.php');
include('../classes/Paiement.php');

$db = new Database();
$conn = $db->connect();

$paiement = new Paiement($conn);

$data = json_decode(file_get_contents('php://input'), true);

$response = $paiement->launch(
    $data['telephone'],
    $data['amount'],
    $data['reference']
);

echo json_encode($response);