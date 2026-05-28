<?php

include('../config/config.php');
include('../classes/Ussd.php');

$sessionId = isset($_POST['sessionId']) ? $_POST['sessionId'] : '';
$phone = isset($_POST['msisdn']) ? $_POST['msisdn'] : '';
$text = isset($_POST['text']) ? $_POST['text'] : '';

$ussd = new Ussd();

$response = $ussd->handle($text, $phone);

echo $response;