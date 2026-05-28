<?php


class Message
{
    const CONTENU_INDISPONIBLE = "Desolé! Le contenu demandé est momentanement indisponible. Veuillez reéssayer plus tard. La radio Omega FM vous remercie!{CR}0: Retour{CR}00: Accueil";
    public $NISSA;
    public $rapport;
    public $projet;

    var $central = null;
    var $setting = null;

    var $shortcode = null;
    var $shortcodeSMS = null;
    var $projetName = null;


    public function __construct(Central $central)
    {
        $this->central = $central;
        // $this->setting = $this->central->fonction->Request->setting;
        $this->setting = "";
        $this->shortcode = "";
        $this->shortcodeSMS = "";
        $this->projetName = "";
        // $this->shortcode = $this->setting->shortcode;
        // $this->shortcodeSMS = $this->setting->shortcodeSMS;
        // $this->projetName = $this->setting->service_name;
    }

    public static function regeneration(EtatLecture $etat)
    {
        return new EtatLecture($etat->page, $etat->title, $etat->contenu, $etat->suivant, $etat->present);
    }


    public function libelleGetReservation($libelle = "")
    {
        return new EtatLecture(1, "{$libelle}", "1. Reserver{CR}0. Retour{CR}00. Accueil");
    }

    public function libelleGetDelaiReservation(Service $service)
    {

        return new EtatLecture(
            1,
            "{$service->libelle}{CR}Dans combien de temps souhaitez -vous que l'agent passe pour exécuter la tache ?",
            "1. Dans 2 jours{CR}2. Dans 3 jours{CR}3. Dans 7 jours{CR}0. Retour{CR}00. Accueil"
        );
    }

    public function libelleGetDelaiReservationElectricite(Service $service)
    {
        return new EtatLecture(
            1,
            "{$service->libelle}{CR}Dans combien de temps souhaitez -vous que l'agent passe pour exécuter la tache ?",
            "1. Aujourd hui (Dans 1H){CR}2. Dans 3 jours{CR}3. Dans 7 jours{CR}0. Retour{CR}00. Accueil"
        );
    }


    public function libelleGetDelaiReservationAfficheDev(Service $service, $montant)
    {
        $lib = "";

        if ($service->keyword == "affiche") {
            $lib = "conception de visuel";
        } elseif ($service->keyword == "informatique") {
            $lib = "developpement";
        }
        return new EtatLecture(
            1,
            "{$service->libelle}{CR}Vous recevrez un lien par SMS pour remplir le formulaire de la demande de $lib. Cout de reservation : {$montant}F.",
            "1. OUI{CR}2. NON{CR}0. Retour{CR}00. Accueil"
        );
    }


    public function libelleChoixForfaitReservationElectricite(Service $service, $montant, $delai)
    {

        $title = "{$service->libelle}{CR}Un agent vous contactera bientot pour la prise en charge.{CR}Cout de reservation : {$montant}F. ";

        // $title = "{$libelle}{CR}Cout de reservation : {$montant}F, un agent passera dans {$delai}jrs a 9h pour la tache.";
        $option = "1. OUI{CR}2. NON{CR}0.Retour";
        return new EtatLecture(1, $title, $option);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function libelleChoixForfaitReservation(Service $service, $montant, $delai)
    {
        if ($service->keyword == "menage") {
            //$libelle = $service->infos;
            $libelle = "Cout du service Menage 5000frs/piece de 9mettre carre.";
        } else
            $libelle = $service->libelle;

        if ($delai == "24")
            $delai = "1";

        $delai = str_replace('J', '', $delai);
        $title = "{$libelle}{CR}Cout de reservation : {$montant}F, un agent passera dans {$delai}jrs a 9h pour la tache.";
        $option = "1. OUI{CR}2. NON{CR}0.Retour";
        return new EtatLecture(1, $title, $option);
    }





    public function libelleModeFacturation(Service $service, $montant, $delai)
    {
        $title = "{$service->libelle}{CR}Choisir le mode paiement de la reservation";
        $option = "1. Airtel Money{CR}0.Retour";
        return new EtatLecture(1, $title, $option);
    }



    public function libelleInviteAM(Service $service, $montant)
    {
        $title = "{$service->libelle}{CR}Paiement de {$montant}F. par AM, entrer le code PIN pour confirmer.";
        $option = "0.Retour";
        return new EtatLecture(1, $title, $option);
    }
    public function libelleReservationOK(Service $service, $delai, $dateRDV, $heure = null)
    {

        //remplacer J dans $delai
        $delai = str_replace('J', '', $delai);
        //date = delai + date actuelle ouvre
        // $delais = [2, 3, 7];

        // foreach ($delais as $delai) {

        //     $dateRDV = date('Y-m-d', strtotime("+$delai days"));

        //     echo formatDateRDV($dateRDV) . "<br>";
        // }
        // $dateRDV = date('Y-m-d', strtotime("+$delai days"));


        $dateResevation = $this->formatDateRDV($dateRDV, $heure);
        return new EtatLecture(1, $service->description . "Félicitation, votre rendez-vous de {$service->libelle} est pris en compte pour {$dateResevation}, un agent vous contactera bientôt. Merci pour la confiance!", "0. Retour{CR}00. Accueil");
    }

    public function echecOperation(Service $service = NULL)
    {
        if ($service != NULL)
            $libelle = $service->libelle;
        else
            $libelle = $this->projetName;
        return new EtatLecture(1, "ton service {$libelle} est momentanement indisponible. Veuillez ressayer plus tard.", "");
    }

    public static function creditInsuffisant(Service $service)
    {
        return new EtatLecture(1, "Désole ! Votre crédit est insuffisant pour effectuer cette opération. Vous devez disposer d'au moins {$service->montant}F", "00. Accueil");
    }


    public function auncunFacture(Service $service = NULL)
    {
        if ($service != NULL) $libelle = $service->libelle;
        else $libelle = "";
        return new EtatLecture(1, "Desole! tu n'as aucune facture au service {$libelle}.", "00. Accueil");
    }

    public static function auncunAbonnementRubrique(Service $service)
    {
        return new EtatLecture(1, "Desole! tu n'as aucune souscription active au service " . $service->description, "00. Accueil");
    }

    public static function menuAbonne(Service $service)
    {
        return new EtatLecture(1, "Cher abonne , recevez l'actualite " . $service->description . " sur ton mobile a " . $service->tarif_consultation . "F la consultation.", "1. Confirmer{CR}2. Se desabonner{CR}0. Retour");
    }

    public static function menuConsultation(Service $service)
    {
        return new EtatLecture(1, "recevez l'actualite " . $service->description . " sur ton mobile a " . $service->tarif_consultation . "F la consultation.", "1. Confirmer{CR}0. Retour");
    }

    public function notificationAbonnOK(Service $service, BundleNISSA $bundle, $renew, $plus = NULL)
    {
        if ($renew == "YES")
            $libelle = "Avec renouvellement automatique";
        else
            $libelle = "Sans renouvellement automatique";

        if ($service->service == "JOB ALERT")
            $additif = " pour " . $bundle->affichage;
        else
            $additif = " {CR}" . $service->tarif_consultation . "F/SMS";
        $aff = "Felicitation, tu es abonne a " . $service->description . " $libelle" . $additif . ".{CR}Pour te desabonner compose " . strtoupper($service->shortcode);
        return new EtatLecture(1, $aff);
    }




    public function menuNotifUssdRenewOK(Service $service, $libelle)
    {
        return new EtatLecture(1, "Ta souscription {$libelle} au service {$service->description} arrive a echeance . Veux tu te reabonner gratuitement?", "1.Oui{CR}2. Non");
    }

    public function menuAbonnementRenewOK(Service $service, $libelle)
    {
        return new EtatLecture(1, "Ton abonnement au service {$service->description} a ete renouvele gratuitement avec succes pour {$libelle}. Plus d'infos sur {$service->shortcode}. {$service->tarif_consultation}F/SMS.", "00. Accueil");
    }

    public function menuAbonnementRenewNOK(Service $service, $libelle)
    {
        return new EtatLecture(1, "Desole ton abonnement au service {$service->description} n a pas aboutir . Plus d'infos sur {$service->shortcode}.", "00. Accueil");
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function menuAbonnementOK(Service $service, $deadline, $renew)
    {
        if ($renew == "YES")
            $libelle = "Avec abonnement automatique";
        else
            $libelle = "Sans renouvellement automatique";
        return new EtatLecture(1, "Ton abonnement au service {$service->description} $libelle  a ete pris en compte. Tu recevras un retour sms sous peu. ", "00. Accueil");
    }
    public function menuAbonnementOKSMS(Service $service)
    {
        return new EtatLecture(1, "Ton abonnement au service {$service->description} a ete pris en compte. Tu recevras un retour sms sous peu. " . $service->tarif_consultation . "F/SMS", "00. Accueil");
    }

    public static function desabonnOK(Service $service)
    {
        return new EtatLecture(1, "Cher client , ta souscription au service {$service->description} a ete annule avec succes. Pour te reabonner , compose {$service->shortcode} ", "0. Retour");
    }

    public static function menuConsultationDesabonnALL($serviceName = null)
    {
        return new EtatLecture(1, "Cher client , souhaite tu te desabonner au service {$serviceName} ", "1. Confirmer{CR}2. Annuler{CR}0. Retour");
    }

    public function desabonnOKTout($serviceName = null)
    {

        return new EtatLecture(1, "Ta souscription au service {$serviceName}  a ete annule avec succes. Pour te reabonner, compose " . $this->shortcode, "00. Accueil");
    }
    public static function choixRenew()
    {
        $libelle = "Merci de choisir ton type d'abonnement SVP!";
        return new EtatLecture(1, "$libelle", "1. Abonnement renouvelable{CR}2. Abonnement non renouvelable{CR}0.Retour");
    }

    public static function abonnementSMS(Service $service)
    {
        //return "Vous avez choisi le service {$service->description}." . $service->tarif_consultation . "/SMS\nMerci d'envoyer par sms:\n1 pour une offre " . NISSA::INT_MOIS . "Jours\n2 pour une offre " . NISSA::INT_QUINZAINE . "Jours\n3 pour une offre " . NISSA::INT_SEMAINE . "Jours\n4 pour une Consultation";
    }
    public static function prolongementOK(Bundle $bundle, Service $service)
    {
        return new EtatLecture(1, "Cher client , ta souscription au service {$service->description} a ete prolonge de " . $bundle->affichage . ". Pour te desabonner envoyez STOP " . strtoupper($service->level) . " au {$service->shortcode_sms}.", "0. Retour");
    }


    public static function contenuIndisponible(Service $service)
    {
        return new EtatLecture(1, "Desole, il n y a pas de contenus disponibles pour le service " . $service->description . ". Merci de ressayer plus tard. ", "0. Retour");
    }

    public function consultationOk(Service $service, BundleNISSA $bundle)
    {
        $aff = "La consultation du service {$service->description} t' as coute " . $bundle->tarif . "F. Tu recevras le contenu via SMS sous peu. ";
        $option = "{CR}0. Retour{CR}00. Accueil";
        return new EtatLecture(1, $aff, $option);
    }

    public function consultationOkSMS2(Service $service, Bundle $bundle)
    {
        return new EtatLecture(1, "La consultation du service {$service->description} t'as a couté " . $$bundle->tarif . "F. Pour plus de contenus, compose " . $service->shortcode, false, 0);
    }

    public function resultatName($nom1, $nom2)
    {
        $lovename = strtolower(preg_replace("/ /", "", strip_tags(trim($nom1 . $nom2))));

        $alp = count_chars($lovename);

        for ($i = 0; $i <= 255; $i++) {
            if ($alp[$i] != false) {
                $anz = strlen($alp[$i]);

                if ($anz < 2) {
                    $calc[] = $alp[$i];
                } else {
                    for ($a = 0; $a < $anz; $a++) {
                        $calc[] = substr($alp[$i], $a, 1);
                    }
                }
            }
        }
        if (!isset($calc) or count($calc) < 2) {
            $calc = array('4', '3');
        }

        while (($anzletter = count($calc)) > 2) {
            $lettermitte = ceil($anzletter / 2);

            for ($i = 0; $i < $lettermitte; $i++) {
                $sum = array_shift($calc) + array_shift($calc);
                $anz = strlen($sum);

                if ($anz < 2) {
                    $calcmore[] = $sum;
                } else {
                    for ($a = 0; $a < $anz; $a++) {
                        $calcmore[] = substr($sum, $a, 1);
                    }
                }
            }

            $anzc = count($calcmore);

            for ($b = 0; $b < $anzc; $b++) {
                $calc[] = $calcmore[$b];
            }

            array_splice($calcmore, 0);
        }
        if (!isset($calc) or !isset($calc[0]) or !isset($calc[1])) {
            $calc[0] = 5;
            $calc[1] = 3;
        }
        $this->rapport = $calc[0] . $calc[1] . " %";
        return "Votre rapport de compatibilité romantique est de " . $calc[0] . $calc[1] . " % pour $nom1 et $nom2";
    }
    public function resultatAstro($nom1, $nom2)
    {
        return $this->resultatName($nom1, $nom2);
    }

    public static function abonnementOKSMS(Bundle $bundle, Service $service)
    {
        return "Felicitations! Tu viens de t'abonner a la rubrique {$service->description}. Le cout du service est " . $bundle->tarif . "F/" . $bundle->affichage . ". Pour te desabonner envoyez STOP {$service->level} au {$service->shortcode}.";
    }

    public function menuProlongement(EtatAbonne $etatAbonne)
    {
        $service = $this->NISSA->retourneService($etatAbonne->node);
        return new EtatLecture(1, "Cher abonne, ton abonnement $service->description arrive a echeance dans " . $etatAbonne->jour . " Jrs, " . $etatAbonne->heure . " H et 00 Min.", "1. Prolonger ton abonnement{CR}0. Annuler - Retour");
    }
    public static function abonnementProlongement()
    {
        return new EtatLecture(1, "Veuillez selectionner l'offre a ajouter a ton forfait actuel.", "1. Offre Semaine{CR}2. Offre Quinzaine{CR}3. Offre Mois{CR}0. Retour{CR}00. Accueil");
    }




    public static function creditInsuffisantSMS(Service $service, Bundle $bundle)
    {
        return "Ton credit est insuffisant pour un abonnement " . $bundle->souscription_aff . " au service " . $service->libelle . ". Tu dois disposer d'au moins " . $bundle->tarif . "F. Plus d'infos sur " . substr($service->shortcode, 0, 4) . "#";
    }

    public static function confirmDesabonnSMS()
    {
        return ("Souhaites-tu quitter le service Togocom Kiosque?" . PHP_EOL . "1. Confirmer{CR}0. Annuler - Retour");
    }
    public static function pasEncoreAbonneSMS(Service $service)
    {
        return " Desole , tu n'es pas inscrit au service {$service->libelle}. Veuilles composer {$service->shortcode} ou envoyer {$service->level} au {$service->shortcode2} pour souscrire a ce contenu.";
    }
    //SMSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS
    public static function pourConsulter(Service $service)
    {
        return "Merci de confirmer ton choix pour ce contenu en envoyant 1 par sms au " . $service->shortcode_sms . ". Tu seras facture a " . $service->tarif_consultation . "F pour cette consultation.";
    }
    public static function consultationOKSMS(Service $service)
    {
        return "La consultation du service {$service->description} t'a coute " . $service->tarif_consultation . "F. Pour plus de contenus, compose {$service->shortcode}";
    }
    public static function contenuIndisponibleSMS(Service $service)
    {
        return "Desole , il n y a pas de contenus disponibles pour le service {$service->description}. Merci de ressayer plus tard.";
    }
    public static function pourSabonner(Service $service, Bundle $bundle)
    {
        return "Merci de confirmer ton choix pour ce contenu en envoyant 1 pour un abonnement simple ou 2 pour un abonnement renouvelable automatiquement." . $bundle->tarif . "F/" . $bundle->affichage . ".";
    }
    public static function prolongementOKSMS(Service $service, Bundle $bundle)
    {
        return "Felicitations! ta souscription a la rubrique {$service->description} a ete prolonge de " . $bundle->affichage . ". Cout du service : " . $bundle->tarif . "F. Pour te desabonner envoi STOP {$service->level} au {$service->shortcode2}";
    }
    public function desabonnToutSMS($serviceName = "Togocom Kiosque")
    {
        return "Cher client Togocom, ta souscription au service $serviceName a ete annule avec succes.  Pour te reabonner, compose {$this->shortcode}";
    }
    public function aucunAbonnementSMS()
    {
        return "Desole, tu n'es inscrit a aucun service Togocomom  Kiosque. Pour t'abonner a nos services Sports, Actualites et Divertissement, compose {$this->shortcode}";
    }
    public static function renewAcceptSMS(Service $service, Bundle $bundle, $nb = '3 jrs')
    {
        return "Merci pour ta confirmation ! Ta souscription " . $bundle->souscription_aff . " au service {$service->description} sera donc renouvele automatiquement dans $nb à {$bundle->tarif}F.";
    }

    //SMSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSSS
    public static function pasEncoreAbonne(Service $service)
    {
        return new EtatLecture(1, "Desole, tu n'es pas inscrit a cette rubrique. Pour t' abonner, envoi {$service->level} au {$service->shortcode2} .", "0. Retour");
    }

    public static function operationDesabAnnuler($serviceName = "Togocom Kiosque")
    {
        return "Cher client Togocom, ton desabonnement au service $serviceName a ete annule avec succes.";
    }


    public static function dejaAbonne(Service $service)
    {
        return "Desole! tu as deja une souscription active au service " . $service->description . ". veuilles composer " . substr($service->shortcode, 0, 4) . "#";
    }



    public static function desabonnOKSMS(Service $service)
    {
        return "Cher client Togocom, ta souscription au service {$service->description} a ete annule avec succes .  Pour te reabonner, compose {$service->shortcode} ou envoi {$service->level} au {$service->shortcode_sms}";
    }

    #########################################################CE QUI CHANGE PAS#######################################
    public static function autoAbonnementNO(Service $service, Bundle $bundle)
    {
        return "Ton abonnement au service {$service->description} n'a pas pu etre renouvele . Veuilles recharger ton compte d'au moins " . $bundle->tarif . "F pour activer vos contenus.";
    }

    public static function exortation(Service $service, Bundle $bundle)
    {
        return "Ta souscription " . $bundle->souscription_aff . " au service {$service->description} a expire . Tu peux envoyer {$service->level} au {$service->shortcode_sms} pour un renouvelement. Plus d'infos {$service->shortcode}";
    }

    public static function autoAbonnementOK(Service $service, Bundle $bundle)
    {
        return "Ton abonnement au service {$service->description} a ete renouvele avec succes pour $bundle->affichage a 0F. Retrouve plus d'infos  sur $service->shortcode.";
    }

    public static function notification(Bundle $bundle, Service $service, $nb = "24", $renew = 'YES')
    {

        return "Ta souscription " . $bundle->souscription_aff . " au service " . $service->description . " sera renouvele dans " . $nb . "H a 0F pour " . $bundle->affichage . ". Pour te desabonner.envoyez STOP " . strtoupper($service->level) . " au " . $service->shortcode_sms;
    }



    public function smsWrongMt()
    {
        return "Desole ,  tu as saisi un mot-cle incorrect. Pour vous abonner a TOGOCOM INFOS , compose {$this->shortcode}";
    }

    public function libelleChoixMode(Service $service, $forfait, $tarif, $type = false)
    {

        if ($type) {
            $message = "Bravo, tu viens d'opter pour {$service->description} et tu seras facture a $tarif F/SMS{CR}1. Avec abonnement automatique{CR}2. Sans renouvellement automatique";
        } else {
            $title = $service->libelle . " - Forfait $forfait ($tarif F/SMS) ";
            $aff = "1. Abonnement renouvelable{CR}2. Abonnement Simple{CR}3. Consultation(" . $service->tarif_consultation . "F)";
            $message = $title . "{CR}" . $aff;
        }
        $option = "0.Retour";

        return new EtatLecture(1, $message, $option);
    }

    public static function menuChoixBundleAbonnementSimple(Service $service)
    {
        $libelle = $service->description;
        return new EtatLecture(1, $libelle . "{CR}1. Offre mois (" . $service->tarif_mois . "F){CR}2. Offre semaine (" . $service->tarif_semaine . "F){CR}3. Jour(" . $service->tarif_jour . "F){CR}4. Consultation(" . $service->tarif_consultation . "F)", "9. Se débonner{CR}0. Retour");
        #else return new EtatLecture(1,"$libelle{CR}1. Offre ".NISSA::INT_MOIS."Jours{CR}2. Offre ".NISSA::INT_QUINZAINE."Jours{CR}3. Offre ".NISSA::INT_SEMAINE."Jours{CR}4. Se débonner","0. Retour");

    }
    //la lecture d'une consultation par rapport au next et a l'id*************************
    /*public  function abonneLecture($id, $page)
    {
        if ($page < 1) $page = 1;
        if ($page > Config::PAGE_MAX_CONTENU) $page = Config::PAGE_MAX_CONTENU;
        $tableauTri = array("where" => "infoId='" . $id . "'", "limit" => "1");
        $find = "*";
        $infoBD = $this->NISSA->findRecord($find, $tableauTri, "info");
        $res = new EtatLecture();
        $res->id_consultation = $id;

        while (strlen($infoBD["info" . $page]) == 0)
            $page--;

        $indice = "info" . ($page);
        $indice2 = "info" . ($page + 1);
        $res->page = $page;
        $message = str_replace("\n", " ", $infoBD[$indice]);
        $message = str_replace("\r\n", " ", $message);
        $res->title = $message;
        $res->contenu = "0. Retour";
        if (isset($infoBD[$indice2]) and strlen($infoBD[$indice2]) > 0) {
            $res->suivant = true;
            $res->contenu .= "{CR}9. Suivant";
        } else {
            $res->contenu .= "{CR}00. Accueil";
        }
        return $res;
    }*/

    public function formatDateRDV($date, $heure = null)
    {
        if (empty($date)) {
            return "";
        }

        $jours = [
            "Sunday" => "Dimanche",
            "Monday" => "Lundi",
            "Tuesday" => "Mardi",
            "Wednesday" => "Mercredi",
            "Thursday" => "Jeudi",
            "Friday" => "Vendredi",
            "Saturday" => "Samedi"
        ];

        $mois = [
            "01" => "Jan",
            "02" => "Fev",
            "03" => "Mars",
            "04" => "Avr",
            "05" => "Mai",
            "06" => "Juin",
            "07" => "Juil",
            "08" => "Août",
            "09" => "Sept",
            "10" => "Oct",
            "11" => "Nov",
            "12" => "Déc"
        ];

        $timestamp = strtotime($date);

        $jourNom = $jours[date("l", $timestamp)];
        $jour = date("d", $timestamp);
        $moisNom = $mois[date("m", $timestamp)];
        $annee = date("Y", $timestamp);

        $dateFormatee = "$jourNom $jour $moisNom $annee";

        // Ajouter heure si fournie
        if (!empty($heure)) {

            $heureFormat = date("G\\h", strtotime($heure));

            $dateFormatee .= " à $heureFormat";
        }

        return $dateFormatee;
    }
}
