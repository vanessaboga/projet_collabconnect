<?php

// require_once "AirtelMoney.php";
header('Content-type:text/plain;charset=ISO-8859-1');
include_once 'autoload.php';

$db = new dbAccess();
$airtel = new AirtelMoney($db, true);


print_r($airtel);
// Paiement
$transactionId = "TXN" . time();

//$response = $airtel->paymentPush("0701234567", 1000, "Paiement facture YNOV",  $transactionId);
// print_r($response);


// $statut = $airtel->checkTransaction($transactionId);
// print_r($statut);

// $response = $airtel->checkTransaction($transactionId);
// $status = $response['data']['transaction']['status'];
// echo getTransactionStatusLabel($status);


// if ($status == 'TS') {
//     $etat = 'SUCCESS';
// } elseif ($status == 'TF' || $status == 'TE') {
//     $etat = 'FAILED';
// } elseif ($status == 'TIP') {
//     $etat = 'PENDING';
// } elseif ($status == 'TA') {
//     $etat = 'AMBIGUOUS';
// }



$reference = "Paiement facture";
$msisdn = "0701234567";
$amount = 10;
$resulat =  $airtel->airtelPayment($reference, $msisdn, $amount, $transactionId);

echo json_encode($resulat);
