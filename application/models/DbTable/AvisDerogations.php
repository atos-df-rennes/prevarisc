<?php

class Model_DbTable_AvisDerogations extends Zend_Db_Table_Abstract
{
    protected $_name = 'avisderogations'; // Nom de la base
    protected $_primary = 'ID_AVIS_DEROGATION'; // Clé primaire

     /**
     * Retourne les avis avis derogation sous forme d un tableau etant lie par l etablissement
     */
     public function getByIdDossier($idDossier){
          $select = $this->select()
               ->setIntegrityCheck(false)
               ->from(['ad' => 'avisderogations'])
               ->join(['d' => 'dossier'], 'ad.ID_DOSSIER = d.ID_DOSSIER', [])
               ->joinLeft(['dl' => 'dossier'], 'ad.ID_DOSSIER_LIE = dl.ID_DOSSIER', 'OBJET_DOSSIER')
               ->where("d.ID_DOSSIER = $idDossier");

          return $this->fetchAll($select)->toArray();
     }

     /**
      * Retourne l avis derogation associe a l id passe en param
      */
     public function getByIdAvisDerogation($idAvisDerogations){
          $select = $this->select()
               ->from('avisderogations')
               ->where("ID_AVIS_DEROGATION = $idAvisDerogations");

          return $this->fetchRow($select);
     }
}
