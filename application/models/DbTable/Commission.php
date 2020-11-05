<?php

class Model_DbTable_Commission extends Zend_Db_Table_Abstract
{
    protected $_name = 'commission'; // Nom de la base
    protected $_primary = 'ID_COMMISSION'; // Clé primaire
    protected $_referenceMap = array(
            'commissiontype' => array(
                'columns' => 'ID_COMMISSIONTYPE',
                'refTableClass' => 'Model_DbTable_CommissionType',
                'refColumns' => 'ID_COMMISSIONTYPE',
            ),
    );

    public function fetchAllPK(): array
    {
        $all = $this->getCommissions();
        $result = array();
        foreach ($all as $row) {
            $result[$row['ID_COMMISSION']] = $row;
        }

        return $result;
    }

    // Donne la liste des catégories
    /**
     * @param string|int|float $id
     *
     * @return array
     */
    public function getCommissions($id = null)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('commission')
            ->join('commissiontype', 'commission.ID_COMMISSIONTYPE = commissiontype.ID_COMMISSIONTYPE');

        if ($id != null) {
            $select->where("ID_COMMISSION = $id");

            return $this->fetchRow($select)->toArray();
        } else {
            return $this->fetchAll($select)->toArray();
        }
    }

    // Donne la liste des catégories
    /**
     * @param string|int|float $type
     *
     * @return array
     */
    public function getCommissionsByType($type)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('commission')
            ->join('commissiontype', 'commissiontype.ID_COMMISSIONTYPE = commission.ID_COMMISSIONTYPE', null)
            ->where("commission.ID_COMMISSIONTYPE = $type");

        return $this->fetchAll($select)->toArray();
    }

    /**
     * @param string|int $crit
     *
     * @return array
     */
    public function commissionListe($crit)
    {
        //Autocomplétion sur la liste des commission
        $select = "SELECT ID_COMMISSION, LIBELLE_COMMISSION
            FROM commission
            WHERE LIBELLE_COMMISSION LIKE '%".$crit."%';
        ";

        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * @return array
     */
    public function getAllCommissions()
    {
        //Récupération de l'ensemble des commissions
        $select = 'SELECT ID_COMMISSION, LIBELLE_COMMISSION
            FROM commission
            ORDER BY LIBELLE_COMMISSION
        ';

        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * @param string|int $id
     *
     * @return array
     */
    public function getLibelleCommissions($id)
    {
        //Récupération de l'ensemble des commissions
        $select = "SELECT LIBELLE_COMMISSION
            FROM commission
            WHERE ID_COMMISSION = '".$id."'";

        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * @param string|int $idCommission
     */
    public function commissionPeriodicite($idCommission)
    {
        $select = "SELECT commissiontype.FREQUENCE_COMMISSIONTYPE
            FROM commissiontype, commission
            WHERE commission.ID_COMMISSIONTYPE = commissiontype.ID_COMMISSIONTYPE
            AND commission.ID_COMMISSION = '".$idCommission."';
        ";

        return $this->getAdapter()->fetchRow($select);
    }

    /**
     * @param string|int $commune
     *
     * @return array|null
     */
    public function getCommission($commune, $categorie, $type, $localsommeil)
    {
        // Check de la sous commission / comunale / interco / arrondissement
        // Récupération des types de commission
        $model_types = new Model_DbTable_CommissionType();
        $array_typesCommission = $model_types->fetchAll(null, 'ID_COMMISSIONTYPE DESC')->toArray();

        foreach ($array_typesCommission as $row_typeCommission) {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from('commissionregle', array('ID_GROUPEMENT', 'NUMINSEE_COMMUNE', 'ID_COMMISSION'))
                ->joinLeft('commission', 'commission.ID_COMMISSION = commissionregle.ID_COMMISSION', null)
                ->joinLeft('commissionregletype', 'commissionregle.ID_REGLE = commissionregletype.ID_REGLE', null)
                ->joinLeft('commissionreglecategorie', 'commissionregle.ID_REGLE = commissionreglecategorie.ID_REGLE', null)
                ->joinLeft('commissionreglelocalsommeil', 'commissionregle.ID_REGLE = commissionreglelocalsommeil.ID_REGLE', null)
                ->joinLeft('adressecommune', 'adressecommune.NUMINSEE_COMMUNE = commissionregle.NUMINSEE_COMMUNE', null)
                ->where('commissionreglecategorie.ID_CATEGORIE = ?', $categorie)
                ->where('commissionregletype.ID_TYPE = ?', $type)
                ->where('commissionreglelocalsommeil.LOCALSOMMEIL = ?', $localsommeil)
                ->where('commission.ID_COMMISSIONTYPE = ?', $row_typeCommission['ID_COMMISSIONTYPE']);

            $results = $this->fetchAll($select);

            if ($results != null) {
                foreach ($results as $result) {
                    if ($result->NUMINSEE_COMMUNE != null) {
                        if ($result->NUMINSEE_COMMUNE == $commune) {
                            return $this->find($result->ID_COMMISSION)->toArray();
                        }
                    } elseif ($result->ID_GROUPEMENT) {
                        $model_groupementCommune = new Model_DbTable_GroupementCommune();
                        $row_groupement = $model_groupementCommune->fetchRow("ID_GROUPEMENT = '".$result->ID_GROUPEMENT."' AND NUMINSEE_COMMUNE = '".$commune."'");

                        if (count($row_groupement) === 1) {
                            return $this->find($result->ID_COMMISSION)->toArray();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string|int $commune
     *
     * @return array|null
     */
    public function getCommissionIGH($commune, $classe, $localsommeil)
    {
        // Check de la sous commission / comunale / interco / arrondissement
        // Récupération des types de commission
        $model_types = new Model_DbTable_CommissionType();
        $array_typesCommission = $model_types->fetchAll('ID_COMMISSIONTYPE != 5')->toArray();

        foreach ($array_typesCommission as $row_typeCommission) {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from('commissionregle', array('ID_GROUPEMENT', 'NUMINSEE_COMMUNE', 'ID_COMMISSION'))
                ->joinLeft('commission', 'commission.ID_COMMISSION = commissionregle.ID_COMMISSION', null)
                ->joinLeft('commissionregleclasse', 'commissionregle.ID_REGLE = commissionregleclasse.ID_REGLE', null)
                ->joinLeft('commissionreglelocalsommeil', 'commissionregle.ID_REGLE = commissionreglelocalsommeil.ID_REGLE', null)
                ->joinLeft('adressecommune', 'adressecommune.NUMINSEE_COMMUNE = commissionregle.NUMINSEE_COMMUNE', null)
                ->where('commissionregleclasse.ID_CLASSE = ?', $classe)
                ->where('commissionreglelocalsommeil.LOCALSOMMEIL = ?', $localsommeil)
                ->where('commission.ID_COMMISSIONTYPE = ?', $row_typeCommission['ID_COMMISSIONTYPE']);

            $results = $this->fetchAll($select);

            if ($results != null) {
                foreach ($results as $result) {
                    if ($result->NUMINSEE_COMMUNE != null) {
                        if ($result->NUMINSEE_COMMUNE == $commune) {
                            return $this->find($result->ID_COMMISSION)->toArray();
                        }
                    } elseif ($result->ID_GROUPEMENT) {
                        $model_groupementCommune = new Model_DbTable_GroupementCommune();
                        $row_groupement = $model_groupementCommune->fetchRow("ID_GROUPEMENT = '".$result->ID_GROUPEMENT."' AND NUMINSEE_COMMUNE = '".$commune."'");

                        if (count($row_groupement) === 1) {
                            return $this->find($result->ID_COMMISSION)->toArray();
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array[]
     *
     * @psalm-return array<int, array{NUMINSEE_COMMUNE:array<int, string|mixed>, LOCALSOMMEIL:mixed, ID_TYPE:mixed, ID_CLASSE:mixed, ID_CATEGORIE:mixed}>
     */
    public function getRegles($id_commission): array
    {
        $model_groupementCommune = new Model_DbTable_GroupementCommune();

        $regles = array();

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('commissionregle', array('ID_GROUPEMENT', 'NUMINSEE_COMMUNE'))
            ->joinLeft('commission', 'commission.ID_COMMISSION = commissionregle.ID_COMMISSION', null)
            ->joinLeft('commissionregletype', 'commissionregle.ID_REGLE = commissionregletype.ID_REGLE', array('ID_TYPE'))
            ->joinLeft('commissionreglecategorie', 'commissionregle.ID_REGLE = commissionreglecategorie.ID_REGLE', array('ID_CATEGORIE'))
                ->joinLeft('commissionregleclasse', 'commissionregle.ID_REGLE = commissionregleclasse.ID_REGLE', array('ID_CLASSE'))
            ->joinLeft('commissionreglelocalsommeil', 'commissionregle.ID_REGLE = commissionreglelocalsommeil.ID_REGLE', array('LOCALSOMMEIL'))
            ->joinLeft('adressecommune', 'adressecommune.NUMINSEE_COMMUNE = commissionregle.NUMINSEE_COMMUNE', null)
            ->where('commission.ID_COMMISSION = ?', $id_commission);

        $results = $this->fetchAll($select);

        if ($results == null) {
            return $regles;
        }

        $groupement_cache = array();
        foreach ($results as $result) {
            $communes = array();
            if ($result->NUMINSEE_COMMUNE != null) {
                $communes = array($result->NUMINSEE_COMMUNE);
            } elseif ($result->ID_GROUPEMENT != null) {
                if (!isset($groupement_cache[$result->ID_GROUPEMENT])) {
                    $groupement_cache[$result->ID_GROUPEMENT] = array();
                    $row_groupement = $model_groupementCommune->fetchAll("ID_GROUPEMENT = '".$result->ID_GROUPEMENT."'");
                    foreach ($row_groupement as $row) {
                        $groupement_cache[$result->ID_GROUPEMENT][] = $row['NUMINSEE_COMMUNE'];
                    }
                }
                $communes = $groupement_cache[$result->ID_GROUPEMENT];
            }

            $regles[] = array(
                'NUMINSEE_COMMUNE' => $communes,
                'LOCALSOMMEIL' => $result->LOCALSOMMEIL,
                'ID_TYPE' => $result->ID_TYPE,
                'ID_CLASSE' => $result->ID_CLASSE,
                'ID_CATEGORIE' => $result->ID_CATEGORIE,
            );
        }

        return $regles;
    }
}
