<?php

class Reservation
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    public function generateReference()
    {
        return 'RSV' . date('YmdHis') . rand(100, 999);
    }

    public function findAvailableAgent($service)
    {
        $sqlQuery = "  SELECT *  FROM agents   WHERE specialite = '$service'  AND disponible = 1    LIMIT 1  ";
        if ($resultat = $this->db->select($sqlQuery)) {
            return $resultat;
        } else
            return null;
    }

    public function create($data)
    {
        $reference = $this->generateReference();

        $agent = $this->findAvailableAgent($data['service_id']);

        $agent_id = NULL;

        if ($agent) {
            $agent_id = $agent['agent_id'];
        }

        $sqlQuery = " INSERT INTO reservations(
            reference, client_nom,  telephone,
            service_id, agent_id,  date_rdv,
            statut,  montant, paiement_statut, date_creation
        ) VALUES( ?, ?,?,?,?,?,?,?,?, NOW() ) ";

        $statut = "";
        $paiement_statut = "";
        $client_nom = trim($data['client_nom']);
        $telephone = trim($data['telephone']);
        $service_id = trim($data['service_id']);
        $date_rdv = trim($data['date_rdv']);
        $montant = trim($data['montant']);
        $insertData = array(
            $reference,
            $client_nom,
            $telephone,
            $service_id,
            $agent_id,
            $date_rdv,
            $statut,
            $montant,
            $paiement_statut
        );
        $status = $this->db->db_executeQuery($sqlQuery, $insertData);
    }

    public function details($reference)
    {
        $sqlQuery = "  SELECT r.*, s.libelle
        FROM reservations r   LEFT JOIN services s ON s.service_id = r.service_id
        WHERE r.reference = '$reference'  ";

        if ($resultat = $this->db->select($sqlQuery)) {
            return $resultat;
        } else
            return null;
    }


    public function InsertReservation($data)
    {
        // print_r($data);
        $sqlQuery = " INSERT INTO reservations(
            reference, client_nom,  telephone,
            service_id,  date_rdv,  statut,  montant, 
            paiement_statut, specialite, `description`, date_creation
        ) VALUES( ?,?,?,?,?,?,?,?,?,?, NOW() ) ";

        $params = array(
            $data['reference'],
            $data['client_nom'],
            $data['telephone'],
            $data['service_id'],
            $data['date_rdv'],
            $data['statut'],
            $data['montant'],
            $data['paiement_statut'],
            $data['specialite'],
            $data['description']
        );

        $status = $this->db->db_executeQuery($sqlQuery, $params);
        return $status;
    }

     public function insertPaiement($reservation_id, $transaction_id, $montant, $status, $raw, $operateur)
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
        return $status;
    }
}
