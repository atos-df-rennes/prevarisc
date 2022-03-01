<?php

class Model_DbTable_ListeTypeChampRubrique extends Zend_Db_Table_Abstract
{
    protected $_name = 'listetypechamprubrique'; // Nom de la base
    protected $_primary = 'ID_TYPECHAMP'; // Clé primaire

    public function getIdTypeChampByName(string $name): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('listetypechamprubrique', ['ID_TYPECHAMP'])
            ->where('TYPE = ?', $name)
        ;

        return $this->fetchRow($select)->toArray();
    }
}
