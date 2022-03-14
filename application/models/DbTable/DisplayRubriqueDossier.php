<?php

class Model_DbTable_DisplayRubriqueDossier extends Zend_Db_Table_Abstract
{
    protected $_name = 'displayrubriqueDossier'; // Nom de la base
    protected $_primary = ['ID_DOSSIER', 'ID_RUBRIQUE']; // Clé primaire

    public function getUserDisplay(int $idDossier, int $idRubrique): array
    {
        $select = $this->select()
            ->from(['drD' => 'displayrubriqueDossier'], ['ID_RUBRIQUE', 'USER_DISPLAY'])
            ->where('drD.ID_DOSSIER = ?', $idDossier)
            ->where('drD.ID_RUBRIQUE = ?', $idRubrique)
        ;

        return $this->getAdapter()->fetchAll($select);
    }
}
