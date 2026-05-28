<?php

class Paiement
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function launch($telephone, $amount, $reference)
    {
        $payload = array(
            'target' => 'payment',
            'telephone' => $telephone,
            'reference' => $reference,
            'amount' => $amount
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, API_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . API_TOKEN
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response, true);
    }

    public function save($reservation_id, $transaction_id, $montant, $status, $raw, $operateur)
    {
        $sqlQuery = " INSERT INTO paiements(
            reservation_id,
            transaction_id,
            montant,
            operateur,
            statut,
            raw_response,
            date_paiement
        ) VALUES( ?,?,?,?,?,?, NOW() ) ";

        $insertData = array(
            $reservation_id,
            $transaction_id,
            $montant,
            $operateur,
            $status,
            $raw
        );
        $status = $this->db->db_executeQuery($sqlQuery, $insertData);
    }
}