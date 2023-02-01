<?php

class Model_DbTable_AdresseRue extends Zend_Db_Table_Abstract
{
    protected $_name = 'adresserue'; // Nom de la base
    protected $_primary = 'ID_RUE'; // Clé primaire

    public function getLibelleRue($idRue)
    {
        $select = $this->select()->setIntegrityCheck(false);

        $select->from('adresserue')
            ->where('ID_RUE = ?', $idRue)
        ;

        return $this->getAdapter()->fetchRow($select);
    }
}
