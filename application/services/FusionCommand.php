<?php

class Service_FusionCommand
{

    public function mergeArrayCommune($objectJson){
        foreach ($objectJson as $nouvelleFusion) {
            $this->setNewNumINSEE($nouvelleFusion->NUMINSEE, $nouvelleFusion->listeCommune);
            $this->setAdresseRueFk($nouvelleFusion->NUMINSEE, $nouvelleFusion->listeCommune);
            $this->deleteArrayCommune($nouvelleFusion->listeCommune);            
        }
    }

    public function setNewNumINSEE($newNumINSEE, $arrayOldCommune){
        foreach ($arrayOldCommune as $oldCommune) {
            $modelEtablissementAdresse = new Model_DbTable_EtablissementAdresse();
            $select = $modelEtablissementAdresse->select()
                ->from('etablissementadresse')
                ->where("etablissementadresse.NUMINSEE_COMMUNE = '$oldCommune->NUMINSEE'");
            foreach ($modelEtablissementAdresse->fetchAll($select) as $oldNumINSEE) {
                $oldNumINSEE->NUMINSEE_COMMUNE = $newNumINSEE;
                $oldNumINSEE->save();
            }
        }     
    }

    //TODO reset les valeur des fk au niveau d adresse etablissement sinon impossible se delete les adresses commune si une clef est renseignee dans adresse rue
    //TODO voir s il faut faire une table a part 
    public function setAdresseRueFk($newNumINSEE, $arrayOldCommune){
        foreach ($arrayOldCommune as $oldCommune) {
            $modelEtablissementAdresse = new Model_DbTable_EtablissementAdresse();
            $select = $modelEtablissementAdresse->select()
                ->from('etablissementadresse')
                ->join('adresserue', 'adresserue.NUMINSEE_COMMUNE = etablissementadresse.NUMINSEE_COMMUNE')
                ->where("adresserue.NUMINSEE_COMMUNE = '$oldCommune->NUMINSEE'");
            foreach ($modelEtablissementAdresse->fetchAll($select) as $oldNumINSEE) {
                $oldNumINSEE->NUMINSEE_COMMUNE = $newNumINSEE;
                $oldNumINSEE->save();
            }
        }    
    }

    public function deleteArrayCommune($arrayCommuneToDelete){
        $modelAdresseCommune = new Model_DbTable_AdresseCommune();
        foreach ($arrayCommuneToDelete as $communeToDelete) {
            $select = $modelAdresseCommune->select()
            ->from('adressecommune')
            ->where("adressecommune.NUMINSEE_COMMUNE = '$communeToDelete->NUMINSEE'");
            $toDelete = $modelAdresseCommune->fetchAll($select);        
            $modelAdresseCommune->delete('NUMINSEE_COMMUNE = '.$communeToDelete->NUMINSEE);
        }
    }


}
?>