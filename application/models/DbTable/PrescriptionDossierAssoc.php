<?php

class Model_DbTable_PrescriptionDossierAssoc extends Zend_Db_Table_Abstract
{
    protected $_name = 'prescriptiondossierassoc'; // Nom de la base
    protected $_primary = array('ID_PRESCRIPTION_DOSSIER', 'NUM_PRESCRIPTION_DOSSIERASSOC'); // Clé primaire

    /**
     * @return array
     */
    public function getPrescriptionDossierAssoc($idPrescriptionDossier)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('pd' => 'prescriptiondossier'))
            ->join(array('pda' => 'prescriptiondossierassoc'), 'pd.ID_PRESCRIPTION_DOSSIER = pda.ID_PRESCRIPTION_DOSSIER')
            ->join(array('pal' => 'prescriptionarticleliste'), 'pal.ID_ARTICLE = pda.ID_ARTICLE')
            ->join(array('ptl' => 'prescriptiontexteliste'), 'ptl.ID_TEXTE = pda.ID_TEXTE')
            ->where('pda.ID_PRESCRIPTION_DOSSIER = ?', $idPrescriptionDossier)
            ->order('pda.NUM_PRESCRIPTION_DOSSIERASSOC');

        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * @return array
     */
    public function getPrescriptionTypeAssoc($idPrescriptionType, $idPrescriptionDossier)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('pt' => 'prescriptiontype'))
            ->join(array('pta' => 'prescriptiontypeassoc'), 'pt.ID_PRESCRIPTIONTYPE = pta.ID_PRESCRIPTIONTYPE')
            ->join(array('pal' => 'prescriptionarticleliste'), 'pal.ID_ARTICLE = pta.ID_ARTICLE')
            ->join(array('ptl' => 'prescriptiontexteliste'), 'ptl.ID_TEXTE = pta.ID_TEXTE')
            ->join(array('pd' => 'prescriptiondossier'), 'pd.ID_PRESCRIPTION_TYPE = pt.ID_PRESCRIPTIONTYPE')
            ->where('pt.ID_PRESCRIPTIONTYPE = ?', $idPrescriptionType)
            ->where('pd.ID_PRESCRIPTION_DOSSIER = ?', $idPrescriptionDossier)
            ->order('pta.NUM_PRESCRIPTIONASSOC');

        return $this->getAdapter()->fetchAll($select);
    }

    public function deletePrescrionAssoc($idPrescriptionDossier)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('pd' => 'prescriptiondossierassoc'))
            ->where('pda.ID_PRESCRIPTION_DOSSIER = ?', $idPrescriptionDossier);

        return $this->getAdapter()->fetchAll($select)->delete();
    }
}
