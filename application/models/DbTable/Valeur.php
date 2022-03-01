<?php

class Model_DbTable_Valeur extends Zend_Db_Table_Abstract
{
    protected $_name = 'valeur'; // Nom de la base
    protected $_primary = 'ID_VALEUR'; // Clé primaire

    public function getByChampAndEtablissement(int $idChamp, int $idEtablissement)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['v' => 'valeur'])
            ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', [])
            ->join(['e' => 'etablissement'], 'v.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT', [])
            ->where('c.ID_CHAMP = ?', $idChamp)
            ->where('e.ID_ETABLISSEMENT = ?', $idEtablissement)
        ;

        $result = $this->fetchRow($select);
        if (null === $result) {
            return $result;
        }

        return $result;
    }
}
