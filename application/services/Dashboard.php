<?php

class Service_Dashboard
{
    protected $options = [];

    protected $blocsConfig = [
        // lié aux commissions
        'nextCommissions' => [
            'service' => 'Service_Dashboard',
            'method' => 'getNextCommission',
            'acl' => ['dashboard', 'view_next_commissions'],
            'title' => 'Prochaines commissions',
            'type' => 'commissions',
            'height' => 'small',
            'width' => 'small',
        ],

        'nextCommissionsOdj' => [
            'service' => 'Service_Dashboard',
            'method' => 'getNextCommission',
            'acl' => ['dashboard', 'view_next_commissions_odj'],
            'title' => 'Prochaines commissions',
            'type' => 'odj',
            'height' => 'small',
            'width' => 'small',
        ],

        // lié aux établissements
        'ERPSuivis' => [
            'service' => 'Service_Dashboard',
            'method' => 'getERPSuivis',
            'acl' => ['dashboard', 'view_ets_suivis'],
            'title' => 'Etablissements suivis',
            'type' => 'etablissements',
            'height' => 'small',
            'width' => 'medium',
        ],
        'ERPOuvertsSousAvisDefavorable' => [
            'service' => 'Service_Dashboard',
            'method' => 'getERPOuvertsSousAvisDefavorable',
            'acl' => ['dashboard', 'view_ets_avis_defavorable'],
            'title' => 'Etablissements sous avis défavorable',
            'type' => 'etablissements',
            'height' => 'small',
            'width' => 'small',
        ],
        'ERPOuvertsSousAvisDefavorableSuivis' => [
            'service' => 'Service_Dashboard',
            'method' => 'getERPOuvertsSousAvisDefavorableSuivis',
            'acl' => ['dashboard', 'view_ets_avis_defavorable_suivis'],
            'title' => 'Etablissements suivis sous avis défavorable',
            'type' => 'etablissements',
            'height' => 'small',
            'width' => 'small',
        ],
        'ERPOuvertsSousAvisDefavorableSurCommune' => [
            'service' => 'Service_Dashboard',
            'method' => 'getERPOuvertsSousAvisDefavorableSurCommune',
            'acl' => ['dashboard', 'view_ets_avis_defavorable_sur_commune'],
            'title' => 'Etablissements de votre commune sous avis défavorable',
            'type' => 'etablissements',
            'height' => 'small',
            'width' => 'small',
        ],
        'ERPSansPreventionniste' => [
            'service' => 'Service_Dashboard',
            'method' => 'getERPSansPreventionniste',
            'acl' => ['dashboard', 'view_ets_sans_preventionniste'],
            'title' => 'Etablissements sans préventionnistes',
            'type' => 'etablissements',
            'height' => 'small',
            'width' => 'small',
        ],
        'ERPOuvertsSansProchainesVisitePeriodiques' => [
            'service' => 'Service_Dashboard',
            'method' => 'getERPOuvertsSansProchainesVisitePeriodiques',
            'acl' => ['dashboard', 'view_ets_ouverts_sans_prochaine_vp'],
            'title' => 'Etablissements sans prochaine VP cette année',
            'type' => 'etablissements',
            'height' => 'small',
            'width' => 'small',
        ],

        // lié aux dossiers
        'DossiersSuivisSansAvis' => [
            'service' => 'Service_Dashboard',
            'method' => 'getDossiersSuivisSansAvis',
            'acl' => ['dashboard', 'view_doss_suivis_sans_avis'],
            'title' => 'Dossiers suivis sans avis du rapporteur',
            'type' => 'dossiers',
            'height' => 'small',
            'width' => 'small',
        ],
        'DossiersSuivisNonVerrouilles' => [
            'service' => 'Service_Dashboard',
            'method' => 'getDossiersSuivisNonVerrouilles',
            'acl' => ['dashboard', 'view_doss_suivis_unlocked'],
            'title' => 'Dossiers suivis non verrouillés',
            'type' => 'dossiers',
            'height' => 'small',
            'width' => 'small',
        ],
        'DossierDateCommissionEchu' => [
            'service' => 'Service_Dashboard',
            'method' => 'getDossierDateCommissionEchu',
            'acl' => ['dashboard', 'view_doss_sans_avis'],
            'title' => 'Dossiers sans avis de commission',
            'type' => 'dossiers',
            'height' => 'small',
            'width' => 'small',
        ],
        'DossierAvecAvisDiffere' => [
            'service' => 'Service_Dashboard',
            'method' => 'getDossierAvecAvisDiffere',
            'acl' => ['dashboard', 'view_doss_avis_differe'],
            'title' => 'Dossiers avec avis différés',
            'type' => 'dossiers',
            'height' => 'small',
            'width' => 'small',
        ],
        'CourrierSansReponse' => [
            'service' => 'Service_Dashboard',
            'method' => 'getCourrierSansReponse',
            'acl' => ['dashboard', 'view_courrier_sans_reponse'],
            'title' => 'Courriers sans réponse',
            'type' => 'dossiers',
            'height' => 'small',
            'width' => 'small',
        ],
        // autres blocs
        'feeds' => [
            'service' => 'Service_Feed',
            'method' => 'getFeeds',
            'acl' => null,
            'title' => 'Messages',
            'type' => 'feeds',
            'height' => 'small',
            'width' => 'small',
        ],
        // bloc levée prescriptions
        'leveePresc' => [
            'service' => 'Service_Dashboard',
            'method' => 'getLeveePresc',
            'acl' => ['dashboard', 'view_doss_levee_prescriptions'],
            'title' => 'Dossiers avec une date de levée des prescriptions',
            'type' => 'dossiers',
            'height' => 'small',
            'width' => 'small',
        ],
        // bloc absence de quorum
        'absQuorum' => [
            'service' => 'Service_Dashboard',
            'method' => 'getAbsenceQuorum',
            'acl' => ['dashboard', 'view_doss_absence_quorum'],
            'title' => 'Dossiers ayant une absence de quorum',
            'type' => 'dossiers',
            'height' => 'small',
            'width' => 'small',
        ],
        // bloc npsp
        'npsp' => [
            'service' => 'Service_Dashboard',
            'method' => 'getNpsp',
            'acl' => ['dashboard', 'view_doss_npsp'],
            'title' => 'Dossiers avec statut ne peut se prononcer',
            'type' => 'dossiers',
            'height' => 'small',
            'width' => 'small',
        ],

        // bloc npsp
        'dossierPlatau' => [
            'service' => 'Service_Dashboard',
            'method' => 'getDossiersPlatAUSansEtablissement',
            'acl' => ['dashboard', 'view_doss_platau_sans_etab'],
            'title' => 'Dossiers Plat\'AU à traiter',
            'type' => 'dossierPlatau',
            'height' => 'small',
            'width' => 'small',
        ],
    ];

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        // default options
        $this->options = array_merge([
            'next_commissions_days' => 15,
            'dossiers_sans_avis_days' => 15,
            'courrier_sans_reponse_days' => 10,
        ], $options);

        // custom configurations
        if (getenv('PREVARISC_DASHBOARD_NEXT_COMMISSIONS_DAYS')) {
            $this->options['next_commissions_days'] = (int) getenv('PREVARISC_DASHBOARD_NEXT_COMMISSIONS_DAYS');
        }

        if (getenv('PREVARISC_DASHBOARD_DOSSIERS_SANS_AVIS_DAYS')) {
            $this->options['dossiers_sans_avis_days'] = (int) getenv('PREVARISC_DASHBOARD_DOSSIERS_SANS_AVIS_DAYS');
        }

        if (getenv('PREVARISC_DASHBOARD_COURRIER_SANS_REPONSE_DAYS')) {
            $this->options['courrier_sans_reponse_days'] = (int) getenv('PREVARISC_DASHBOARD_COURRIER_SANS_REPONSE_DAYS');
        }
    }

    public function getBlocConfig()
    {
        return $this->blocsConfig;
    }

    /**
     * @psalm-return array<int, array{id:mixed, LIBELLE_COMMISSION:mixed, LIBELLE_DATECOMMISSION:mixed, ID_COMMISSIONTYPEEVENEMENT:mixed, DATE_COMMISSION:mixed, HEUREDEB_COMMISSION:mixed, HEUREFIN_COMMISSION:mixed, heure:string, odj:mixed}>
     *
     * @param mixed $user
     *
     * @return array[]
     */
    public function getNextCommission($user): array
    {
        $dbDateCommission = new Model_DbTable_DateCommission();

        $prochainesCommission = $dbDateCommission->getNextCommission(
            $this->getCommissionUser($user),
            time(),
            time() + 3600 * 24 * $this->options['next_commissions_days']
        );

        // on récupère pour chaque prochaine commission le nombre de dossiers affectés
        $commissions = [];
        foreach ($prochainesCommission as $commissiondujour) {
            $commissions[] = [
                'id' => $commissiondujour['ID_DATECOMMISSION'],
                'LIBELLE_COMMISSION' => $commissiondujour['LIBELLE_COMMISSION'],
                'LIBELLE_DATECOMMISSION' => $commissiondujour['LIBELLE_DATECOMMISSION'],
                'ID_COMMISSIONTYPEEVENEMENT' => $commissiondujour['ID_COMMISSIONTYPEEVENEMENT'],
                'DATE_COMMISSION' => $commissiondujour['DATE_COMMISSION'],
                'HEUREDEB_COMMISSION' => $commissiondujour['HEUREDEB_COMMISSION'],
                'HEUREFIN_COMMISSION' => $commissiondujour['HEUREFIN_COMMISSION'],
                'heure' => substr($commissiondujour['HEUREDEB_COMMISSION'], 0, 5).' - '.substr($commissiondujour['HEUREFIN_COMMISSION'], 0, 5),
                'odj' => $this->getCommissionODJ($commissiondujour),
            ];
        }

        return $commissions;
    }

    public function getDossierDateCommissionEchu($user)
    {
        $dbDossier = new Model_DbTable_Dossier();
        $commissionsUser = $this->getCommissionUser($user);

        return $dbDossier->listeDesDossierDateCommissionEchu($commissionsUser, $this->options['dossiers_sans_avis_days']);
    }

    public function getERPOuvertsSousAvisDefavorable($user)
    {
        $dbEtablissement = new Model_DbTable_Etablissement();

        return $dbEtablissement->listeDesERPOuvertsSousAvisDefavorable();
    }

    public function getERPOuvertsSousAvisDefavorableSuivis($user)
    {
        $dbEtablissement = new Model_DbTable_Etablissement();

        return $dbEtablissement->listeDesERPOuvertsSousAvisDefavorable(null, null, $user['ID_UTILISATEUR']);
    }

    public function getERPOuvertsSousAvisDefavorableSurCommune($user)
    {
        $dbEtablissement = new Model_DbTable_Etablissement();

        if (!$user['NUMINSEE_COMMUNE']) {
            return [];
        }

        return $dbEtablissement->listeDesERPOuvertsSousAvisDefavorable(null, $user['NUMINSEE_COMMUNE']);
    }

    public function getERPOuvertsSansProchainesVisitePeriodiques($user)
    {
        $dbEtablissement = new Model_DbTable_Etablissement();
        $commissionsUser = $this->getCommissionUser($user);

        return $dbEtablissement->listeErpOuvertsSansProchainesVisitePeriodiques($commissionsUser);
    }

    public function getERPSansPreventionniste($user)
    {
        $dbEtablissement = new Model_DbTable_Etablissement();

        return $dbEtablissement->listeERPSansPreventionniste();
    }

    /**
     * @param mixed $user
     *
     * @return array
     */
    public function getERPSuivis($user)
    {
        $etablissements = [];
        $id_user = $user['ID_UTILISATEUR'];

        // Ets 1 - 4ème catégorie
        $search = new Model_DbTable_Search();
        $search->setItem('etablissement');
        $search->setCriteria('utilisateur.ID_UTILISATEUR', $id_user);
        $search->setCriteria('etablissementinformations.ID_STATUT', ['2', '4']);
        $search->sup('etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS', 0);
        $search->setCriteria('etablissementinformations.ID_CATEGORIE', ['1', '2', '3', '4']);
        $search->setCriteria('etablissementinformations.ID_GENRE', 2);
        $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

        // 5ème catégorie defavorable
        $search = new Model_DbTable_Search();
        $search->setItem('etablissement');
        $search->setCriteria('utilisateur.ID_UTILISATEUR', $id_user);
        $search->setCriteria('etablissementinformations.ID_STATUT', ['2', '4']);
        $search->sup('etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS', 0);
        $search->setCriteria('etablissementinformations.ID_CATEGORIE', '5');
        $search->setCriteria('avis.ID_AVIS', 2);
        $search->setCriteria('etablissementinformations.ID_GENRE', 2);
        $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

        // 5ème catégorie avec local à sommeil
        $search = new Model_DbTable_Search();
        $search->setItem('etablissement');
        $search->setCriteria('utilisateur.ID_UTILISATEUR', $id_user);
        $search->setCriteria('etablissementinformations.ID_STATUT', ['2', '4']);
        $search->sup('etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS', 0);
        $search->setCriteria('etablissementinformations.ID_CATEGORIE', '5');
        $search->setCriteria('etablissementinformations.ID_GENRE', 2);
        $search->setCriteria('etablissementinformations.LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS', '1');
        $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

        // EIC - IGH - HAB - Autres
        $search = new Model_DbTable_Search();
        $search->setItem('etablissement');
        $search->setCriteria('utilisateur.ID_UTILISATEUR', $id_user);
        $search->setCriteria('etablissementinformations.ID_STATUT', ['2', '4']);
        $search->sup('etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS', 0);
        $search->setCriteria('etablissementinformations.ID_GENRE', ['6', '5', '4', '7', '8', '9', '10']);
        $etablissements = array_merge($search->run(false, null, false)->toArray(), $etablissements);

        return array_unique($etablissements, SORT_REGULAR);
    }

    public function getDossierAvecAvisDiffere($user)
    {
        $dbDossier = new Model_DbTable_Dossier();
        $commissionsUser = $this->getCommissionUser($user);

        return $dbDossier->listeDossierAvecAvisDiffere($commissionsUser);
    }

    public function getCourrierSansReponse($user)
    {
        $dbDossier = new Model_DbTable_Dossier();

        return $dbDossier->listeDesCourrierSansReponse($this->options['courrier_sans_reponse_days']);
    }

    public function getDossiersSuivisNonVerrouilles($user)
    {
        $dossiers = [];
        $id_user = $user['ID_UTILISATEUR'];

        // Dossiers suivis
        $search = new Model_DbTable_Search();
        $search->setItem('dossier');
        $search->setCriteria('utilisateur.ID_UTILISATEUR', $id_user);
        $search->setCriteria('d.VERROU_DOSSIER', 0);
        $search->order('IFNULL(d.DATEVISITE_DOSSIER, d.DATEINSERT_DOSSIER) desc');

        return $search->run(false, null, false)->toArray();
    }

    public function getDossiersSuivisSansAvis($user)
    {
        $dossiers = [];

        $id_user = $user['ID_UTILISATEUR'];

        // Dossiers suivis
        $search = new Model_DbTable_Search();
        $search->setItem('dossier');
        $search->setCriteria('utilisateur.ID_UTILISATEUR', $id_user);

        $conditionEtudesSansAvis = 'd.AVIS_DOSSIER IS NULL AND (d.AVIS_DOSSIER_COMMISSION IS NULL OR d.AVIS_DOSSIER_COMMISSION = 0) AND d.TYPE_DOSSIER = 1';
        $conditionCourriersSansReponse = 'd.DATEREP_DOSSIER IS NULL AND d.TYPE_DOSSIER = 5';

        $search->setCriteria("({$conditionEtudesSansAvis}) OR ({$conditionCourriersSansReponse})");

        $search->order('d.DATEINSERT_DOSSIER desc');

        return $search->run(false, null, false)->toArray();
    }

    /**
     * Retourne la liste des dossiers Plat'AU non associé à un etablissement.
     */
    public function getDossiersPlatAUSansEtablissement()
    {
        $dbDossier = new Model_DbTable_Dossier();
        
        return $dbDossier->getAllDossierPlatAU();
    }

    public function getLeveePresc()
    {
        $DBdossierLie = new Model_DbTable_DossierLie();
        $DBdossierNautre = new Model_DbTable_DossierNature();

        $search = new Model_DbTable_Search();
        $search->setItem('dossier');
        $search->setCriteria('DELAIPRESC_DOSSIER IS NOT NULL');
        $dossiers = $search->run(false, null, false)->toArray();

        $valCpt = 0;

        foreach ($dossiers as $dossier) {
            $listeDossiersLies = $DBdossierLie->getDossierLie($dossier['ID_DOSSIER']);
            foreach ($listeDossiersLies as $lien) {
                if ($lien['ID_DOSSIER1'] == $dossier['ID_DOSSIER']) {
                    $idLien = $lien['ID_DOSSIER2'];
                } else {
                    $idLien = $lien['ID_DOSSIER1'];
                }
                $idNature = $DBdossierNautre->getDossierNaturesId($idLien)['ID_NATURE'];
                if (19 == $idNature || 7 == $idNature || 46 == $idNature) {
                    unset($dossiers[$valCpt]);
                }
            }
            ++$valCpt;
        }

        return $dossiers;
    }

    public function getAbsenceQuorum()
    {
        $search = new Model_DbTable_Search();
        $DBdossier = new Model_DbTable_Dossier();
        $DBdossierLie = new Model_DbTable_DossierLie();

        $search->setItem('dossier');
        $search->setCriteria('ABSQUORUM_DOSSIER = 1');
        $dossiers = $search->run(false, null, false)->toArray();

        $valCpt = 0;
        foreach ($dossiers as $dossier) {
            $listeDossiersLies = $DBdossierLie->getDossierLie($dossier['ID_DOSSIER']);
            foreach ($listeDossiersLies as $lien) {
                if ($lien['ID_DOSSIER1'] == $dossier['ID_DOSSIER']) {
                    $idLien = $lien['ID_DOSSIER2'];
                } else {
                    $idLien = $lien['ID_DOSSIER1'];
                }
                $tabType = $DBdossier->getTypeDossier($idLien);
                if (0 == $tabType['TYPE_DOSSIER'] || 5 == $tabType['TYPE_DOSSIER']) {
                    unset($dossiers[$valCpt]);
                }
            }
            ++$valCpt;
        }

        return $dossiers;
    }

    public function getNpsp()
    {
        $search = new Model_DbTable_Search();
        $DBdossier = new Model_DbTable_Dossier();
        $DBdossierLie = new Model_DbTable_DossierLie();

        $search->setItem('dossier');
        $search->setCriteria('NPSP_DOSSIER = 1');
        $dossiers = $search->run(false, null, false)->toArray();

        $valCpt = 0;
        foreach ($dossiers as $dossier) {
            $listeDossiersLies = $DBdossierLie->getDossierLie($dossier['ID_DOSSIER']);
            foreach ($listeDossiersLies as $lien) {
                if ($lien['ID_DOSSIER1'] == $dossier['ID_DOSSIER']) {
                    $idLien = $lien['ID_DOSSIER2'];
                } else {
                    $idLien = $lien['ID_DOSSIER1'];
                }
                $tabType = $DBdossier->getTypeDossier($idLien);
                if (0 == $tabType['TYPE_DOSSIER'] || 5 == $tabType['TYPE_DOSSIER']) {
                    unset($dossiers[$valCpt]);
                }
            }
            ++$valCpt;
        }

        return $dossiers;
    }

    /**
     * @psalm-return array<int, mixed>
     *
     * @param mixed $user
     */
    protected function getCommissionUser($user): array
    {
        $commissionsUser = [];

        if (isset($user['commissions']) && is_array($user['commissions'])) {
            foreach ($user['commissions'] as $commission) {
                $commissionsUser[] = $commission['ID_COMMISSION'];
            }
        }

        return $commissionsUser;
    }

    /**
     * @param mixed $commission
     *
     * @return array
     */
    protected function getCommissionODJ($commission)
    {
        $dbDossierAffectation = new Model_DbTable_DossierAffectation();
        $odj = array_merge(
            $dbDossierAffectation->getDossierNonAffect($commission['ID_DATECOMMISSION']),
            $dbDossierAffectation->getDossierAffect($commission['ID_DATECOMMISSION'])
        );

        return array_unique($odj, SORT_REGULAR);
    }
}
