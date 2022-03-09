<?php

class Model_DbTable_ValeurDossier extends Zend_Db_Table_Abstract
{
    protected $_name = 'valeur'; // Nom de la base
    protected $_primary = 'ID_VALEUR'; // Clé primaire

    public function getByChampAndDossier(int $idChamp, int $idDossier)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['v' => 'valeur'])
            ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', [])
            ->join(['e' => 'dossier'], 'v.ID_DOSSIER = e.ID_DOSSIER', [])
            ->where('c.ID_CHAMP = ?', $idChamp)
            ->where('e.ID_DOSSIER = ?', $idEtablissement)
        ;

        $result = $this->fetchRow($select);
        if (null === $result) {
            return $result;
        }

        return $result;
    }
}
