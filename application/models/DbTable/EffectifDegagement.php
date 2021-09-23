<?php

class Model_DbTable_EffectifDegagement extends Zend_Db_Table_Abstract
{
    protected $_name = 'EffectifDegagement'; // Nom de la base
    protected $_primary = 'ID_EFFECTIF_DEGAGEMENT'; // Clé primaire

    /**
     * 
     * retourne le bloc effectif et degagement selon l id saisi en entree
     * @return array
     */
    public function getEffectifEtDegagement($idInput)
    {
        $select = 'SELECT *
            FROM EffectifDegagement 
            WHERE EffectifDegagement.ID_EFFECTIF_DEGAGEMENT =' .$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

    public function getEffectif($idInput){
        $select = 'SELECT EFFECTIF
            FROM EffectifDegagement 
            WHERE EffectifDegagement.ID_EFFECTIF_DEGAGEMENT =' .$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

    public function getDegagement($idInput){
        $select = 'SELECT DEGAGEMENT
            FROM EffectifDegagement 
            WHERE EffectifDegagement.ID_EFFECTIF_DEGAGEMENT =' .$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

  
    /**
     * 
     * retourne le bloc effectif et degagement selon l id saisi en entree
     * @return array
     */
    public function getEffectifEtDegagementByRef($idInput)
    {
        $select = 'SELECT *
            FROM EffectifDegagement 
            WHERE EffectifDegagement.ID_REF =' .$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

    public function getEffectifByRef($idInput){
        $select = 'SELECT EFFECTIF
            FROM EffectifDegagement 
            WHERE EffectifDegagement.ID_REF =' .$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

    public function getDegagementByRef($idInput){
        $select = 'SELECT DEGAGEMENT
            FROM EffectifDegagement 
            WHERE EffectifDegagement.ID_REF =' .$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }
}
