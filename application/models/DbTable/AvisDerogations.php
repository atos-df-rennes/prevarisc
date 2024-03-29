<?php

class Model_DbTable_AvisDerogations extends Zend_Db_Table_Abstract
{
    protected $_name = 'avisderogations'; // Nom de la base
    protected $_primary = 'ID_AVIS_DEROGATION'; // Clé primaire

    /**
     * Retourne l avis derogation associe a l id passe en param.
     *
     * @param mixed $idAvisDerogations
     */
    public function getByIdAvisDerogation($idAvisDerogations)
    {
        $select = $this->select()
            ->from('avisderogations')
            ->where('ID_AVIS_DEROGATION = ?', $idAvisDerogations)
        ;

        return $this->fetchRow($select);
    }
}
