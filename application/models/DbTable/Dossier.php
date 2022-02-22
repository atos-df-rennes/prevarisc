<?php

class Model_DbTable_Dossier extends Zend_Db_Table_Abstract
{
    protected $_name = 'dossier'; // Nom de la base
    protected $_primary = 'ID_DOSSIER'; // Clé primaire

    //Fonction qui récupére toutes les infos générales d'un dossier
    /**
     * @param string|int|float $id
     */
    public function getGeneral($id)
    {
        $select = "SELECT *
            from dossier d
            join dossiertype dt ON d.TYPE_DOSSIER = dt.ID_DOSSIERTYPE
            join dossiernature dn ON d.ID_DOSSIER = dn.ID_DOSSIER
            join commission c ON d.COMMISSION_DOSSIER = c.ID_COMMISSION
            join commissiontype ct ON c.ID_COMMISSIONTYPE = ct.ID_COMMISSIONTYPE
            where d.ID_DOSSIER = '$id'
            and d.DATESUPPRESSION_DOSSIER IS NULL;
        ";
        
        return $this->getAdapter()->fetchRow($select);
    }

    //Fonction qui récupére tous les établissements concernés par le dossier
    //PAS CERTAIN QU'ELLE SOIT ENCORE UTILISÉE
    /**
     * @param string|int $id_etablissement
     *
     * @return array
     */
    public function getEtablissementLibelleListe($id_etablissement)
    {
        $select = "SELECT etablissementlibelle.*
            FROM etablissementlibelle
            WHERE etablissementlibelle.id_etablissement = '".$id_etablissement."'
            AND etablissementlibelle.date_etablissementlibelle = (
                SELECT MAX(etablissementlibelle.date_etablissementlibelle)
                FROM etablissementlibelle
                WHERE etablissementlibelle.id_etablissement = '".$id_etablissement."'
            );
        ";

        return $this->getAdapter()->fetchAll($select);
    }

    //Fonction qui récup tous les établissements liés au dossier LAST VERSION
    /**
     * @param string|int $id_dossier
     *
     * @return array
     */
    public function getEtablissementDossier($id_dossier)
    {
        //retourne la liste des catégories de prescriptions par ordre
        $select = "SELECT etablissementdossier.ID_ETABLISSEMENTDOSSIER ,t1.ID_ETABLISSEMENT, LIBELLE_ETABLISSEMENTINFORMATIONS, LIBELLE_GENRE
            FROM etablissementdossier, etablissement e, etablissementinformations t1, genre
            WHERE etablissementdossier.ID_ETABLISSEMENT = t1.ID_ETABLISSEMENT
            AND etablissementdossier.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT
            AND e.DATESUPPRESSION_ETABLISSEMENT IS NULL
            AND t1.ID_GENRE = genre.ID_GENRE
            AND etablissementdossier.ID_DOSSIER = '".$id_dossier."'
            AND t1.DATE_ETABLISSEMENTINFORMATIONS = (
                SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS)
                FROM etablissementdossier, etablissementinformations
                WHERE etablissementinformations.ID_ETABLISSEMENT = t1.ID_ETABLISSEMENT
            )
			GROUP BY ID_ETABLISSEMENT;
        ";
        
        return $this->getAdapter()->fetchAll($select);
    }

    // Fonction optimisée pour les ACL
    /**
     * @return array
     */
    public function getEtablissementDossier2($id_dossier)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('etablissementdossier', array('etablissementdossier.ID_ETABLISSEMENT'))
            ->joinLeftUsing(array('e' => 'etablissement'), 'ID_ETABLISSEMENT')
            ->where('etablissementdossier.ID_DOSSIER = ?', $id_dossier)
            ->where('e.DATESUPPRESSION_ETABLISSEMENT IS NULL');

        return $this->fetchAll($select)->toArray();
    }

    //autocompletion utilisé dans la partie dossier - Recherche etablissement LAST VERSION
    /**
     * @param string|int $etablissementLibelle
     *
     * @return array
     */
    public function searchLibelleEtab($etablissementLibelle)
    {
        $select = "SELECT ID_ETABLISSEMENT, LIBELLE_ETABLISSEMENTINFORMATIONS, LIBELLE_GENRE
            FROM etablissementinformations t1,genre
            WHERE genre.ID_GENRE = t1.ID_GENRE
            AND LIBELLE_ETABLISSEMENTINFORMATIONS LIKE '%".$etablissementLibelle."%'
            AND t1.DATE_ETABLISSEMENTINFORMATIONS = (
                SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS)
                FROM etablissementinformations
                WHERE t1.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT
            )
        ";

        return $this->getAdapter()->fetchAll($select);
    }

    //Fonction qui récupère toutes les cellules concernées par le dossier
    /**
     * @param string|int $id_dossier
     *
     * @return array
     */
    public function getCelluleListe($id_dossier)
    {
        $select = "SELECT cellulelibelle.*, MAX(cellulelibelle.date_cellulelibelle)
            FROM celluledossier, cellulelibelle
            WHERE cellulelibelle.id_cellule = celluledossier.id_cellule
            AND celluledossier.id_dossier = '".$id_dossier."'
            GROUP BY cellulelibelle.id_cellule;
        ";

        return $this->getAdapter()->fetchAll($select);
    }

    //retourne 1 si dossier Etude - 0 si Visite
    public function getTypeDossier($id_dossier)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('dossier', 'TYPE_DOSSIER')
            ->where('dossier.ID_DOSSIER = ?', $id_dossier);

        return $this->getAdapter()->fetchRow($select);
    }

    /**
     * @param string|int $id_dossier
     */
    public function getNatureDossier($id_dossier)
    {
        $select = "SELECT ID_NATURE
            FROM dossiernature
            WHERE id_dossier = '".$id_dossier."';
        ";

        return $this->getAdapter()->fetchRow($select);
    }

    /**
     * @param string|int $id_dossier
     */
    public function getCommissionDossier($id_dossier)
    {
        $select = "SELECT commission_dossier
            FROM dossier
            WHERE id_dossier = '".$id_dossier."';
        ";

        return $this->getAdapter()->fetchRow($select);
    }

    public function getCommissionV2($idDossier)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('d' => 'dossier'), 'd.ID_DOSSIER')
            ->join(array('c' => 'commission'), 'd.COMMISSION_DOSSIER = c.ID_COMMISSION')
            ->join(array('ct' => 'commissiontype'), 'c.ID_COMMISSIONTYPE = ct.ID_COMMISSIONTYPE')
            ->where('d.ID_DOSSIER = ?', $idDossier);

        return $this->getAdapter()->fetchRow($select);
    }

    /**
     * @param string|int $id_dossier
     */
    public function getGenerationInfos($id_dossier)
    {
        $select = "SELECT dossier.*, dossiertype.*, commission.*, commissiontype.*
            FROM dossier, dossiertype, commission, commissiontype
            WHERE dossier.commission_dossier =	commission.id_commission
            AND commission.id_commissiontype = commissiontype.id_commissiontype
            AND dossier.TYPE_DOSSIER = dossiertype.id_dossiertype
            AND dossier.id_dossier = '".$id_dossier."';
        ";

        return $this->getAdapter()->fetchRow($select);
    }

    // Retourne la liste de tout les dossiers (études et/ou visite) d'un établissement
    // Si type vaut 1 : visites ; 0 : études
    /**
     * @param string|int|float $etablissement
     *
     * @return array
     */
    public function getDossiersEtablissement($etablissement, $type = null)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('etablissementdossier', null)
            ->join('dossier', 'etablissementdossier.ID_DOSSIER = dossier.ID_DOSSIER', array('ID_DOSSIER', 'LIBELLE_DOSSIER', 'OBJET_DOSSIER', 'DESCRIPTIFGEN_DOSSIER', 'DATESECRETARIAT_DOSSIER'))
            ->join('dossiertype', 'dossier.TYPE_DOSSIER = dossiertype.ID_DOSSIERTYPE', 'VISITEBOOL_DOSSIERTYPE')
            ->where("etablissementdossier.ID_ETABLISSEMENT = $etablissement")
            ->where('dossier.DATESUPPRESSION_DOSSIER IS NULL')
            ->order('dossier.DATESECRETARIAT_DOSSIER DESC');

        if ($type == '1' || $type == '0') {
            $select->where("dossiertype.VISITEBOOL_DOSSIERTYPE = $type");
        }

        return $this->fetchAll($select)->toArray();
    }

    public function getLastInfosEtab($idEtablissement)
    {
        $select = "SELECT ID_ETABLISSEMENT, LIBELLE_ETABLISSEMENTINFORMATIONS, LIBELLE_GENRE
            FROM etablissementinformations,genre
            WHERE genre.ID_GENRE = etablissementinformations.ID_GENRE;
        ";

        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * @param string|int $idEtablissement
     * @param string|int $idDossier
     *
     * @return array
     */
    public function getDossierEtab($idEtablissement, $idDossier)
    {
        $select = "SELECT *
            FROM dossier, etablissementdossier, dossiertype
            WHERE dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
            AND etablissementdossier.ID_ETABLISSEMENT = '".$idEtablissement."'
            AND dossiertype.ID_DOSSIERTYPE = dossier.TYPE_DOSSIER

            AND dossier.ID_DOSSIER NOT IN (
                SELECT ID_DOSSIER1
                FROM dossierlie
                WHERE ID_DOSSIER2 = '".$idDossier."'
            )
            AND dossier.ID_DOSSIER NOT IN (
                SELECT ID_DOSSIER2
                FROM dossierlie
                WHERE ID_DOSSIER1 = '".$idDossier."'
            )
            AND dossier.DATESUPPRESSION_DOSSIER IS NULL
            ORDER BY dossier.DATEINSERT_DOSSIER;
        ";
        
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * @param string|int $idDossier
     *
     * @return array
     */
    public function getDossierTypeNature($idDossier)
    {
        $select = "SELECT *
            FROM dossier, dossiertype, dossiernature, dossiernatureliste
            WHERE dossier.TYPE_DOSSIER = dossiertype.ID_DOSSIERTYPE
            AND dossier.ID_DOSSIER = dossiernature.ID_DOSSIER
            AND dossiernatureliste.ID_DOSSIERNATURE = dossiernature.ID_NATURE
            AND dossier.id_dossier = '".$idDossier."';
        ";
        
        return $this->getAdapter()->fetchAll($select);
    }

    public function findLastVp($idEtab)
    {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from(array('d' => 'dossier'))
                ->join(array('ed' => 'etablissementdossier'), 'ed.ID_DOSSIER = d.ID_DOSSIER')
                ->join(array('dn' => 'dossiernature'), 'd.ID_DOSSIER = dn.ID_DOSSIER')
                ->where('ed.ID_ETABLISSEMENT = ?', $idEtab)
                ->where('dn.ID_NATURE = 21 OR dn.ID_NATURE = 26')
                ->where('d.DATEVISITE_DOSSIER IS NOT NULL')
                ->order('d.DATEVISITE_DOSSIER desc')
                ->limit(1);

        return $this->getAdapter()->fetchRow($select);
    }

    public function findLastVpCreationDoc($idEtab, $idDossier, $dateVisite)
    {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from(array('d' => 'dossier'))
                ->join(array('ed' => 'etablissementdossier'), 'ed.ID_DOSSIER = d.ID_DOSSIER')
                ->join(array('dn' => 'dossiernature'), 'd.ID_DOSSIER = dn.ID_DOSSIER')
                ->where('ed.ID_ETABLISSEMENT = ?', $idEtab)
                ->where('ed.ID_DOSSIER <> ?', $idDossier)
                ->where('dn.ID_NATURE = 21 OR dn.ID_NATURE = 26')
                ->where('d.DATEVISITE_DOSSIER IS NOT NULL')
                ->where('d.DATEVISITE_DOSSIER < ?', $dateVisite)
                ->order('d.DATEVISITE_DOSSIER desc')
                ->limit(1);
        
        return $this->getAdapter()->fetchRow($select);
    }

    public function getAvisDossier($id_dossier)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('a' => 'avis'), 'LIBELLE_AVIS')
            ->join(array('d' => 'dossier'), 'd.AVIS_DOSSIER_COMMISSION = a.ID_AVIS')
            ->where('d.ID_DOSSIER = ?', $id_dossier);
        
        return $this->getAdapter()->fetchRow($select);
    }

    /**
     * @param string|int $id_dossier
     *
     * @return array
     */
    public function getEtablissementDossierGenConvoc($id_dossier)
    {
        $select = "SELECT etablissementdossier.ID_ETABLISSEMENTDOSSIER ,t1.ID_ETABLISSEMENT, LIBELLE_ETABLISSEMENTINFORMATIONS, LIBELLE_GENRE
            FROM etablissementdossier, etablissementinformations t1, genre
            WHERE etablissementdossier.ID_ETABLISSEMENT = t1.ID_ETABLISSEMENT
            AND t1.ID_GENRE = genre.ID_GENRE
            AND etablissementdossier.ID_DOSSIER = '".$id_dossier."'
            AND t1.DATE_ETABLISSEMENTINFORMATIONS = (
                SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS)
                FROM etablissementdossier, etablissementinformations
                WHERE etablissementinformations.ID_ETABLISSEMENT = t1.ID_ETABLISSEMENT
            )
            GROUP BY ID_ETABLISSEMENT;
        ";

        
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * @return array
     */
    public function listeDesDossierDateCommissionEchu($idsCommission, $sinceDays = 10, $untilDays = 100)
    {
        $ids = (array) $idsCommission;

        $select = $this->select()->setIntegrityCheck(false)
                     ->from(array('d' => 'dossier'))
                     ->joinLeft('dossierlie', 'd.ID_DOSSIER = dossierlie.ID_DOSSIER2')
                     ->join('dossiernature', 'dossiernature.ID_DOSSIER = d.ID_DOSSIER', null)
                     ->join('dossiernatureliste', 'dossiernatureliste.ID_DOSSIERNATURE = dossiernature.ID_NATURE', array('LIBELLE_DOSSIERNATURE', 'ID_DOSSIERNATURE'))
                     ->join('dossiertype', 'dossiertype.ID_DOSSIERTYPE = dossiernatureliste.ID_DOSSIERTYPE', 'LIBELLE_DOSSIERTYPE')
                     ->joinLeft('dossierdocurba', 'd.ID_DOSSIER = dossierdocurba.ID_DOSSIER', 'NUM_DOCURBA')
                     ->joinLeft(array('e' => 'etablissementdossier'), 'd.ID_DOSSIER = e.ID_DOSSIER', null)
                     ->joinLeft('avis', 'd.AVIS_DOSSIER_COMMISSION = avis.ID_AVIS')
                     ->joinLeft('dossierpreventionniste', 'dossierpreventionniste.ID_DOSSIER = d.ID_DOSSIER', null)
                     ->joinLeft('utilisateur', 'utilisateur.ID_UTILISATEUR = dossierpreventionniste.ID_PREVENTIONNISTE', 'ID_UTILISATEUR')
                     ->joinLeft('etablissementinformations', 'e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )', 'LIBELLE_ETABLISSEMENTINFORMATIONS')
                     ->joinLeft('dossieraffectation', 'dossieraffectation.ID_DOSSIER_AFFECT = d.ID_DOSSIER')
                     ->joinLeft('datecommission', 'dossieraffectation.ID_DATECOMMISSION_AFFECT = datecommission.ID_DATECOMMISSION ')
                     ->group('d.ID_DOSSIER')
                     ->where('DATEDIFF(CURDATE(), datecommission.DATE_COMMISSION) >= '.((int) $sinceDays))
                     ->where('DATEDIFF(CURDATE(), datecommission.DATE_COMMISSION) <= '.((int) $untilDays))
                     ->where('d.AVIS_DOSSIER_COMMISSION IS NULL or d.AVIS_DOSSIER_COMMISSION = 0')
                     ->order('datecommission.DATE_COMMISSION desc');

        if (count($ids) > 0) {
            $select->where('datecommission.COMMISSION_CONCERNE IN ('.implode(',', $ids).')');
        }

        return $this->getAdapter()->fetchAll($select);
    }

    public function listeDossierAvecAvisDiffere($idsCommission)
    {
        $ids = (array) $idsCommission;

        // Dossiers avec avis différé
        $search = new Model_DbTable_Search();
        $search->setItem('dossier');
        if (count($ids) > 0) {
            $search->setCriteria('d.COMMISSION_DOSSIER', $ids);
        }
        $search->setCriteria('d.DIFFEREAVIS_DOSSIER', 1);

        return $search->run(false, null, false)->toArray();
    }

    public function listeDesCourrierSansReponse($duree_en_jour = 5)
    {
        $search = new Model_DbTable_Search();
        $search->setItem('dossier');
        $search->setCriteria('d.TYPE_DOSSIER', 5);
        $search->setCriteria('d.DATEREP_DOSSIER IS NULL');
        $search->setCriteria('d.OBJET_DOSSIER IS NOT NULL');
        $search->sup('DATEDIFF(CURDATE(), d.DATEINSERT_DOSSIER)', (int) $duree_en_jour);
        $search->order('d.DATEINSERT_DOSSIER desc');

        return $search->run(false, null, false)->toArray();
    }

    //Fonction qui récup tous les établissements liés au dossier LAST VERSION
    /**
     * @param string|int $id_dossier
     *
     * @return array
     */
    public function getPreventionnistesDossier($id_dossier)
    {

        //retourne la liste des catégories de prescriptions par ordre
        $select = "SELECT usrinfos.*
            FROM dossierpreventionniste, utilisateur usr, utilisateurinformations usrinfos
            WHERE dossierpreventionniste.ID_PREVENTIONNISTE = usr.ID_UTILISATEUR
            AND usr.ID_UTILISATEURINFORMATIONS = usrinfos.ID_UTILISATEURINFORMATIONS
            AND dossierpreventionniste.ID_DOSSIER = '".$id_dossier."'
	        GROUP BY usr.ID_UTILISATEUR;
        ";
        
        return $this->getAdapter()->fetchAll($select);
    }

    // Retourne la liste de tout les dossiers d'un Etablissement
    /**
     * @param string|int $etablissement
     *
     * @return array
     */
    public function getDossiersEtab($etablissement)
    {
        $select = "SELECT dossier.ID_DOSSIER
            from dossier
            join etablissementdossier ON etablissementdossier.ID_DOSSIER = dossier.ID_DOSSIER
            where etablissementdossier.ID_ETABLISSEMENT = '".$etablissement."'
            and dossier.DATESUPPRESSION_DOSSIER IS NULL;
        ";

        return $this->getAdapter()->fetchAll($select);
    }

    // Retourne l'ID du dernier dossier donnant avis pour un établissement donné
    /**
     * @param string|int|float $idEtab
     */
    public function getDernierIdDossierDonnantAvis($idEtab)
    {
        $select = "SELECT ID_DOSSIER from (
                (SELECT d.ID_DOSSIER, d.DATECOMM_DOSSIER
                from etablissement e 
                join etablissementdossier ed ON e.ID_ETABLISSEMENT = ed.ID_ETABLISSEMENT 
                join dossier d ON ed.ID_DOSSIER = d.ID_DOSSIER
                join dossiernature dn ON d.ID_DOSSIER = dn.ID_DOSSIER
                where e.ID_ETABLISSEMENT = '$idEtab'
                and dn.ID_NATURE in (7,16,17,19,21,23,24,26,28,29,47,48)
                and d.AVIS_DOSSIER_COMMISSION in (1,2)
                and d.DATESUPPRESSION_DOSSIER IS NULL
                order by d.DATECOMM_DOSSIER desc
                limit 1)
            UNION
                (SELECT d.ID_DOSSIER, d.DATEVISITE_DOSSIER
                from etablissement e 
                join etablissementdossier ed ON e.ID_ETABLISSEMENT = ed.ID_ETABLISSEMENT 
                join dossier d ON ed.ID_DOSSIER = d.ID_DOSSIER
                join dossiernature dn ON d.ID_DOSSIER = dn.ID_DOSSIER
                where e.ID_ETABLISSEMENT = '$idEtab'
                and dn.ID_NATURE in (7,16,17,19,21,23,24,26,28,29,47,48)
                and d.AVIS_DOSSIER_COMMISSION in (1,2)
                and d.DATESUPPRESSION_DOSSIER IS NULL
                order by d.DATEVISITE_DOSSIER desc
                limit 1)
            order by DATECOMM_DOSSIER desc
            limit 1) as result;
        ";

        return $this->getAdapter()->fetchRow($select);
    }

    /**
     * Retourne la liste des dossiers provenant de plat'au et n etant lie a aucun etablissement 
     */
    public function getAllDossierPlatAU()
    {
        $select = $this->select()->setIntegrityCheck(false)
            ->from(array('d' => 'dossier'))
            ->columns(
                array(
                    'NB_URBA' => new Zend_Db_Expr("( SELECT group_concat(dossierdocurba.NUM_DOCURBA, ', ')
                        FROM dossier
                        INNER JOIN dossierdocurba ON dossierdocurba.ID_DOSSIER = dossier.ID_DOSSIER
                        WHERE dossier.ID_DOSSIER = d.ID_DOSSIER
                        LIMIT 1)")
                )
            )
            ->join(array('dt' => 'dossiertype'), 'd.TYPE_DOSSIER = dt.ID_DOSSIERTYPE', 'dt.LIBELLE_DOSSIERTYPE')
            ->join(array('dn' => 'dossiernature'), 'd.ID_DOSSIER = dn.ID_DOSSIER')
            ->join(array('dnl' => 'dossiernatureliste'), 'dn.ID_NATURE = dnl.ID_DOSSIERNATURE', 'dnl.LIBELLE_DOSSIERNATURE')
            ->where('d.ID_DOSSIER NOT IN (SELECT etablissementdossier.ID_DOSSIER from etablissementdossier)')
            ->where('d.ID_PLATAU IS NOT NULL')
        ;

        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * Retourne la liste des dossiers d un etablissement 
     */
    public function getListeDossierFromEtablissement($idEtablissement){
        $select = "SELECT * from etablissementdossier 
                            INNER JOIN dossier ON dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER 
                            WHERE etablissmentdossier.ID_ETABLISSMENT = $idEtablissement ;";
        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * Retourne la liste des dossiers d un etablissement en se basant sur un dossier
     */
    public function getListeDossierFromDossier($idDossier){
        
        $select = $this->select()->setIntegrityCheck(false)
                       ->from(array('d' => 'dossier'))
                       ->join(array('ed' => 'etablissementdossier'), 'ed.ID_DOSSIER = d.ID_DOSSIER')
                       ->join(array('e' => 'etablissement'), 'e.ID_ETABLISSEMENT = ed.ID_ETABLISSEMENT')
                       ->where("e.ID_ETABLISSEMENT = (Select etablissement.ID_ETABLISSEMENT from etablissement 
                                       inner join etablissementdossier on etablissementdossier.ID_ETABLISSEMENT = etablissement.ID_ETABLISSEMENT 
                                       inner join dossier on etablissementdossier.ID_DOSSIER = dossier.ID_DOSSIER
                                       where dossier.ID_DOSSIER = $idDossier)")
                       ->where('d.ID_DOSSIER != ?',$idDossier);
       return $this->getAdapter()->fetchAll($select);
    }


    /**
     * Retourne la liste des avis derogations d un dossier en passant l id dossier en param
     */
    public function getListAvisDerogationsFromDossier($idDossier){
        $select = $this->select()
                                ->setIntegrityCheck(false)
                                ->from("avisderogations")
                                ->where("avisderogations.ID_DOSSIER = $idDossier");
        return $this->getAdapter()->fetchAll($select);
    }
}
