<?php

class Model_DbTable_Champ extends Zend_Db_Table_Abstract
{
    protected $_name = 'champ'; // Nom de la base
    protected $_primary = 'ID_CHAMP'; // Clé primaire

    public function getChampsByRubrique(int $idRubrique): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('c' => 'champ'), array('ID_CHAMP', 'NOM'))
            ->join(array('r' => 'rubrique'), 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', array())
            ->join(array('ltcr' => 'listetypechamprubrique'), 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', array('TYPE'))
            ->where('r.ID_RUBRIQUE = ?', $idRubrique);

        return $this->fetchAll($select)->toArray();
    }
}