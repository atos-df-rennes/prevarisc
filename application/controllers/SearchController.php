<?php

class SearchController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->redirector('etablissement');
    }

    public function etablissementAction()
    {
        $this->_helper->layout->setLayout('search');

        // Gestion droit export Calc
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $this->view->is_allowed_export_calc = unserialize($cache->load('acl'))->isAllowed(Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'], "export", "export_ets");

        $service_search = new Service_Search;

        $service_genre = new Service_Genre;
        $service_statut = new Service_Statut;
        $service_avis = new Service_Avis;
        $service_categorie = new Service_Categorie;
        $service_typeactivite = new Service_TypeActivite;
        $service_famille = new Service_Famille;
        $service_classe = new Service_Classe;
        $service_commission = new Service_Commission;

        $this->view->DB_genre = $service_genre->getAll();
        $this->view->DB_statut = $service_statut->getAll();
        $this->view->DB_avis = $service_avis->getAll();
        $this->view->DB_classe = $service_classe->getAll();
        $this->view->DB_categorie = $service_categorie->getAll();
        $this->view->DB_typeactivite = $service_typeactivite->getAllWithTypes();
        $this->view->DB_famille = $service_famille->getAll();
        $this->view->DB_commission = $service_commission->getAll();

        if($this->_request->isGet() && count($this->_request->getQuery()) > 0) {
            if (!empty($_GET)) {
                // Export Calc
                if (isset($_GET['Exporter'])) {
                    try {
                        $parameters = $this->_request->getQuery();
                        $page = array_key_exists('page', $parameters) ? $parameters['page'] : null;
                        $label = array_key_exists('label', $parameters) && $parameters['label'] != '' && (string) $parameters['label'][0] != '#' ? $parameters['label'] : null;
                        $identifiant = array_key_exists('label', $parameters) && $parameters['label'] != '' && (string) $parameters['label'][0] == '#'? substr($parameters['label'], 1) : null;
                        $genres = array_key_exists('genres', $parameters) ? $parameters['genres'] : null;
                        $categories = array_key_exists('categories', $parameters) ? $parameters['categories'] : null;
                        $classes = array_key_exists('classes', $parameters) ? $parameters['classes'] : null;
                        $familles = array_key_exists('familles', $parameters) ? $parameters['familles'] : null;
                        $types_activites = array_key_exists('types_activites', $parameters) ? $parameters['types_activites'] : null;
                        $avis_favorable = array_key_exists('avis', $parameters) && count($parameters['avis']) == 1 ? $parameters['avis'][0] == 'true' : null;
                        $statuts = array_key_exists('statuts', $parameters) ? $parameters['statuts'] : null;
                        $local_sommeil = array_key_exists('presences_local_sommeil', $parameters) && count($parameters['presences_local_sommeil']) == 1 ? $parameters['presences_local_sommeil'][0] == 'true' : null;
                        $city = array_key_exists('city', $parameters) && $parameters['city'] != '' ? $parameters['city'] : null;
                        $street = array_key_exists('street', $parameters) && $parameters['street'] != '' ? $parameters['street'] : null;
                        $number = array_key_exists('number', $parameters) && $parameters['number'] != '' ? $parameters['number'] : null;
                        $commissions = array_key_exists('commissions', $parameters) && $parameters['commissions'] != '' ? $parameters['commissions'] : null;

                        $search = $service_search->extractionEtablissements($label, $identifiant, $genres, $categories, $classes, $familles, $types_activites, $avis_favorable, $statuts, $local_sommeil, null, null, null, $city, $street, $number, $commissions);

                        $objPHPExcel = new PHPExcel ();
                        $objPHPExcel->setActiveSheetIndex ( 0 );
                        $sheet = $objPHPExcel->getActiveSheet ();
                        $sheet->setTitle ( 'Liste des établissements' );
                         
                        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(10)->setBold(false);
                        $sheet->getDefaultRowDimension()->setRowHeight(-1);
                         
                        // Formattage des titres de colonnes
                        $styleArray = array(
                                'borders' => array(
                                        'allborders' => array(
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                        )
                                )
                        );
                        $sheet->getStyle('A1:V1')->applyFromArray($styleArray);
                        unset($styleArray);
                        $sheet->getStyle('A1:V1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A1:V1')->getFont()->setSize(11)->setBold(true);
                         
                        foreach(range('A','V') as $columnID) {
                            $sheet->getColumnDimension($columnID)->setAutoSize(true);
                        }
                         
                        $sheet->setCellValueByColumnAndRow ( 0, 1, "Commune" );
                        $sheet->setCellValueByColumnAndRow ( 1, 1, "Catégorie" );
                        $sheet->setCellValueByColumnAndRow ( 2, 1, "Type" );
                        $sheet->setCellValueByColumnAndRow ( 3, 1, "Activité" );
                        $sheet->setCellValueByColumnAndRow ( 4, 1, "Commission compétente" );
                        $sheet->setCellValueByColumnAndRow ( 5, 1, "Code/identifiant établissement" );
                        $sheet->setCellValueByColumnAndRow ( 6, 1, "Libellé établissement" );
                        $sheet->setCellValueByColumnAndRow ( 7, 1, "Statut" );
                        $sheet->setCellValueByColumnAndRow ( 8, 1, "Avis" );
                        $sheet->setCellValueByColumnAndRow ( 9, 1, "Date du dernier avis" );
                        $sheet->setCellValueByColumnAndRow ( 10, 1, "Date du premier avis favorable" );
                        $sheet->setCellValueByColumnAndRow ( 11, 1, "Date du premier avis défavorable consécutif" );
                        $sheet->setCellValueByColumnAndRow ( 12, 1, "Effectif total" );
                        $sheet->setCellValueByColumnAndRow ( 13, 1, "Effectif public" );
                        $sheet->setCellValueByColumnAndRow ( 14, 1, "Effectif personnel" );
                        $sheet->setCellValueByColumnAndRow ( 15, 1, "Date de dernière visite" );
                        $sheet->setCellValueByColumnAndRow ( 16, 1, "Date de prochaine visite périodique" );
                        $sheet->setCellValueByColumnAndRow ( 17, 1, "Date de visite prévue" );
                        $sheet->setCellValueByColumnAndRow ( 18, 1, "Adresse" );
                        $sheet->setCellValueByColumnAndRow ( 19, 1, "Libellé du père/site" );
                        $sheet->setCellValueByColumnAndRow ( 20, 1, "Genre" );
                        $sheet->setCellValueByColumnAndRow ( 21, 1, "Préventionniste" );
                         
                        $ligne = 2;
                        foreach ($search['results'] as $row) {
                            $sheet->setCellValueByColumnAndRow ( 0, $ligne, $row ['LIBELLE_COMMUNE'] );
                            $sheet->setCellValueByColumnAndRow ( 1, $ligne, $row ['LIBELLE_CATEGORIE'] );
                            $sheet->setCellValueByColumnAndRow ( 2, $ligne, $row ['LIBELLE_TYPE'] );
                            $sheet->setCellValueByColumnAndRow ( 3, $ligne, $row ['LIBELLE_ACTIVITE'] );
                            $sheet->setCellValueByColumnAndRow ( 4, $ligne, $row ['LIBELLE_COMMISSION'] );
                            $sheet->setCellValueByColumnAndRow ( 5, $ligne, $row ['NUMEROID_ETABLISSEMENT'] );
                            $sheet->setCellValueByColumnAndRow ( 6, $ligne, $row ['LIBELLE_ETABLISSEMENTINFORMATIONS'] );
                            $sheet->setCellValueByColumnAndRow ( 7, $ligne, $row ['LIBELLE_STATUT'] );
                            $sheet->setCellValueByColumnAndRow ( 8, $ligne, $row ['LIBELLE_AVIS'] );
                             
                            if ($row ['DATE_DERNIER_AVIS'] != '') {
                                $dateDernierAvis = explode("-",$row ['DATE_DERNIER_AVIS']);
                                $datetimeDernierAvis = PHPExcel_Shared_Date::FormattedPHPToExcel($dateDernierAvis[0], $dateDernierAvis[1], $dateDernierAvis[2]);
                                $sheet->setCellValueByColumnAndRow ( 9, $ligne, $datetimeDernierAvis );
                                $sheet->getStyleByColumnAndRow( 9, $ligne)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
                            }

                            if ($row ['DATE_PREMIER_AVIS_FAVORABLE'] != '') {
                                $datePremierAvisFavorable = explode ( "-", $row ['DATE_PREMIER_AVIS_FAVORABLE'] );
                                $datePremierAvisFavorable = PHPExcel_Shared_Date::FormattedPHPToExcel ( $datePremierAvisFavorable [0], $datePremierAvisFavorable [1], $datePremierAvisFavorable [2] );
                                $sheet->setCellValueByColumnAndRow ( 10, $ligne, $datePremierAvisFavorable );
                                $sheet->getStyleByColumnAndRow ( 10, $ligne )->getNumberFormat ()->setFormatCode ( PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY );
                            }
                             
                            if ($row ['DATE_PREMIER_AVIS_DEFAVORABLE_CONSECUTIF'] != '') {
                                $datePremierAvisDefavorableConsecutif = explode("-",$row ['DATE_PREMIER_AVIS_DEFAVORABLE_CONSECUTIF']);
                                $datetimePremierAvisDefavorableConsecutif = PHPExcel_Shared_Date::FormattedPHPToExcel($datePremierAvisDefavorableConsecutif[0], $datePremierAvisDefavorableConsecutif[1], $datePremierAvisDefavorableConsecutif[2]);
                                $sheet->setCellValueByColumnAndRow ( 11, $ligne, $datetimePremierAvisDefavorableConsecutif );
                                $sheet->getStyleByColumnAndRow( 11, $ligne)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
                            }
                             
                            $sheet->setCellValueByColumnAndRow ( 12, $ligne, $row ['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'] + $row ['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'] );
                            $sheet->setCellValueByColumnAndRow ( 13, $ligne, $row ['EFFECTIFPUBLIC_ETABLISSEMENTINFORMATIONS'] );
                            $sheet->setCellValueByColumnAndRow ( 14, $ligne, $row ['EFFECTIFPERSONNEL_ETABLISSEMENTINFORMATIONS'] );
                             
                            if ($row ['DATE_DERNIERE_VISITE'] != '') {
                                $dateDerniereVisite = explode("-",$row ['DATE_DERNIERE_VISITE']);
                                $datetimeDerniereVisite = PHPExcel_Shared_Date::FormattedPHPToExcel($dateDerniereVisite[0], $dateDerniereVisite[1], $dateDerniereVisite[2]);
                                $sheet->setCellValueByColumnAndRow ( 15, $ligne, $datetimeDerniereVisite );
                                $sheet->getStyleByColumnAndRow( 15, $ligne)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
                                 
                                if($row ['PERIODICITE_ETABLISSEMENTINFORMATIONS'] != 0) {
                                     
                                    $dateProchaineVisite = date ( 'Y-m-j' , strtotime("+".$row ['PERIODICITE_ETABLISSEMENTINFORMATIONS']." months", strtotime($row ['DATE_DERNIERE_VISITE'])) );
                                    $dateProchaineVisite = explode("-",$dateProchaineVisite);
                                    $datetimeProchaineVisite = PHPExcel_Shared_Date::FormattedPHPToExcel($dateProchaineVisite[0], $dateProchaineVisite[1], $dateProchaineVisite[2]);
                                    $sheet->setCellValueByColumnAndRow(16, $ligne, $datetimeProchaineVisite);
                                    $sheet->getStyleByColumnAndRow(16, $ligne)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
                                }
                            }
                
                            if ($row ['DATE_VISITE_PREVUE'] != '') {
                                $dateVisitePrevue = explode ( "-", $row ['DATE_VISITE_PREVUE'] );
                                $datetimeVisitePrevue = PHPExcel_Shared_Date::FormattedPHPToExceel ( $dateVisitePrevue [0], $dateVisitePrevue [1], $dateVisitePrevue [2] );
                                $sheet->setCellValueByColumnAndRow ( 17, $ligne, $datetimeVisitePrevue );
                                $sheet->getStyleByColumnAndRow ( 17, $ligne )->getNumberFormat ()->setFormatCode ( PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY );
                            }
                            
                            $sheet->setCellValueByColumnAndRow ( 18, $ligne, $row ['NUMERO_ADRESSE'] . " " . $row ['LIBELLE_RUE'] . " " . $row ['COMPLEMENT_ADRESSE'] . " " . $row ['CODEPOSTAL_COMMUNE'] );
                            $sheet->setCellValueByColumnAndRow ( 19, $ligne, $row ['LIBELLE_ETABLISSEMENT_PERE'] );
                            $sheet->setCellValueByColumnAndRow ( 20, $ligne, $row ['LIBELLE_GENRE'] );
                            $sheet->setCellValueByColumnAndRow ( 21, $ligne, $row ['PRENOM_UTILISATEURINFORMATIONS'] . " " . $row ['NOM_UTILISATEURINFORMATIONS'] );
                             
                            $ligne ++;
                        }
                         
                        $this->view->writer = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
                         
                        // Ensuite j'ai choisi de désactiver mon layout
                        $this->_helper->layout ()->disableLayout ();
                         
                        header("Content-Type: application/vnd.oasis.opendocument.spreadsheet");
                        header("Content-Disposition: attachment; filename=\"Export_Etablissements_".date('Y-m-d_H-i-s').".ods\"");
                        $this->view->writer->save('php://output');
                        exit();
                         
                    } catch ( Exception $e ) {
                        $this->_helper->flashMessenger ( array (
                                'context' => 'error',
                                'title' => 'Problème d\'export',
                                'message' => 'L\'export a rencontré un problème. Veuillez rééssayez. (' . $e->getMessage () . ')'
                        ) );
                    }
                     
                } else {
                // Recherche
                    try {
                        $parameters = $this->_request->getQuery();
                        $page = array_key_exists('page', $parameters) ? $parameters['page'] : null;
                        $label = array_key_exists('label', $parameters) && $parameters['label'] != '' && (string) $parameters['label'][0] != '#' ? $parameters['label'] : null;
                        $identifiant = array_key_exists('label', $parameters) && $parameters['label'] != '' && (string) $parameters['label'][0] == '#'? substr($parameters['label'], 1) : null;
                        $genres = array_key_exists('genres', $parameters) ? $parameters['genres'] : null;
                        $categories = array_key_exists('categories', $parameters) ? $parameters['categories'] : null;
                        $classes = array_key_exists('classes', $parameters) ? $parameters['classes'] : null;
                        $familles = array_key_exists('familles', $parameters) ? $parameters['familles'] : null;
                        $types_activites = array_key_exists('types_activites', $parameters) ? $parameters['types_activites'] : null;
                        $avis_favorable = array_key_exists('avis', $parameters) && count($parameters['avis']) == 1 ? $parameters['avis'][0] == 'true' : null;
                        $statuts = array_key_exists('statuts', $parameters) ? $parameters['statuts'] : null;
                        $local_sommeil = array_key_exists('presences_local_sommeil', $parameters) && count($parameters['presences_local_sommeil']) == 1 ? $parameters['presences_local_sommeil'][0] == 'true' : null;
                        $city = array_key_exists('city', $parameters) && $parameters['city'] != '' ? $parameters['city'] : null;
                        $street = array_key_exists('street', $parameters) && $parameters['street'] != '' ? $parameters['street'] : null;
                        $number = array_key_exists('number', $parameters) && $parameters['number'] != '' ? $parameters['number'] : null;
                        $commissions = array_key_exists('commissions', $parameters) && $parameters['commissions'] != '' ? $parameters['commissions'] : null;
                        
                        $search = $service_search->etablissements($label, $identifiant, $genres, $categories, $classes, $familles, $types_activites, $avis_favorable, $statuts, $local_sommeil, null, null, null, $city, $street, $number, $commissions, 50, $page);

                        $paginator = new Zend_Paginator(new SDIS62_Paginator_Adapter_Array($search['results'], $search['search_metadata']['count']));
                        $paginator->setItemCountPerPage(50)->setCurrentPageNumber($page)->setDefaultScrollingStyle('Elastic');

                        $this->view->results = $paginator;
                    }
                    catch(Exception $e) {
                        $this->_helper->flashMessenger(array('context' => 'error','title' => 'Problème de recherche','message' => 'La recherche n\'a pas été effectuée correctement. Veuillez réessayer. (' . $e->getMessage() . ')'));
                    }
                }
            }
        }
    }

    public function dossierAction()
    {
        $this->_helper->layout->setLayout('search');

        // Gestion droit export Calc
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $this->view->is_allowed_export_calc = unserialize($cache->load('acl'))->isAllowed(Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'], "export", "export_doss");

        $service_search = new Service_Search;
        $service_commissions = new Service_Commission;
        $service_adresse = new Service_Adresse;
        $service_dossier = new Service_Dossier;

        $this->view->DB_type = $service_dossier->getAllTypes();
        $this->view->array_commissions = $service_commissions->getCommissionsAndTypes();
        $this->view->array_communes = $service_adresse->getAllCommunes();
        $this->view->liste_prev = $service_search->listePrevActifs();
        $this->view->array_voies = $this->_request->isGet() && count($this->_request->getQuery()) > 0 && array_key_exists('commune', $this->_request->getQuery()) && $this->_request->getQuery()['commune'] != '' ? $service_adresse->getVoies($this->_request->getQuery()['commune']) : array();

        $checkDateFormat = function($date) {
            if (!$date) return false;
            $dateArgs = explode('/', $date);
            return checkdate($dateArgs[1], $dateArgs[0], $dateArgs[2]);
        };

        if($this->_request->isGet() && count($this->_request->getQuery()) > 0) {
            if (!empty($_GET)) {
                // Export Calc
                if (isset ( $_GET ['Exporter'] )) {
                    try {
                        $parameters = $this->_request->getQuery();
                        $page = array_key_exists('page', $parameters) ? $parameters['page'] : 1;
                        $num_doc_urba = array_key_exists('permis', $parameters) && $parameters['permis'] != '' ? $parameters['permis'] : null;
                        $objet = array_key_exists('objet', $parameters) && $parameters['objet'] != ''  && (string) $parameters['objet'][0] != '#'? $parameters['objet'] : null;
                        $types = array_key_exists('types', $parameters) ? $parameters['types'] : null;
                        $criteresRecherche = array();
                        $criteresRecherche['commissions'] = array_key_exists('commissions', $parameters) ? $parameters['commissions'] : null;
                        $criteresRecherche['avisCommission'] = array_key_exists('avisCommission', $parameters) ? $parameters['avisCommission'] : null;
                        $criteresRecherche['avisRapporteur'] = array_key_exists('avisRapporteur', $parameters) ? $parameters['avisRapporteur'] : null;
                        $criteresRecherche['avisDiffere'] = array_key_exists('avisDiffere', $parameters ) && count ( $parameters ['avisDiffere'] ) == 1 ? $parameters ['avisDiffere'] [0] == 'true' : null;
                        $criteresRecherche['commune'] = array_key_exists('commune', $parameters) && $parameters['commune'] != '' ? $parameters['commune'] : null;
                        $criteresRecherche['voie'] = array_key_exists('voie', $parameters) && $parameters['voie'] != '' ? $parameters['voie'] : null;
                        $criteresRecherche['courrier'] = array_key_exists('courrier', $parameters) && $parameters['courrier'] != '' ? $parameters['courrier'] : null;
                        $criteresRecherche['preventionniste'] = array_key_exists('preventionniste', $parameters) && $parameters['preventionniste'] != '' ? $parameters['preventionniste'] : null;
                        $criteresRecherche['dateCreationStart'] = array_key_exists('date-creation-start', $parameters) && $checkDateFormat($parameters['date-creation-start']) ? $parameters['date-creation-start'] : null;
                        $criteresRecherche['dateCreationEnd'] = array_key_exists('date-creation-end', $parameters) && $checkDateFormat($parameters['date-creation-end']) ? $parameters['date-creation-end'] : null;
                        $criteresRecherche['dateReceptionStart'] = array_key_exists('date-reception-start', $parameters) && $checkDateFormat($parameters['date-reception-start']) ? $parameters['date-reception-start'] : null;
                        $criteresRecherche['dateReceptionEnd'] = array_key_exists('date-reception-end', $parameters) && $checkDateFormat($parameters['date-reception-end']) ? $parameters['date-reception-end'] : null;
                        $criteresRecherche['dateReponseStart'] = array_key_exists('date-reponse-start', $parameters) && $checkDateFormat($parameters['date-reponse-start']) ? $parameters['date-reponse-start'] : null;
                        $criteresRecherche['dateReponseEnd'] = array_key_exists('date-reponse-end', $parameters) && $checkDateFormat($parameters['date-reponse-end']) ? $parameters['date-reponse-end'] : null;
                        $criteresRecherche['dateCommissionStart'] = array_key_exists('date-commission-start', $parameters) && $checkDateFormat($parameters['date-commission-start']) ? $parameters['date-commission-start'] : null;
                        $criteresRecherche['dateCommissionEnd'] = array_key_exists('date-commission-end', $parameters) && $checkDateFormat($parameters['date-commission-end']) ? $parameters['date-commission-end'] : null;
                        $criteresRecherche['dateVisiteStart'] = array_key_exists('date-visite-start', $parameters) && $checkDateFormat($parameters['date-visite-start']) ? $parameters['date-visite-start'] : null;
                        $criteresRecherche['dateVisiteEnd'] = array_key_exists('date-visite-end', $parameters) && $checkDateFormat($parameters['date-visite-end']) ? $parameters['date-visite-end'] : null;
                        $criteresRecherche['label'] = array_key_exists ( 'label', $parameters ) && $parameters ['label'] != '' && ( string ) $parameters ['label'] [0] != '#' ? $parameters ['label'] : null;
                        $criteresRecherche['identifiant'] = array_key_exists ( 'label', $parameters ) && $parameters ['label'] != '' && ( string ) $parameters ['label'] [0] == '#' ? substr ( $parameters ['label'], 1 ) : null;
                        
                        $search = $service_search->extractionDossiers($types, $objet, $num_doc_urba, null, null, $criteresRecherche);
                        
                        $objPHPExcel = new PHPExcel ();
                        $objPHPExcel->setActiveSheetIndex ( 0 );
                        $sheet = $objPHPExcel->getActiveSheet ();
                        $sheet->setTitle ( 'Liste des dossiers' );
                        
                        $objPHPExcel->getDefaultStyle ()->getFont ()->setName ( 'Arial' )->setSize ( 10 )->setBold ( false );
                        $sheet->getDefaultRowDimension ()->setRowHeight ( - 1 );
                        
                        // Formattage des titres de colonnes
                        $styleArray = array (
                                'borders' => array (
                                        'allborders' => array (
                                                'style' => PHPExcel_Style_Border::BORDER_THIN
                                        )
                                )
                        );
                        $sheet->getStyle ( 'A1:T1' )->applyFromArray ( $styleArray );
                        unset ( $styleArray );
                        $sheet->getStyle ( 'A1:T1' )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
                        $sheet->getStyle ( 'A1:T1' )->getFont ()->setSize ( 11 )->setBold ( true );
                        
                        foreach ( range ( 'A', 'T' ) as $columnID ) {
                            $sheet->getColumnDimension ( $columnID )->setAutoSize ( true );
                        }
                        
                        $sheet->setCellValueByColumnAndRow ( 0, 1, "Commune" );
                        $sheet->setCellValueByColumnAndRow ( 1, 1, "Catégorie" );
                        $sheet->setCellValueByColumnAndRow ( 2, 1, "Type" );
                        $sheet->setCellValueByColumnAndRow ( 3, 1, "Activité" );
                        $sheet->setCellValueByColumnAndRow ( 4, 1, "Code/identifiant établissement" );
                        $sheet->setCellValueByColumnAndRow ( 5, 1, "Libellé établissement" );
                        $sheet->setCellValueByColumnAndRow ( 6, 1, "Statut" );
                        $sheet->setCellValueByColumnAndRow ( 7, 1, "Genre" );
                        $sheet->setCellValueByColumnAndRow ( 8, 1, "Type du dossier" );
                        $sheet->setCellValueByColumnAndRow ( 9, 1, "Nature du dossier" );
                        $sheet->setCellValueByColumnAndRow ( 10, 1, "Date de création du dossier" );
                        $sheet->setCellValueByColumnAndRow ( 11, 1, "Objet du dossier" );
                        $sheet->setCellValueByColumnAndRow ( 12, 1, "Numéro document urbanisme" );
                        $sheet->setCellValueByColumnAndRow ( 13, 1, "Date de visite" );
                        $sheet->setCellValueByColumnAndRow ( 14, 1, "Date de la commission en salle" );
                        $sheet->setCellValueByColumnAndRow ( 15, 1, "Commission du dossier" );
                        $sheet->setCellValueByColumnAndRow ( 16, 1, "Avis rapporteur" );
                        $sheet->setCellValueByColumnAndRow ( 17, 1, "Avis commission" );
                        $sheet->setCellValueByColumnAndRow ( 18, 1, "Préventionniste en charge du dossier" );
                        $sheet->setCellValueByColumnAndRow ( 19, 1, "Pièces jointes ?" );
                        
                        $ligne = 2;
                        foreach ( $search ['results'] as $row ) {
                            $sheet->setCellValueByColumnAndRow ( 0, $ligne, $row ['LIBELLE_COMMUNE'] );
                            $sheet->setCellValueByColumnAndRow ( 1, $ligne, $row ['LIBELLE_CATEGORIE'] );
                            $sheet->setCellValueByColumnAndRow ( 2, $ligne, $row ['LIBELLE_TYPE_ETABLISSEMENT'] );
                            $sheet->setCellValueByColumnAndRow ( 3, $ligne, $row ['LIBELLE_ACTIVITE'] );
                            $sheet->setCellValueByColumnAndRow ( 4, $ligne, $row ['NUMEROID_ETABLISSEMENT'] );
                            $sheet->setCellValueByColumnAndRow ( 5, $ligne, $row ['LIBELLE_ETABLISSEMENTINFORMATIONS'] );
                            $sheet->setCellValueByColumnAndRow ( 6, $ligne, $row ['LIBELLE_STATUT'] );
                            $sheet->setCellValueByColumnAndRow ( 7, $ligne, $row ['LIBELLE_GENRE'] );
                            $sheet->setCellValueByColumnAndRow ( 8, $ligne, $row ['LIBELLE_DOSSIERTYPE'] );
                            $sheet->setCellValueByColumnAndRow ( 9, $ligne, $row ['LIBELLE_DOSSIERNATURE'] );
                            if ($row ['DATEINSERT_DOSSIER'] != '') {
                                $dateCreationDossier = explode ( "-", $row ['DATEINSERT_DOSSIER'] );
                                // Formattage du jour, qui contient aussi l'heure -> ne passe pas avec FormattedPHPToExcel
                                $dateCreationDossier[2] = substr($dateCreationDossier[2], 0, 2);
                                $datetimeCreationDossier = PHPExcel_Shared_Date::FormattedPHPToExcel ( $dateCreationDossier [0], $dateCreationDossier [1], $dateCreationDossier [2] );
                                $sheet->setCellValueByColumnAndRow ( 10, $ligne, $datetimeCreationDossier );
                                $sheet->getStyleByColumnAndRow ( 10, $ligne )->getNumberFormat ()->setFormatCode ( PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY );
                            }
                            $sheet->setCellValueByColumnAndRow ( 11, $ligne, $row ['OBJET_DOSSIER'] );
                            $sheet->setCellValueByColumnAndRow ( 12, $ligne, $row ['NUM_DOCURBA'] );
                            if ($row ['DATEVISITE_DOSSIER'] != '') {
                                $dateVisiteDossier = explode ( "-", $row ['DATEVISITE_DOSSIER'] );
                                $datetimeVisiteDossier = PHPExcel_Shared_Date::FormattedPHPToExcel ( $dateVisiteDossier [0], $dateVisiteDossier [1], $dateVisiteDossier [2] );
                                $sheet->setCellValueByColumnAndRow ( 13, $ligne, $datetimeVisiteDossier );
                                $sheet->getStyleByColumnAndRow ( 13, $ligne )->getNumberFormat ()->setFormatCode ( PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY );
                            }
                            if ($row ['DATECOMM_DOSSIER'] != '') {
                                $dateCommissionDossier = explode ( "-", $row ['DATECOMM_DOSSIER'] );
                                $datetimeCommissionDossier = PHPExcel_Shared_Date::FormattedPHPToExcel ( $dateCommissionDossier [0], $dateCommissionDossier [1], $dateCommissionDossier [2] );
                                $sheet->setCellValueByColumnAndRow ( 14, $ligne, $datetimeCommissionDossier );
                                $sheet->getStyleByColumnAndRow ( 14, $ligne )->getNumberFormat ()->setFormatCode ( PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY );
                            }
                            $sheet->setCellValueByColumnAndRow ( 15, $ligne, $row ['LIBELLE_COMMISSION'] );
                            $sheet->setCellValueByColumnAndRow ( 16, $ligne, $row ['LIBELLE_AVIS_RAPPORTEUR'] );
                            $sheet->setCellValueByColumnAndRow ( 17, $ligne, $row ['LIBELLE_AVIS_COMMISSION'] );
                            $sheet->setCellValueByColumnAndRow ( 18, $ligne, $row ['PRENOM_UTILISATEURINFORMATIONS'] . " " . $row ['NOM_UTILISATEURINFORMATIONS'] );
                            if ($row ['ID_PIECEJOINTE'] != '') {
                                $sheet->setCellValueByColumnAndRow ( 19, $ligne, "Oui" );
                            } else {
                                $sheet->setCellValueByColumnAndRow ( 19, $ligne, "Non" );
                            }
                            
                            $ligne ++;
                        }
                        
                        $this->view->writer = PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel5' );
                        
                        // Ensuite j'ai choisi de désactiver mon layout
                        $this->_helper->layout ()->disableLayout ();
                        
                        header("Content-Type: application/vnd.oasis.opendocument.spreadsheet");
                        $filename = "Export_Dossiers_".date('Y-m-d_H-i-s').".ods";
                        header("Content-Disposition: attachment; filename=".$filename."");
                        $this->view->writer->save('php://output');
                        exit();
                        
                    } catch ( Exception $e ) {
                        $this->_helper->flashMessenger ( array (
                                'context' => 'error',
                                'title' => 'Problème d\'export',
                                'message' => 'L\'export a rencontré un problème. Veuillez rééssayez. (' . $e->getMessage () . ')'
                        ) );
                    }
                    
                } else {
                    // Recherche
                    try {
                        $parameters = $this->_request->getQuery();
                        $page = array_key_exists('page', $parameters) ? $parameters['page'] : 1;
                        $num_doc_urba = array_key_exists('permis', $parameters) && $parameters['permis'] != '' ? $parameters['permis'] : null;
                        $objet = array_key_exists('objet', $parameters) && $parameters['objet'] != ''  && (string) $parameters['objet'][0] != '#'? $parameters['objet'] : null;
                        $types = array_key_exists('types', $parameters) ? $parameters['types'] : null;
                        $criteresRecherche = array();
                        $criteresRecherche['commissions'] = array_key_exists('commissions', $parameters) ? $parameters['commissions'] : null;
                        $criteresRecherche['avisCommission'] = array_key_exists('avisCommission', $parameters) ? $parameters['avisCommission'] : null;
                        $criteresRecherche['avisRapporteur'] = array_key_exists('avisRapporteur', $parameters) ? $parameters['avisRapporteur'] : null;
                        $criteresRecherche['avisDiffere'] = array_key_exists('avisDiffere', $parameters ) && count ( $parameters ['avisDiffere'] ) == 1 ? $parameters ['avisDiffere'] [0] == 'true' : null;
                        $criteresRecherche['commune'] = array_key_exists('commune', $parameters) && $parameters['commune'] != '' ? $parameters['commune'] : null;
                        $criteresRecherche['voie'] = array_key_exists('voie', $parameters) && $parameters['voie'] != '' ? $parameters['voie'] : null;
                        $criteresRecherche['courrier'] = array_key_exists('courrier', $parameters) && $parameters['courrier'] != '' ? $parameters['courrier'] : null;
                        $criteresRecherche['preventionniste'] = array_key_exists('preventionniste', $parameters) && $parameters['preventionniste'] != '' ? $parameters['preventionniste'] : null;
                        $criteresRecherche['dateCreationStart'] = array_key_exists('date-creation-start', $parameters) && $checkDateFormat($parameters['date-creation-start']) ? $parameters['date-creation-start'] : null;
                        $criteresRecherche['dateCreationEnd'] = array_key_exists('date-creation-end', $parameters) && $checkDateFormat($parameters['date-creation-end']) ? $parameters['date-creation-end'] : null;
                        $criteresRecherche['dateReceptionStart'] = array_key_exists('date-reception-start', $parameters) && $checkDateFormat($parameters['date-reception-start']) ? $parameters['date-reception-start'] : null;
                        $criteresRecherche['dateReceptionEnd'] = array_key_exists('date-reception-end', $parameters) && $checkDateFormat($parameters['date-reception-end']) ? $parameters['date-reception-end'] : null;
                        $criteresRecherche['dateReponseStart'] = array_key_exists('date-reponse-start', $parameters) && $checkDateFormat($parameters['date-reponse-start']) ? $parameters['date-reponse-start'] : null;
                        $criteresRecherche['dateReponseEnd'] = array_key_exists('date-reponse-end', $parameters) && $checkDateFormat($parameters['date-reponse-end']) ? $parameters['date-reponse-end'] : null;
                        $criteresRecherche['dateCommissionStart'] = array_key_exists('date-commission-start', $parameters) && $checkDateFormat($parameters['date-commission-start']) ? $parameters['date-commission-start'] : null;
                        $criteresRecherche['dateCommissionEnd'] = array_key_exists('date-commission-end', $parameters) && $checkDateFormat($parameters['date-commission-end']) ? $parameters['date-commission-end'] : null;
                        $criteresRecherche['dateVisiteStart'] = array_key_exists('date-visite-start', $parameters) && $checkDateFormat($parameters['date-visite-start']) ? $parameters['date-visite-start'] : null;
                        $criteresRecherche['dateVisiteEnd'] = array_key_exists('date-visite-end', $parameters) && $checkDateFormat($parameters['date-visite-end']) ? $parameters['date-visite-end'] : null;
                        $criteresRecherche['label'] = array_key_exists ( 'label', $parameters ) && $parameters ['label'] != '' && ( string ) $parameters ['label'] [0] != '#' ? $parameters ['label'] : null;
                        $criteresRecherche['identifiant'] = array_key_exists ( 'label', $parameters ) && $parameters ['label'] != '' && ( string ) $parameters ['label'] [0] == '#' ? substr ( $parameters ['label'], 1 ) : null;
                        
                        $search = $service_search->dossiers($types, $objet, $num_doc_urba, null, null, 50, $page,$criteresRecherche);
                        
                        $paginator = new Zend_Paginator(new SDIS62_Paginator_Adapter_Array($search['results'], $search['search_metadata']['count']));
                        $paginator->setItemCountPerPage(50)->setCurrentPageNumber($page)->setDefaultScrollingStyle('Elastic');
                        
                        $this->view->results = $paginator;
                    }
                    catch(Exception $e) {
                        $this->_helper->flashMessenger(array('context' => 'error','title' => 'Problème de recherche','message' => 'La recherche n\'a pas été effectué correctement. Veuillez rééssayez. (' . $e->getMessage() . ')'));
                    }
                }
            }
        }
    }

    public function utilisateurAction()
    {
        $this->_helper->layout->setLayout('search');

        $service_search = new Service_Search;
        $service_user = new Service_User;

        $this->view->DB_fonction = $service_user->getAllFonctions();

        if($this->_request->isGet() && count($this->_request->getQuery()) > 0) {
            try {
                $parameters = $this->_request->getQuery();
                $page = array_key_exists('page', $parameters) ? $parameters['page'] : 1;
                $name = $parameters['name'];
                $fonctions = array_key_exists('fonctions', $parameters) ? $parameters['fonctions'] : null;

                $search = $service_search->users($fonctions, $name, null, true, 50, $page);

                $paginator = new Zend_Paginator(new SDIS62_Paginator_Adapter_Array($search['results'], $search['search_metadata']['count']));
                $paginator->setItemCountPerPage(50)->setCurrentPageNumber($page)->setDefaultScrollingStyle('Elastic');

                $this->view->results = $paginator;
            }
            catch(Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Problème de recherche','message' => 'La recherche n\'a pas été effectué correctement. Veuillez rééssayez. (' . $e->getMessage() . ')'));
            }
        }
    }
    
    public function displayAjaxSearchEtablissementAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $service_search = new Service_Search;

        $data = $service_search->etablissements(null, null, null, null, null, null, null, null, null, null, null, null, $this->_request->parent, null, null, null, null, null);

        $data = $data['results'];

        $html = "<ul class='recherche_liste'>";
        $html .= Zend_Layout::getMvcInstance()->getView()->partialLoop('search/results/etablissement.phtml', (array) $data );
        $html .= "</ul>";

        echo $html;
    }
    
    public function displayAjaxSearchDossierAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $service_search = new Service_Search;

        $data = $service_search->dossiers(null, null, null, $this->_request->parent, null, 100);

        $data = $data['results'];

        $html = "<ul class='recherche_liste'>";
        $html .= Zend_Layout::getMvcInstance()->getView()->partialLoop('search/results/dossier.phtml', (array) $data );
        $html .= "</ul>";

        echo $html;
    }
}
