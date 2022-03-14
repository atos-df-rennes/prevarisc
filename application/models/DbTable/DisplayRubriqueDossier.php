<?php

class Model_DbTable_DisplayRubriqueDossier extends Zend_Db_Table_Abstract
{
    protected $_name = 'displayrubriquedossier'; // Nom de la base
    protected $_primary = ['ID_DOSSIER', 'ID_RUBRIQUE']; // Clé primaire

    public function getUserDisplay(int $idDossier, int $idRubrique): array
    {
        $select = $this->select()
            ->from(['drd' => 'displayrubriquedossier'], ['ID_RUBRIQUE', 'USER_DISPLAY'])
            ->where('drd.ID_DOSSIER = ?', $idDossier)
            ->where('drd.ID_RUBRIQUE = ?', $idRubrique)
        ;

        return $this->getAdapter()->fetchAll($select);
    }
}
