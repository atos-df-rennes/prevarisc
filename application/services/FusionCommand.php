<?php

class Service_FusionCommand
{
    public function mergeArrayCommune(array $objectJson): void
    {
        foreach ($objectJson as $nouvelleFusion) {
            $this->setNewNumINSEE($nouvelleFusion->NUMINSEE, $nouvelleFusion->listeCommune);
            $this->setAdresseRueFk($nouvelleFusion->NUMINSEE, $nouvelleFusion->listeCommune);

            $this->deleteArrayGroupementCommune($nouvelleFusion->listeCommune);
            $this->deleteArrayCommissionRegle($nouvelleFusion->listeCommune);

            $this->deleteArrayAdresseCommune($nouvelleFusion->listeCommune);
        }
    }

    public function setNewNumINSEE(string $newNumINSEE, array $arrayOldCommune): void
    {
        $modelEtablissementAdresse = new Model_DbTable_EtablissementAdresse();
        foreach ($arrayOldCommune as $oldCommune) {
            $select = $modelEtablissementAdresse->select()
                ->from('etablissementadresse')
                ->where("etablissementadresse.NUMINSEE_COMMUNE = '{$oldCommune->NUMINSEE}'")
            ;

            $oldCommunes = $modelEtablissementAdresse->fetchAll($select);
            foreach ($oldCommunes as $oldNumINSEE) {
                $oldNumINSEE->NUMINSEE_COMMUNE = $newNumINSEE;
                $oldNumINSEE->save();
            }
        }
    }

    public function setAdresseRueFk(string $newNumINSEE, array $arrayOldCommune): void
    {
        $modelAdresseRue = new Model_DbTable_AdresseRue();
        foreach ($arrayOldCommune as $oldCommune) {
            $select = $modelAdresseRue->select()
                ->from('adresserue')
                ->where("adresserue.NUMINSEE_COMMUNE = '{$oldCommune->NUMINSEE}'")
            ;

            $oldCommunes = $modelAdresseRue->fetchAll($select);
            foreach ($oldCommunes as $oldNumINSEE) {
                $oldNumINSEE->NUMINSEE_COMMUNE = $newNumINSEE;
                $oldNumINSEE->save();
            }
        }
    }

    public function deleteArrayGroupementCommune(array $arrayCommuneToDelete): void
    {
        $modelGroupementCommune = new Model_DbTable_GroupementCommune();
        foreach ($arrayCommuneToDelete as $communeToDelete) {
            $modelGroupementCommune->delete('NUMINSEE_COMMUNE = '.$communeToDelete->NUMINSEE);
        }
    }

    public function deleteArrayCommissionRegle($arrayCommissionRegleToDelete): void
    {
        $modelCommissionRegle = new Model_DbTable_CommissionRegle();
        foreach ($arrayCommissionRegleToDelete as $commissionRegleToDelete) {
            $modelCommissionRegle->delete('NUMINSEE_COMMUNE = '.$commissionRegleToDelete->NUMINSEE);
        }
    }

    public function deleteArrayAdresseCommune($arrayAdresseCommuneToDelete): void
    {
        $modelAdresseCommune = new Model_DbTable_AdresseCommune();
        foreach ($arrayAdresseCommuneToDelete as $adresseCommuneToDelete) {
            $modelAdresseCommune->delete('NUMINSEE_COMMUNE = '.$adresseCommuneToDelete->NUMINSEE);
        }
    }
}
