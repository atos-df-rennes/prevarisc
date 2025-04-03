<?php

class Service_Count extends Service_Dashboard
{
    /**
     * Retourne le nombre de prochaines commissions.
     */
    public function getNextCommissionCount(array $user): int
    {
        return $this->getNextCommission($user, true);
    }

    /**
     * Retourne le nombre d'ERP suivis.
     */
    public function getERPSuivisCount(array $user): int
    {
        return $this->getERPSuivis($user, true);
    }

    /**
     * Retourne le nombre d'ERP ouverts sous avis défavorable.
     */
    public function getERPOuvertsSousAvisDefavorableCount(array $user): int
    {
        return $this->getERPOuvertsSousAvisDefavorable($user, true);
    }

    /**
     * Retourne le nombre d'ERP suivis ouverts sous avis défavorable.
     */
    public function getERPOuvertsSousAvisDefavorableSuivisCount(array $user): int
    {
        return $this->getERPOuvertsSousAvisDefavorableSuivis($user, true);
    }

    /**
     * Retourne le nombre d'ERP ouverts sous avis défavorable sur la commune affectée à l'utilisateur.
     */
    public function getERPOuvertsSousAvisDefavorableSurCommuneCount(array $user): int
    {
        return $this->getERPOuvertsSousAvisDefavorableSurCommune($user, true);
    }

    /**
     * Retourne le nombre d'ERP sans préventionniste.
     */
    public function getERPSansPreventionnisteCount(array $user): int
    {
        return $this->getERPSansPreventionniste($user, true);
    }

    /**
     * Retourne le nombre d'ERP ouverts sans prochaines visites périodiques.
     */
    public function getERPOuvertsSansProchainesVisitePeriodiquesCount(array $user): int
    {
        return $this->getERPOuvertsSansProchainesVisitePeriodiques($user, true);
    }

    /**
     * Retourne le nombre de dossiers suivis sans avis.
     */
    public function getDossiersSuivisSansAvisCount(array $user): int
    {
        return $this->getDossiersSuivisSansAvis($user, true);
    }

    /**
     * Retourne le nombre de dossiers suivis non vérouillés.
     */
    public function getDossiersSuivisNonVerrouillesCount(array $user): int
    {
        return $this->getDossiersSuivisNonVerrouilles($user, true);
    }

    /**
     * Retourne le nombre de dossiers pour lesquels la date de commission est échue.
     */
    public function getDossierDateCommissionEchuCount(array $user): int
    {
        return $this->getDossierDateCommissionEchu($user, true);
    }

    /**
     * Retourne le nombre de courriers sans réponse.
     */
    public function getCourrierSansReponseCount(array $user): int
    {
        return $this->getCourrierSansReponse($user, true);
    }

    /**
     * Retourne le nombre de messages.
     */
    public function getFeedsCount(array $user): int
    {
        $serviceFeed = new Service_Feed();

        return $serviceFeed->getFeeds($user, null, true);
    }

    /**
     * Retourne le nombre de dossiers avec une levée de prescription.
     */
    public function getLeveePrescCount(array $user): int
    {
        return $this->getLeveePresc($user, true);
    }

    /**
     * Retourne le nombre de dossiers avec une absence de quorum.
     */
    public function getAbsenceQuorumCount(array $user): int
    {
        return $this->getAbsenceQuorum($user, true);
    }

    /**
     * Retourne le nombre de dossiers avec une mention "Ne peut se prononcer".
     */
    public function getNpspCount(array $user): int
    {
        return $this->getNpsp($user, true);
    }

    /**
     * Retourne le nombre de dossiers Plat'AU non rattachés à un établissement.
     */
    public function getDossiersPlatAUSansEtablissementCount(array $user): int
    {
        $modelDossier = new Model_DbTable_Dossier();

        $select = $modelDossier->select()
            ->setIntegrityCheck(false)
            ->from(['d' => 'dossier'], ['count' => 'COUNT(*)'])
            ->joinLeft(['ed' => 'etablissementdossier'], 'd.ID_DOSSIER = ed.ID_DOSSIER', [])
            ->join(['pc' => 'platauconsultation'], 'd.ID_PLATAU = pc.ID_PLATAU', [])
            ->where('d.DATESUPPRESSION_DOSSIER IS NULL')
            ->where('d.ID_PLATAU IS NOT NULL')
            ->where('ed.ID_DOSSIER IS NULL')
        ;
        $results = $modelDossier->fetchRow($select)['count'];

        return filter_var($results, FILTER_VALIDATE_INT);
    }

    /**
     * Retourne la liste des dossiers Plat'AU ayant envoyé un avis
     * et qui comportent des pièces jointes non envoyées.
     */
    public function getDossiersPlatauPjsEnErreurCount(array $user): int
    {
        $modelDossier = new Model_DbTable_Dossier();

        $select = $modelDossier->select()
            ->setIntegrityCheck(false)
            ->from(['d' => 'dossier'], ['count' => 'COUNT(*)'])
            ->join(['ed' => 'etablissementdossier'], 'ed.ID_DOSSIER = d.ID_DOSSIER', [])
            ->join(['pc' => 'platauconsultation'], 'pc.ID_PLATAU = d.ID_PLATAU', [])
            ->join(['dpj' => 'dossierpj'], 'dpj.ID_DOSSIER = d.ID_DOSSIER', [])
            ->join(['pj' => 'piecejointe'], 'pj.ID_PIECEJOINTE = dpj.ID_PIECEJOINTE', [])
            ->join(['pjs' => 'piecejointestatut'], 'pjs.ID_PIECEJOINTESTATUT = pj.ID_PIECEJOINTESTATUT', [])
            ->where('d.DATESUPPRESSION_DOSSIER IS NULL')
            ->where('d.ID_PLATAU IS NOT NULL')
            ->where('pc.STATUT_AVIS = ?', Model_Enum_PlatauStatutAvis::TRAITE)
            ->where('pjs.NOM_STATUT IN ("to_be_exported", "on_error", "awaiting_status")')
            ->group('d.ID_DOSSIER')
        ;
        $results = $modelDossier->fetchRow($select)['count'];

        return filter_var($results, FILTER_VALIDATE_INT);
    }

    /**
     * Retourne la liste des dossiers Plat'AU ayant de nouvelles pièces.
     */
    public function getDossiersPlatauNouvellesPjsCount(array $user): int
    {
        $modelDossier = new Model_DbTable_Dossier();
        $serviceNotification = new Service_Notification();

        $select = $modelDossier->select()
            ->setIntegrityCheck(false)
            ->from(['d' => 'dossier'], ['COUNT(*) AS count'])
            ->join(['ed' => 'etablissementdossier'], 'd.ID_DOSSIER = ed.ID_DOSSIER', [])
            ->join(['pc' => 'platauconsultation'], 'pc.ID_PLATAU = d.ID_PLATAU', [])
            ->join(['dpj' => 'dossierpj'], 'd.ID_DOSSIER = dpj.ID_DOSSIER', [])
            ->join(['pj' => 'piecejointe'], 'dpj.ID_PIECEJOINTE = pj.ID_PIECEJOINTE', [])
            ->where('d.DATESUPPRESSION_DOSSIER IS NULL')
            ->where('d.ID_PLATAU IS NOT NULL')
            ->where('pj.DATE_NOTIFICATION >= ?', $serviceNotification->getLastPageVisitDate(Service_Notification::DOSSIER_PIECES_SESSION_NAMESPACE))
            ->group('d.ID_DOSSIER')
        ;
        $results = $modelDossier->fetchRow($select)['count'];

        return filter_var($results, FILTER_VALIDATE_INT);
    }

    /**
     * Retourne le nombre de dossiers avec avis différé.
     */
    public function getDossierAvecAvisDiffereCount(array $user): int
    {
        return $this->getDossierAvecAvisDiffere($user, true);
    }
}
