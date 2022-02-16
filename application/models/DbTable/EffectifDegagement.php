<?php

class Model_DbTable_EffectifDegagement extends Zend_Db_Table_Abstract
{
    protected $_name = 'effectifDegagement'; // Nom de la base
    protected $_primary = 'ID_EFFECTIF_DEGAGEMENT'; // Clé primaire

    /**
     * retourne le bloc effectif et degagement selon l id saisi en entree.
     *
     * @param mixed $idInput
     *
     * @return array
     */
    public function getEffectifEtDegagement($idInput)
    {
        $select = 'SELECT *
            FROM effectifDegagement 
            WHERE effectifDegagement.ID_EFFECTIF_DEGAGEMENT ='.$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

    public function getEffectif($idInput)
    {
        $select = 'SELECT EFFECTIF
            FROM effectifDegagement 
            WHERE effectifDegagement.ID_EFFECTIF_DEGAGEMENT ='.$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

    public function getDegagement($idInput)
    {
        $select = 'SELECT DEGAGEMENT
            FROM effectifDegagement 
            WHERE effectifDegagement.ID_EFFECTIF_DEGAGEMENT ='.$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * retourne le bloc effectif et degagement selon l id saisi en entree.
     *
     * @param mixed $idInput
     *
     * @return array
     */
    public function getEffectifEtDegagementByRef($idInput)
    {
        $select = 'SELECT *
            FROM effectifDegagement 
            WHERE effectifDegagement.ID_DOSSIER ='.$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

    public function getEffectifDegagementByDossier($idInput)
    {
        $select = 'SELECT * FROM 
                    effectifDegagement INNER JOIN dossierEffectifDegagement ON effectifDegagement.ID_EFFECTIF_DEGAGEMENT = dossierEffectifDegagement.ID_EFFECTIF_DEGAGEMENT 
                    WHERE dossierEffectifDegagement.ID_DOSSIER = '.$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

    public function getIDEffectifDegagementByIDDossier($idInput)
    {
        $select = 'SELECT ID_EFFECTIF_DEGAGEMENT FROM dossierEffectifDegagement WHERE dossierEffectifDegagement.ID_DOSSIER = '.$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }

    public function getEffectifDegagementByIDEtablissement($idInput)
    {
        $select = 'SELECT * FROM etablissementEffectifDegagement INNER JOIN effectifDegagement ON etablissementEffectifDegagement.ID_EFFECTIF_DEGAGEMENT = effectifDegagement.ID_EFFECTIF_DEGAGEMENT WHERE etablissementEffectifDegagement.ID_ETABLISSEMENT = '.$idInput.';';

        return $this->getAdapter()->fetchAll($select);
    }
}
