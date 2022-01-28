<?php

class Model_DbTable_Rubrique extends Zend_Db_Table_Abstract
{
    protected $_name = 'rubrique'; // Nom de la base
    protected $_primary = 'ID_RUBRIQUE'; // Clé primaire

    public function getRubriquesByCapsuleRubrique(string $capsuleRubrique): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('r' => 'rubrique'))
            ->join(array('cr' => 'capsulerubrique'), 'r.ID_CAPSULERUBRIQUE = cr.ID_CAPSULERUBRIQUE', array())
            ->where('cr.NOM_INTERNE = ?', $capsuleRubrique);

        return $this->fetchAll($select)->toArray();
    }
}
