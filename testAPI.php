<?php

// require_once "AirtelMoney.php";
header('Content-type:text/plain;charset=ISO-8859-1');
include_once 'autoload.php';

$db = new dbAccess();
$airtel = new AirtelMoney($db, true);

$msisdn = "0701234567";
$amount = 10;


// Paiement
$transactionId = "TXN" . time();

$response = $airtel->paymentPush($msisdn, $amount, "Paiement facture YNOV",  $transactionId);
print_r(json_encode($response));


/////////////////////////////// PAIEMENT RESEVATION  //////////////

$etat = '';

if ($statut = $airtel->checkTransaction($transactionId)) {
    print_r($statut);

    if ($statut == 'TS') {
        $etat = 'SUCCESS';
    } elseif ($statut == 'TF' || $statut == 'TE') {
        $etat = 'FAILED';
    } elseif ($statut == 'TIP') {
        $etat = 'PENDING';
    } elseif ($statut == 'TA') {
        $etat = 'AMBIGUOUS';
    }

    $return = array(
        "etat" => $etat,
        "status" => $statut

    );
    print_r(json_encode($return));
}

////////////////////// VERIFICATION DE STATUT //////////////


if ($response = $airtel->checkTransaction($transactionId)) {

    $status = $response['data']['transaction']['status'];
    $retour = $airtel->getTransactionStatusLabel($status);

    $return = array(
        "etat" => $etat,
        "status" => $status

    );
    print_r(json_encode($return));
}

////////////////////// PAIEMENT FACTURE //////////////

$reference = "Paiement facture";
$resulat =  $airtel->airtelPayment($reference, $msisdn, $amount, $transactionId);

print_r(json_encode($resulat));
