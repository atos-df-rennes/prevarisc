<?php

class Model_DbTable_CapsuleRubrique extends Zend_Db_Table_Abstract
{
    protected $_name = 'capsulerubrique'; // Nom de la base
    protected $_primary = 'ID_CAPSULERUBRIQUE'; // Clé primaire

    public function getCapsuleRubriqueIdByName(string $name): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('capsulerubrique', array('ID_CAPSULERUBRIQUE'))
            ->where('NOM_INTERNE = ?', $name);

        return $this->fetchRow($select)->toArray();
    }
}