<?php

class Model_DbTable_CommissionTypeEvenement extends Zend_Db_Table_Abstract
{
    protected $_name = 'commissiontypeevenement'; // Nom de la base
    protected $_primary = 'ID_COMMISSIONTYPEEVENEMENT'; // Clé primaire

    // Fonction qui récupère toutes les infos générales d'un dossier

    /**
     * @return array
     */
    public function getCommListe()
    {
        $select = 'SELECT *
            FROM '.$this->_name.';
        ';

        return $this->getAdapter()->fetchAll($select);
    }
}
