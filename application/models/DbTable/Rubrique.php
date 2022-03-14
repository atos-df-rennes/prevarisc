<?php

class Model_DbTable_Rubrique extends Zend_Db_Table_Abstract
{
    protected $_name = 'rubrique'; // Nom de la base
    protected $_primary = 'ID_RUBRIQUE'; // Clé primaire

    // FIXME Mettre à jour pour remettre en standard, on a plus besoin du adminView
    public function getRubriquesByCapsuleRubrique(string $capsuleRubrique, bool $adminView = false): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['r' => 'rubrique'], ['ID_RUBRIQUE', 'NOM'])
            ->columns(
                ['DISPLAY' => 'r.DEFAULT_DISPLAY']
            )
            ->join(['cr' => 'capsulerubrique'], 'r.ID_CAPSULERUBRIQUE = cr.ID_CAPSULERUBRIQUE', [])
            ->where('cr.NOM_INTERNE = ?', $capsuleRubrique)
            ->order('r.Id_RUBRIQUE')
        ;

        return $this->fetchAll($select)->toArray();
    }
}
