<?php


class Setting {

    var $serviceId =null;
    var $settingId = null;
    var $smscId=null;
    var $shortcode=null;
    var $shortcodeSMS=null;
    var $operateur=null;
    var $prefixe=null;
    var $pays=null;
    var $langue=null;
    var $langue_2=null;

    var $mode=null;
    var $dateDebut=null;
    var $dateFin=null;
    var $service_name=null;
    var $name=null;
    var $prenom=null;
    var $facture=null;
    var $montant=null;
    var $monnaie=null;
    var $call_to_action=null;
    var $canal=null;
    var $type_service=null;
    var $consultation=null;
    var $afficheuse=null;
    var $commentaire=null;
    var $active=null;


    public function __construct(array $init=NULL,$tablo_session=NULL,$tablo_lot=NULL){


        if ($init != null) {
            foreach ($init as $key => $value) {
                // $this->{$key} = trim($value);
                $this->{$key} = $value;
            }
        }

        #if($init==NULL)$init=$init["SERVICE"];
        /*
        $this->serviceId=$init["settingId"];
        $this->smscId=$init["smscId"];
        $this->keyword=$init["keyword"];
        $this->mot_cle_stop=$init["keyword_stop"];
        $this->mot_cle_stat=$init["keyword_stat"];
        $this->mot_cle_point=$init["keyword_point"];
        $this->shortcode=$init["shortcode"];
        $this->shortcode2=$init["shortcode2"];
        $this->service_name=$init["service_name"];
        $this->operateur=$init["operateur"];
        $this->pays= $init["pays"];
        $this->langue=$init["langue"];
        $this->langue_2=$init["langue_2"];
        $this->prenom=$init["prenom"];
        $this->facture=$init["facture"];
        $this->montant=$init["montant"];
        $this->monnaie=$init["monnaie"];
        $this->compteurCrack=$init["compteurCrack"];
        $this->bonusCrack=$init["bonusCrack"];
        $this->point_invit=$init["point_invit"];
        $this->point_welcom=$init["point_welcom"];
        $this->pointBad=$init["pointBad"];
        $this->point_keyword=$init["point_keyword"];
        $this->code_service=$init["code_service"];
        $this->call_to_action= "Cout : ".$this->montant.' '.$this->monnaie."/SMS";
        $this->statut= $init["active"];
        $this->prefixe=$init["prefixe"];
        $this->listLots=$this->inChaine($init["LISTE_LOTS"],$separateur="|");
        $this->top_lot=$init["TOP"];
        $this->dateDebut=$init["dateDebut"];
        $this->dateFin=$init["dateFin"];
        //$this->lot=$this->getRetourneOneKeyword($init["LISTE_LOTS"]);
        $this->mode=$init["mode"];

        $this->point_fidelite=$init["point_fidelite"];
        $this->table_fidelite=$init["table_fidelite"];
        */

    }




    public function inChaine($chaine,$separateur="|",$inf=" , "){
        $chaine=explode($separateur,$chaine);
        $res='';
        for($i=0;$i<count($chaine);$i++){
            $res.="".$chaine[$i].$inf;
        }
        return substr($res,0,strlen($res)-3);
    }

    public function getformatTablo($separateur,$a_separer)
    {
        $tablo =array();
        $i=0;
        $tablo1 = explode($separateur,$a_separer);
        $taille = (count($tablo1)-1);
        while($i<=$taille)  {
            $tablo[] = $tablo1[$i];
            $i ++;
        }
        return $tablo;
    }

    public function addKeyArray($a_separer,$tablo,$separateur="|")
    {
        $i=0;
        $tablo1 = explode($separateur,$a_separer);
        $taille = (count($tablo1)-1);
        while($i<=$taille)  {
            $tablo[] = $tablo1[$i];
            $i ++;
        }
        return $tablo;
    }

    function multiStReplace ($delimiters,$string)
    {
        $ready = str_replace($delimiters,$delimiters[0], $string);
        return  $ready;
    }

    public function replaceInKey($key,$content)
    {
        $tablo=array("");
        $mot_plus= $this->addKeyArray($key,$tablo);
        print_r($mot_plus);
        $content = $this->multiStReplace($mot_plus,strtoupper($content));
        return $content;
    }

   
}


?>
