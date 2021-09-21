<?php

class Model_DbTable_EffectifEtDegagement extends Zend_Db_Table_Abstract
{
    protected $_name = 'EffectifEtDegagement'; // Nom de la base
    protected $_primary = 'ID_EFFECTIF_DEGAGEMENT'; // Clé primaire

    /**
     * 
     * retourne le bloc effectif et degagement selon l id saisi en entree
     * @return array
     */
    public function getEffectifEtDegagement($idInput)
    {
        $select = 'SELECT *
            FROM EffectifEtDegagement 
            WHERE EffectifEtDegagement.ID_EFFECTIF_DEGAGEMENT =' .$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }
}
