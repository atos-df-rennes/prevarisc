<?php

class Model_DbTable_DisplayRubriqueEtablissement extends Zend_Db_Table_Abstract
{
    protected $_name = 'displayrubriqueetablissement'; // Nom de la base
    protected $_primary = ['ID_ETABLISSEMENT', 'ID_RUBRIQUE']; // Clé primaire

    public function getUserDisplay(int $idEtablissement, int $idRubrique): array
    {
        $select = $this->select()
            ->from(['dre' => 'displayrubriqueetablissement'], ['ID_RUBRIQUE', 'USER_DISPLAY'])
            ->where('dre.ID_ETABLISSEMENT = ?', $idEtablissement)
            ->where('dre.ID_RUBRIQUE = ?', $idRubrique)
        ;

        return $this->getAdapter()->fetchAll($select);
    }
}
