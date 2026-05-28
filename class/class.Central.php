<?php

class Central extends MENU
{


    var $init;
    var $text;
    var $newNext;
    var $result;
    var $freeFlow;

    var $mes_abonnements = null;
    var $callers = null;
    var $callerId = null;

    public $menuUssd = null;

    public $link = null;
    public $menu = null;

    public $menuSpecialite = null;
    public $tableauSpecialite = null;
    public $users = null;


    function __construct($request, $canal)
    {

        $this->link = new dbAccess();
        parent::__construct($request, $canal, $this->link);


        $this->fonction = new Fonction($request, $canal, $this->link);
        $this->menuUssd = new MenUssd($this->link);
        $this->menuSpecialite = new MenuServiceSpecialite();

        $this->users = $this->fonction->retourneAgent();

        $this->tableauSpecialite = array(
            "electricite" => array(

                1 => array(
                    "libelle" => "Installation",
                    "keyword" => "installation",
                    "code_service" => 1,
                    "url" => ""
                ),

                2 => array(
                    "libelle" => "Reinstallation",
                    "keyword" => "reinstallation",
                    "code_service" => 2,
                    "url" => ""
                ),
                3 => array(
                    "libelle" => "Maintenance",
                    "keyword" => "maintenance",
                    "code_service" => 3,
                    "url" => ""
                )
            )
        );
        // $this->etat = $this->fonction->getAbonnementTelephone();

    }


    public function menuErreurService()
    {
        $pourAfficher = new EtatLecture(1, "desolé une erreur est survenue veuillez ressayer", "");
        $this->setResponse($pourAfficher, $pourAfficher, "menu", "FB");
    }

    public function menuFinParcours()
    {
        $pourAfficher = new EtatLecture(1, "Merci d'avoir utilise le service, revenez Bientot", "");
        $this->setResponse($pourAfficher, $pourAfficher, "menu", "FB");
    }


    public function flowMenuPrincipal($id_context = null, $page = 1)
    {

        $pourAfficher = $this->menuUssd->menuGroupe($id_context, $page);
        $this->setResponse($pourAfficher, $pourAfficher, "menu_groupe_" . $id_context . "_" . $page);
    }

    public function flowContinueMain($code_service = null, $id_context = null)
    {

        if ($code_service == NULL) {
            $this->flowMenuPrincipal(0, 1);
        } else {

            if ($code_service == "2") {
                $pourAfficher = new EtatLecture(1, "Saisir le numero du client", "0.Retour");
                $this->setResponse($pourAfficher, $pourAfficher, "genererFacture");
            } else {
                $service = $this->retourneService($code_service);
                if ($service != null) {
                    $this->getRedirectionMenuGroupe($service);
                } else {
                    $this->menuFinParcours();
                }
            }
        }
    }

    public function getRedirectionMenuGroupe(Service $service)
    {
        //$this->LOG(__FUNCTION__ . "  Executing Main menu rule");
        switch (strtolower($service->keyword)) {
            case "repassage":
                $pourAfficher = $this->messager->libelleGetReservation($service->libelle);
                $this->setResponse($pourAfficher, $pourAfficher, "menuReservation_" . $service->specialite . "_{$service->code_service}");
                break;
            case "menage":
                $pourAfficher = $this->messager->libelleGetReservation($service->infos);
                $this->setResponse($pourAfficher, $pourAfficher, "menuReservation_" . $service->specialite . "_{$service->code_service}");
                break;
            case "affiche":
            case "informatique":
                $pourAfficher = $this->messager->libelleGetDelaiReservationAfficheDev($service, TARIF_RESERVATION);
                $this->setResponse($pourAfficher, $pourAfficher, "menuReservationAfficheDev" . "_{$service->code_service}");
                break;
            case "electricite":
                $pourAfficher = $this->messager->libelleGetDelaiReservationElectricite($service);
                $this->setResponse($pourAfficher, $pourAfficher, "menuReserverElect_" . $service->specialite . "_{$service->code_service}");
                break;
        }
    }

    public function getReserverRM()
    {

        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();

        $code_service = $etal->page;
        $specialite = $etal->id_consultation;
        $service = $this->retourneService($code_service);
        if ($service != null) {
            switch ($this->content) {
                case '0':
                    $this->flowMenuPrincipal($service->precedent, 1);
                    break;
                case '1':
                    $pourAfficher = $this->messager->libelleGetDelaiReservation($service);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuDelaiReservation_" . $service->specialite . "_{$service->code_service}");
                    break;
                default:
                    $this->getRedirectionMenuGroupe($service);
                    break;
            }
        } else {
            $this->menuErreurService();
        }
        exit;
    }

    // public function getReserver()
    // {
    //     //$this->LOG(__FUNCTION__."  Executing Main menu rule");
    //     $etal = $this->getEtatLecture();
    //     print_r($etal);

    //     $page = $etal->page;
    //     $code_service = $etal->id_consultation;
    //     $specialite = $etal->context;

    //     $params = "";
    //     $params2 = "";
    //     $service = $this->retourneService($code_service);
    //     if ($service != null) {

    //         switch ($this->content) {
    //             case '0':
    //                 $this->flowContinueMain();
    //                 break;
    //             // case '0':
    //             //     // $groupement = $this->menuUssd->retourneGroupement($code_service);
    //             //     // print_r($groupement);
    //             //     $pourAfficher = $this->menuUssd->menuGroupe($code_service, $page - 1, $params);
    //             //     print_r($pourAfficher);
    //             //    // $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
    //             //     break;
    //             case '1':
    //                 $pourAfficher = $this->messager->libelleGetDelaiReservation($service);
    //                 $this->setResponse($pourAfficher, $pourAfficher, "menuDelaiReserver_" . $service->specialite . "_{$service->code_service}_1");

    //                 break;
    //             default:
    //                 $this->getRedirectionMenuGroupe($service);
    //                 break;
    //         }
    //         // $tableauSpecialite = $this->tableauSpecialite[$service->keyword];
    //         // $retMenu =  $this->menuSpecialite->getMenu($tableauSpecialite);
    //         // $menu = $service->libelle . "{CR}" . $retMenu;
    //         // $pourAfficher = new EtatLecture(1, $service->libelle, $retMenu);
    //         // $this->setResponse($pourAfficher, $pourAfficher, "menuSpecialite_" . $service->keyword . "_{$service->code_service}_1");
    //     } else {
    //         $this->menuErreurService();
    //     }
    // }



    public function getDelaiReservation()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        $code_service = $etal->page;
        $specialite = $etal->id_consultation;

        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $this->getRedirectionMenuGroupe($service);
                    break;
                case '1':
                    $this->getMenuReserverChoixDelai($service, "J2");
                    break;
                case '2':
                    $this->getMenuReserverChoixDelai($service, "J3");
                    break;
                case '3':
                    $this->getMenuReserverChoixDelai($service, "J7");
                    break;

                default:
                    $pourAfficher = $this->messager->libelleGetDelaiReservation($service);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuDelaiReservation_" . $service->specialite . "_{$service->code_service}");
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }


    public function getMenuReserverChoixDelai(Service $service, $delai)
    {
        $montant = TARIF_RESERVATION;
        $pourAfficher = $this->messager->libelleChoixForfaitReservation($service, $montant, $delai);
        $this->setResponse($pourAfficher, $pourAfficher, "menuChoixDelaiReservation_" . $delai . "_{$service->code_service}");
    }

    public function getMenuModeFacturation(Service $service, $delai, $choix = 0)
    {
        $montant = TARIF_RESERVATION;
        $pourAfficher = $this->messager->libelleModeFacturation($service, $montant, $delai);
        $this->setResponse($pourAfficher, $pourAfficher, "menuModeFacturation_" . $delai . "_{$service->code_service}_{$choix}");
    }



    public function getChoixDelaiReservation()
    {
        $etal = $this->getEtatLecture();
        $code_service = $etal->page;
        $delai = $etal->id_consultation;

        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $pourAfficher = $this->messager->libelleGetDelaiReservation($service);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuDelaiReservation_" . $service->specialite . "_{$service->code_service}");
                    break;
                case '1':
                    $this->getMenuModeFacturation($service, $delai);
                    break;
                case '2':
                    $this->menuFinParcours();
                    break;
                default:
                    $this->getMenuReserverChoixDelai($service, $delai);
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function getChoixModeFacturation()
    {
        $etal = $this->getEtatLecture();
        $delai = $etal->context;
        $code_service = $etal->id_consultation;
        $choix = $etal->page;

        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    if ($choix == 0)
                        $this->getMenuReserverChoixDelai($service, $delai);
                    else
                        $this->getMenuReserverChoixDelai($service, $delai);
                    break;
                case '1':

                    $pourAfficher = $this->messager->libelleInviteAM($service, TARIF_RESERVATION);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuInviteAM_" . $service->code_service . "_{$delai}_{$choix}");

                    break;
                default:
                    $this->getMenuModeFacturation($service, $delai, $choix);
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function getChoixReservationAfficheDev()
    {
        $etal = $this->getEtatLecture();
        $code_service = $etal->page;
        $delai = $etal->id_consultation;

        $service = $this->retourneService($code_service);
        if ($service != null) {


            switch ($this->content) {
                case '0':
                    //    $this->getRedirectionMenuGroupe($service);
                    $this->flowMenuPrincipal($service->precedent, 1);
                    break;
                case '1':
                    $this->getMenuModeFacturation($service, 0);
                    break;
                case '2':
                    $this->menuFinParcours();
                    break;
                default:
                    $pourAfficher = $this->messager->libelleGetDelaiReservationAfficheDev($service, TARIF_RESERVATION);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuReservationAfficheDev" . "_{$service->code_service}");

                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function getReserverElectricite()
    {
        $etal = $this->getEtatLecture();
        $page = $etal->page;
        $code_service = $etal->page;
        $specialite = $etal->id_consultation;


        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $this->flowMenuPrincipal($service->precedent, 1);
                    break;
                case '1':
                    $this->AfficheSpecialiteElectricite($service, "H24");
                    break;
                case '2':
                    $this->AfficheSpecialiteElectricite($service, "J3");
                    break;
                case '3':
                    $this->AfficheSpecialiteElectricite($service, "J7");
                    break;

                default:
                    $this->getRedirectionMenuGroupe($service);

                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function AfficheSpecialiteElectricite(Service $service, $delai)
    {
        if ($service->specialite == "OUI") {
            $tableauSpecialite = $this->tableauSpecialite[$service->keyword];
            $retMenu = $this->menuSpecialite->getMenu($tableauSpecialite);
            $pourAfficher = new EtatLecture(1, $service->libelle . "{CR}Faire un choix :", $retMenu);

            $this->setResponse($pourAfficher, $pourAfficher, "menuSpecialiteElect_" . $service->code_service . "_{$delai}");
        } else {
            $this->flowMenuPrincipal($service->precedent, 1);
        }
    }

    public function getReserverSpecialiteElectricite()
    {
        $etal = $this->getEtatLecture();
        $code_service = $etal->id_consultation;
        $delai = $etal->page;


        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $this->getRedirectionMenuGroupe($service);
                    break;
                default:
                    $content = strtolower($this->content);
                    $tableauSpecialite = $this->tableauSpecialite[$service->keyword];
                    // if ($choix = $this->menuSpecialite->getChoix($tableauSpecialite, $content)) {
                    //     $choix = $choix["code_service"];
                    //     $pourAfficher = $this->messager->libelleChoixForfaitReservationElectricite($service, TARIF_RESERVATION, $delai);
                    //     $this->setResponse($pourAfficher, $pourAfficher, "menuConfirSpecialiteElect_" . $service->code_service . "_{$delai}_{$choix}");
                    // } else {
                    //     $this->AfficheSpecialiteElectricite($service, $delai);
                    // }
                    $this->getRedirectionMenuSpecialiteElectricite($service, $content, $delai);
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function getRedirectionMenuSpecialiteElectricite(Service $service, $content, $delai)
    {
        $tableauSpecialite = $this->tableauSpecialite[$service->keyword];
        if ($choix = $this->menuSpecialite->getChoix($tableauSpecialite, $content)) {
            $choix = $choix["code_service"];
            $pourAfficher = $this->messager->libelleChoixForfaitReservationElectricite($service, TARIF_RESERVATION, $delai);
            $this->setResponse($pourAfficher, $pourAfficher, "menuConfirSpecialiteElect_" . $service->code_service . "_{$delai}_{$choix}");
        } else {
            $this->AfficheSpecialiteElectricite($service, $delai);
        }
    }

    public function getConfirSpecialiteElect()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        $delai = $etal->id_consultation;
        $code_service = $etal->context;
        $choix = $etal->page;

        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $this->AfficheSpecialiteElectricite($service, $delai);
                    break;
                case '1':
                    $this->getMenuModeFacturation($service, $delai, $choix);
                    break;
                case '2':
                    $this->menuFinParcours();
                    break;
                default:

                    $tableauSpecialite = $this->tableauSpecialite[$service->keyword]["$choix"];
                    $choix = $tableauSpecialite["code_service"];
                    $pourAfficher = $this->messager->libelleChoixForfaitReservationElectricite($service, TARIF_RESERVATION, $delai);
                    $this->setResponse($pourAfficher, $pourAfficher, "menuConfirSpecialiteElect_" . $service->code_service . "_{$delai}_{$choix}");
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }

    public function getConfirmationPaiementAM()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        $delai = $etal->id_consultation;
        $code_service = $etal->context;
        $choix = $etal->page;

        // print_r($etal);
        $service = $this->retourneService($code_service);
        if ($service != null) {

            switch ($this->content) {
                case '0':
                    $this->flowContinueMain();
                    break;
                default:

                    if (ctype_digit($this->content) && strlen($this->content) == LONGUEUR_CODE_PAIEMENT) {
                        // print "affichage de la facture" . PHP_EOL;
                        $this->getFacturationService($service, $delai, $choix);
                    } else {
                        $pourAfficher = $this->messager->libelleInviteAM($service, TARIF_RESERVATION);
                        $this->setResponse($pourAfficher, $pourAfficher, "menuInviteAM_" . $service->code_service . "_{$delai}_{$choix}");
                    }
                    break;
            }
        } else {
            $this->menuErreurService();
        }
    }


    public function getFacturationService(Service $service, $delai, $choix)
    {

        $service->montant = TARIF_RESERVATION;
        $codePin = trim($this->content);
        $autentification = $this->getAuthentification($codePin, $service);
        if ($autentification == true) {
            $ref_reservation = $this->generateReference($service);


            $delai = str_replace('J', '', $delai);
            $delai = str_replace('H', '', $delai);

            $heure = "09:00";

            if ($delai == "24") {
                $hh = 10;
                if ($hh <= 16) {
                    $date_rdv = date('Y-m-d');
                    $heure = date('H:i', strtotime("+1 hours"));
                } else {
                    $delai = "1";
                    $date_rdv = date('Y-m-d', strtotime("+$delai days"));
                }
            } else {
                $date_rdv = date('Y-m-d', strtotime("+$delai days"));
            }


            if ($service->specialite == "OUI") {
                $tableauSpecialite = $this->tableauSpecialite[$service->keyword]["$choix"];
                $keywordSpecialite = $tableauSpecialite["keyword"];
                $codeSpecialite = $tableauSpecialite["code_service"];
            } else {
                $keywordSpecialite = null;
                $codeSpecialite = null;
            }

            $reservation = new Reservation($this->link);
            $data = array(
                'reference' => $ref_reservation,
                'client_nom' => "client n." . $this->telephone,
                'telephone' => $this->telephone,
                'service_id' => $service->service_id,
                'service_libelle' => $service->libelle,
                'montant' => $service->montant,
                'delai' => $delai,
                'choix' => $choix,
                'paiement_statut' => 1,
                'statut' => 1,
                'date_rdv' => $date_rdv,
                'specialite' => $codeSpecialite,
                'description' => $keywordSpecialite,
                'code_pin' => $codePin
            );

            $reservation_id = $reservation->InsertReservation($data);

            $transaction_id = "1";
            $status_paie = '1';
            $raw_response = "SUCCESS";
            $operateur = "AIRTEL";
            $paiement_id = $reservation->insertPaiement($reservation_id, $transaction_id, $service->montant, $status_paie, $raw_response, $operateur);

            $pourAfficher = $this->messager->libelleReservationOK($service, $delai, $date_rdv, $heure);

            $this->setResponse($pourAfficher, $pourAfficher, "menu");
        } else {
            $pourAfficher = $this->messager->creditInsuffisant($service);
            $this->setResponse($pourAfficher, $pourAfficher, "menu", "FB");
        }
    }

    public function getAuthentification($codePin, Service $service)
    {
        return true;
    }

    public function getMenuInvitGenererFacture()
    {
        $pourAfficher = new EtatLecture(1, "Saisir le numero du client", "0.Retour");
        $this->setResponse($pourAfficher, $pourAfficher, "genererFacture");
    }
    public function getGenererFacture()
    {
        switch ($this->content) {
            case '0':
                $this->flowContinueMain();
                break;
            default:

                if (ctype_digit($this->content) && strlen($this->content) >= LONGUEUR_NUMERO) {
                    // print "traitement du numero" . PHP_EOL;
                    $numero = trim($this->content);
                    $pourAfficher = $this->menuUssd->menuGroupe(1, 1, null, "Saisir le service");
                    $this->setResponse($pourAfficher, $pourAfficher, "getGenererFactureService_" . $numero . "_1_1");
                } else {
                    $this->getMenuInvitGenererFacture();
                }
                break;
        }
    }
    public function getMenuGenererFacture()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        print_r($etal);
        $page = $etal->page;
        $id_groupement = $etal->id_consultation;
        $numeroClient = $etal->context;
        $id_context = $numeroClient;
        $libelle = $etal->renew;
        $params = null;
        $params2 = null;

        switch ($this->content) {
            case '0':
                $this->getMenuInvitGenererFacture();
                break;

            default:
                $pourAfficher = $this->menuUssd->menuGroupe($id_groupement, $page, $params, "Saisir le service");
                if (dbAccess::estEntier($this->content, 1)) {
                    $ideff = $this->retourneIdEffectifR($this->content, "select id_menu from menus_ussd where precedent=$id_groupement and is_active='1' $params order by position ASC");
                    // debug("//$ideff//");
                    if ($ideff == null)
                        $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                    else {

                        $afficho = $this->menuUssd->menuGroupe($ideff, 1, $params, $params);
                        if ($afficho->present > 1) {
                            // debug("yyyyyyyyyyyyyy");
                            $this->setResponse($afficho, $afficho, $libelle . "_" . $id_context . "_{$ideff}_1");
                        } else {
                            // debug("zzzzzzzzzzzz");
                            $afficho = $this->menuUssd->menuGroupe($ideff, 1, $params2);
                            // print_r($afficho);
                            if ($afficho->present > 1) {
                                $this->setResponse($afficho, $afficho, "debut_" . $id_context . "_{$ideff}_1");
                            } elseif ($afficho->present == 1) {
                                // echo "jjjjjjjj";
                                $ideff = $this->retourneIdEffectifR(1, "select code_service from services where precedent=$ideff and statut='yes' order by position");
                                // if ($ideff != NULL) {
                                //     $service = $this->retourneService($ideff);
                                //     if ($service->external != "YES") {
                                //         $this->flowContinueMain($ideff, $id_context);
                                //     } else {
                                //         $next2 = $this->ussdHttp($service->url_central, "menu", $this->telephone, $this->content, $this->sessionId, $service->code_service, $id_context);
                                //         $this->setNext("", "", $service->url_central);
                                //         if ($next2->freeFlow_ext != "FC") {
                                //             $this->setResponse(NEXT_EXTERNAL, new EtatLecture(1, $next2->contenu_ext), $next2->next_ext, "FB");
                                //         } else {
                                //             if ($next2->next_ext != EXTERNAL)
                                //                 $this->setResponse(NEXT_EXTERNAL, new EtatLecture(1, $next2->contenu_ext), $next2->next_ext);
                                //             else
                                //                 $this->setResponse(__FUNCTION__, $this->menuUssd->menuGroupe(0, 1, $params), $libelle . "_" . $id_context . "_0_1");
                                //         }
                                //     }
                                // } else {
                                //     $next = $afficho->context->id_menu;
                                //     $this->setResponse($afficho, $afficho, $libelle . "_" . $id_context . "_{$next}_1");
                                // }
                            } else {
                                print "xxxxxxxx";
                                $service = $this->retourneService($ideff);
                                if ($service != null) {
                                    print_r($service);

                                    $forfait = $this->retourneForfait($service->service_id, "id_service");
                                    if ($forfait != null) {
                                        print_r($forfait);
                                        if ($forfait->tarif == "devis") {
                                            $libelle = $forfait->affichage . "{CR}Merci de saisir le montant de la prestation";
                                            $pourAfficher = new EtatLecture(1, $libelle, "0.Retour");
                                            $this->setResponse(__FUNCTION__, $pourAfficher, "designationFacturePrestation_" . $numeroClient . "_{$ideff}_1");
                                        } else {
                                            $libelle = $forfait->affichage . "{CR}Merci de saisir le nombre de {$forfait->souscription_aff}";
                                            $pourAfficher = new EtatLecture(1, $libelle, "0.Retour");
                                            $this->setResponse(__FUNCTION__, $pourAfficher, "designationPrestation_" . $numeroClient . "_{$ideff}_1");
                                        }
                                    } else {
                                        $this->menuErreur();
                                    }
                                } else {
                                    $this->menuErreur();
                                }
                            }
                        }
                    }
                } else {
                    $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                }
                break;
        }
    }


    public function getMenuDesignationPrestation()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        print_r($etal);
        $page = $etal->page;
        $code_service = $etal->id_consultation;
        $numeroClient = $etal->context;
        $libelle = $etal->renew;

        $service = $this->retourneService($code_service);
        if ($service != null) {


            $forfait = $this->retourneForfait($service->service_id, "id_service");
            if ($forfait != null) {
                //print_r($forfait);

                switch ($this->content) {
                    case '0':
                        $pourAfficher = $this->menuUssd->menuGroupe(1, 1, null, "Saisir le service");
                        $this->setResponse($pourAfficher, $pourAfficher, "getGenererFactureService_" . $numeroClient . "_1_1");
                        break;
                    default:

                        if (ctype_digit($this->content)) {

                            if ($service->keyword === "menage") {
                                $nombre = 1;
                                if (DESIGNATION_MENAGE > 0) {
                                    $nombre = max(1, ceil($this->content / DESIGNATION_MENAGE));
                                }
                            } else $nombre = $this->content;

                            $montant = $forfait->tarif * $nombre;
                            $service->montant = $montant;
                            $designation = trim($this->content);

                            $libelle = "Merci de confirmer la prestation {CR}{$service->libelle} ({$forfait->affichage}){CR}Details: {$designation} {$forfait->souscription_aff}{CR}Total : {$montant} FCFA";
                            $pourAfficher = new EtatLecture(1, $libelle, "1. OUI{CR}2. NON {CR}0.Retour");

                            $next = "getConfirmationDesignationPrestation_" . $numeroClient . "_{$service->service_id}_{$service->montant}_{$designation}_" . $nombre;
                            //"getConfirmationDesignationPrestation_" . $numeroClient . "_{$service->service_id}_{$service->montant}_1"
                            $this->setResponse(__FUNCTION__, $pourAfficher, $next);
                        } else {
                            $libelle = $forfait->affichage . "{CR}Merci de saisir le nombre de {$forfait->souscription_aff}";
                            $pourAfficher = new EtatLecture(1, $libelle, "0. Retour");
                            $this->setResponse(__FUNCTION__, $pourAfficher, "designationPrestation_" . $numeroClient . "_{$service->service_id}_1");
                        }

                        break;
                }
            } else {
                $this->menuErreur();
            }
        } else {
            $this->menuErreur();
        }
    }

    public function getMenuConfirmationDesignationPrestation()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        print_r($etal);
        //getConfirmationDesignationPrestation_1111111111111111_4_5000_2_1

        $numeroClient = $etal->present;
        $designation = $etal->id_consultation;
        $montant = $etal->context;
        $code_service = $etal->renew;
        $nombre = $etal->page;

        $service = $this->retourneService($code_service);
        if ($service != null) {
            $forfait = $this->retourneForfait($service->service_id, "id_service");
            if ($forfait != null) {

                $montant = $forfait->tarif * $nombre;
                $service->montant = $montant;

                switch ($this->content) {
                    case '0':
                        $libelle = $forfait->affichage . "{CR}Merci de saisir le nombre de {$forfait->souscription_aff}";
                        $pourAfficher = new EtatLecture(1, $libelle, "0. Retour");
                        $this->setResponse(__FUNCTION__, $pourAfficher, "designationPrestation_" . $numeroClient . "_{$service->service_id}_1");
                        break;
                    case '1':
                        $this->genererFacturePrestation($service, $forfait, $numeroClient, $designation, $nombre);
                        break;
                    case '2':
                        $this->menuFinParcours();
                        break;
                    default:

                        $libelle = "Merci de confirmer la prestation {CR}{$service->libelle} ({$forfait->affichage}){CR}Details: {$designation} {$forfait->souscription_aff}{CR}Total : {$montant} FCFA";
                        $pourAfficher = new EtatLecture(1, $libelle, "1. OUI{CR}2. NON {CR}0.Retour");

                        $next = "getConfirmationDesignationPrestation_" . $numeroClient . "_{$service->service_id}_{$service->montant}_{$designation}_" . $nombre;
                        $this->setResponse(__FUNCTION__, $pourAfficher, $next);
                        break;
                }
            } else {
                $this->menuErreur();
            }
        } else {
            $this->menuErreur();
        }
    }


    public function genererFacturePrestation(Service $service, $forfait, $numeroClient, $designation, $nombre)
    {
        

    //inserer facture et notifier le client

    $sqlQuery = "INSERT INTO facture (id_client, id_service, montant, designation, nombre) VALUES ({$numeroClient}, {$service->service_id}, {$service->montant}, '{$designation}', {$nombre})";
    //$status = $this->db->db_executeQuery($sqlQuery);
    $this->menuFinParcours();

    }

    public function getGroupement()
    {
        //$this->LOG(__FUNCTION__."  Executing Main menu rule");
        $etal = $this->getEtatLecture();
        $page = $etal->page;
        $id_groupement = $etal->id_consultation;
        $id_context = $etal->context;
        $libelle = $etal->renew;
        $params = "";
        $params2 = "";

        switch ($this->content) {
            case '0':
                if ($page == 1 and $id_groupement != 0) {
                    $groupement = $this->menuUssd->retourneGroupement($id_groupement);
                    $pourAfficher = $this->menuUssd->menuGroupe($groupement->precedent, 1, $params);
                    while ($pourAfficher->present <= 1 and $groupement->precedent != 0) {
                        $groupement = $this->menuUssd->retourneGroupement($groupement->id_groupement);
                        $pourAfficher = $this->menuUssd->menuGroupe($groupement->precedent, 1, $params);
                    }
                    if ($pourAfficher->present > 1) {
                        $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$groupement->precedent}_" . $pourAfficher->page);
                    } else {
                        $this->flowContinueMain();
                    }
                } elseif ($page == 1 and $id_context == 0) {
                    $this->flowContinueMain();
                } else {
                    $pourAfficher = $this->menuUssd->menuGroupe($id_groupement, $page - 1, $params);
                    $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                }
                break;
            case NOMBRE_SUIVANT:
                $pourAfficher = $this->menuUssd->menuGroupe($id_groupement, $page + 1, $params);
                $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                break;
            default:
                $pourAfficher = $this->menuUssd->menuGroupe($id_groupement, $page, $params);
                if (dbAccess::estEntier($this->content, 1)) {
                    $ideff = $this->retourneIdEffectifR($this->content, "select id_menu from menus_ussd where precedent=$id_groupement and is_active='1' $params order by position ASC");
                    debug("//$ideff//");
                    if ($ideff == null)
                        $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                    else {
                        $afficho = $this->menuUssd->menuGroupe($ideff, 1, $params, $params);
                        if ($afficho->present > 1) {
                            // debug("yyyyyyyyyyyyyy");
                            $this->setResponse($afficho, $afficho, $libelle . "_" . $id_context . "_{$ideff}_1");
                        } else {
                            // debug("zzzzzzzzzzzz");
                            $afficho = $this->menuUssd->menuGroupe($ideff, 1, $params2);
                            // print_r($afficho);
                            if ($afficho->present > 1) {
                                $this->setResponse($afficho, $afficho, "debut_" . $id_context . "_{$ideff}_1");
                            } elseif ($afficho->present == 1) {
                                // echo "jjjjjjjj";
                                $ideff = $this->retourneIdEffectifR(1, "select code_service from services where precedent=$ideff and statut='yes' order by position");
                                // if ($ideff != NULL) {
                                //     $service = $this->retourneService($ideff);
                                //     if ($service->external != "YES") {
                                //         $this->flowContinueMain($ideff, $id_context);
                                //     } else {
                                //         $next2 = $this->ussdHttp($service->url_central, "menu", $this->telephone, $this->content, $this->sessionId, $service->code_service, $id_context);
                                //         $this->setNext("", "", $service->url_central);
                                //         if ($next2->freeFlow_ext != "FC") {
                                //             $this->setResponse(NEXT_EXTERNAL, new EtatLecture(1, $next2->contenu_ext), $next2->next_ext, "FB");
                                //         } else {
                                //             if ($next2->next_ext != EXTERNAL)
                                //                 $this->setResponse(NEXT_EXTERNAL, new EtatLecture(1, $next2->contenu_ext), $next2->next_ext);
                                //             else
                                //                 $this->setResponse(__FUNCTION__, $this->menuUssd->menuGroupe(0, 1, $params), $libelle . "_" . $id_context . "_0_1");
                                //         }
                                //     }
                                // } else {
                                //     $next = $afficho->context->id_menu;
                                //     $this->setResponse($afficho, $afficho, $libelle . "_" . $id_context . "_{$next}_1");
                                // }
                            } else {
                                print "xxxxxxxx";
                                $this->flowContinueMain($ideff, $id_context);
                            }
                        }
                    }
                } else {
                    $this->setResponse($pourAfficher, $pourAfficher, $libelle . "_" . $id_context . "_{$id_groupement}_" . $pourAfficher->page);
                }
                break;
        }
    }
}
