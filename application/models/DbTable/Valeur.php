<?php

class Model_DbTable_Valeur extends Zend_Db_Table_Abstract
{
    protected $_name = 'valeur'; // Nom de la base
    protected $_primary = 'ID_VALEUR'; // Clé primaire

    public function getByChampAndEtablissement(int $idChamp, int $idEtablissement)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('v' => 'valeur'))
            ->join(array('c' => 'champ'), 'v.ID_CHAMP = c.ID_CHAMP', array())
            ->join(array('e' => 'etablissement'), 'v.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT', array())
            ->where('c.ID_CHAMP = ?', $idChamp)
            ->where('e.ID_ETABLISSEMENT = ?', $idEtablissement);

        $result = $this->fetchRow($select);
        if ($result === null) {
            return $result;
        }

        return $result;
    }
}