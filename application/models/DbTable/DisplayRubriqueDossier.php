<?php

class Model_DbTable_DisplayRubriqueDossier extends Zend_Db_Table_Abstract
{
    protected $_name = 'displayrubriquedossier'; // Nom de la base
    protected $_primary = ['ID_DOSSIER', 'ID_RUBRIQUE']; // Clé primaire

    /**
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function getUserDisplay(int $idDossier, int $idRubrique)
    {
        $select = $this->select()
            ->from(['drd' => 'displayrubriquedossier'], ['USER_DISPLAY'])
            ->where('drd.ID_DOSSIER = ?', $idDossier)
            ->where('drd.ID_RUBRIQUE = ?', $idRubrique)
        ;

        return $this->getAdapter()->fetchRow($select);
    }
}
