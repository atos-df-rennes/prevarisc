<?php

class Model_DbTable_AvisDerogations extends Zend_Db_Table_Abstract
{
    protected $_name = 'avisderogations'; // Nom de la base
    protected $_primary = 'ID_AVIS_DEROGATION'; // Clé primaire


    /**
     * Retourne les avis avis derogation sous forme d un tableau etant lie par l etablissement
     */
   public function getByIdEtablissement($idEtablissement){
        $select =   $this->select()
                    ->from('avisderogations')
                    ->where("ID_DOSSIER = $idEtablissement");
        return $this->fetchAll($select)->toArray();
   }



     /**
     * Retourne les avis avis derogation sous forme d un tableau etant lie par l etablissement
     */
    public function getByIdDossier($idDossier){
     $select =   $this->select()
                 ->from('avisderogations')
                 ->where("ID_DOSSIER = $idDossier");
     return $this->fetchAll($select)->toArray();
     }


     /**
      * Retourne l avis derogation associe a l id passe en param
      */
     public function getByIdAvisDerogation($idAvisDerogations){
          $select =   $this->select()
          ->from('avisderogations')
          ->where("ID_AVIS_DEROGATION = $idAvisDerogations");
          return $this->fetchAll($select)->toArray();     
     }



     /**
     * Retourne le nombre d avis et derogation rattacher au dossier
     */
    public function getNbAvisDerogationFromDossier($idDossier){
          $select = $this->select()->setIntegrityCheck(false);
          $select->from('avisderogations');
          return sizeof($this->fetchAll($select)->toArray());
     }
}
?>
