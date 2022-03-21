<?php

class Service_FusionCommand
{

    public function mergeArrayCommune($objectJson){
        foreach ($objectJson as $nouvelleFusion) {
            $this->setNewNumINSEE($nouvelleFusion->NUMINSEE, $nouvelleFusion->listeCommune);           
            $this->setAdresseRueFk($nouvelleFusion->NUMINSEE, $nouvelleFusion->listeCommune);

            $this->deleteArrayGroupementCommune($nouvelleFusion->listeCommune);
            $this->deleteArrayCommissionRegle($nouvelleFusion->listeCommune);

            $this->deleteArrayAdresseCommune($nouvelleFusion->listeCommune);
        }
    }

    public function setNewNumINSEE($newNumINSEE, $arrayOldCommune){
        $modelEtablissementAdresse = new Model_DbTable_EtablissementAdresse();
        foreach ($arrayOldCommune as $oldCommune) {
            $select = $modelEtablissementAdresse->select()
                ->from('etablissementadresse')
                ->where("etablissementadresse.NUMINSEE_COMMUNE = '$oldCommune->NUMINSEE'");
            foreach ($modelEtablissementAdresse->fetchAll($select) as $oldNumINSEE) {
                $oldNumINSEE->NUMINSEE_COMMUNE = $newNumINSEE;
                $oldNumINSEE->save();
            }
        }     
    }

    public function setAdresseRueFk($newNumINSEE, $arrayOldCommune){
        $modelAdresseRue = new Model_DbTable_AdresseRue();
        foreach ($arrayOldCommune as $oldCommune) {
            $select = 
                $modelAdresseRue->select()
                    ->setIntegrityCheck(false)
                    ->from('adresserue')
                    ->where("adresserue.NUMINSEE_COMMUNE = '$oldCommune->NUMINSEE'");
            foreach ($modelAdresseRue->fetchAll($select) as $oldNumINSEE) {
                $oldNumINSEE->NUMINSEE_COMMUNE = $newNumINSEE;
                $oldNumINSEE->save();
            }
        }    
    }

    public function deleteArrayGroupementCommune($arrayCommuneToDelete){
        $modelGroupementCommune = new Model_DbTable_GroupementCommune();
        foreach ($arrayCommuneToDelete as $communeToDelete) {
            $select = $modelGroupementCommune->select()
            ->from('groupementcommune')
            ->where("groupementcommune.NUMINSEE_COMMUNE = '$communeToDelete->NUMINSEE'");
            $toDelete = $modelGroupementCommune->fetchAll($select);        
            $modelGroupementCommune->delete('NUMINSEE_COMMUNE = '.$communeToDelete->NUMINSEE);
        }
    }

    public function deleteArrayCommissionRegle($arrayCommissionRegleToDelete){
        $modelCommissionRegle = new Model_DbTable_CommissionRegle();
        foreach ($arrayCommissionRegleToDelete as $commissionRegleToDelete) {
            $select = $modelCommissionRegle->select()
            ->from('commissionregle')
            ->where("commissionregle.NUMINSEE_COMMUNE = '$commissionRegleToDelete->NUMINSEE'");
            $toDelete = $modelCommissionRegle->fetchAll($select);        
            $modelCommissionRegle->delete('NUMINSEE_COMMUNE = '.$commissionRegleToDelete->NUMINSEE);
        }
    }

    public function deleteArrayAdresseCommune($arrayAdresseCommuneToDelete){
        $modelAdresseCommune = new Model_DbTable_AdresseCommune();
        foreach ($arrayAdresseCommuneToDelete as $adresseCommuneToDelete) {
            $select = $modelAdresseCommune->select()
            ->from('adressecommune')
            ->where("adressecommune.NUMINSEE_COMMUNE = '$adresseCommuneToDelete->NUMINSEE'");
            $toDelete = $modelAdresseCommune->fetchAll($select);        
            $modelAdresseCommune->delete('NUMINSEE_COMMUNE = '.$adresseCommuneToDelete->NUMINSEE);
        }
    }
}
?>