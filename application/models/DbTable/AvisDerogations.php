<?php

class Model_DbTable_AvisDerogations extends Zend_Db_Table_Abstract
{
    protected $_name = 'avisderogations'; // Nom de la base
    protected $_primary = 'ID_AVIS_DEROGATIONS'; // Clé primaire


    /**
     * Retourne les avis avis derogation sous forme d un tableau etant lie par l etablissement
     */
   public function getByIdEtablissement($idEtablissement){
        $select =   $this->select()
                    ->from('avisderogations')
                    ->where("ID_DOSSIER_LIE = $idEtablissement");
        return $this->fetchAll($select)->toArray();
   }



     /**
     * Retourne les avis avis derogation sous forme d un tableau etant lie par l etablissement
     */
    public function getByIdDossier($idEtablissement){
     $select =   $this->select()
                 ->from('avisderogations')
                 ->where("ID_DOSSIER_LIE = $idEtablissement");
     return $this->fetchAll($select)->toArray();
}
}
?>
