<?php

class Service_FusionCommand
{
    public function mergeArrayCommune(array $objectJson): int
    {
        foreach ($objectJson as $nouvelleFusion) {
            if ($this->checkExists($nouvelleFusion)) {
                return true;
            }

            $this->setNewNumINSEE($nouvelleFusion->NUMINSEE, $nouvelleFusion->listeCommune);
            $this->setAdresseRueFk($nouvelleFusion->NUMINSEE, $nouvelleFusion->listeCommune);

            $this->deleteArrayGroupementCommune($nouvelleFusion->listeCommune);
            $this->deleteArrayCommissionRegle($nouvelleFusion->listeCommune);

            $this->deleteArrayAdresseCommune($nouvelleFusion->listeCommune);
        }

        return 0;
    }

    public function checkExists(stdClass $nouvelleFusion): int
    {
        $modelAdresseCommune = new Model_DbTable_AdresseCommune();

        $numinseeQuery = $modelAdresseCommune->select()
            ->where("NUMINSEE_COMMUNE = '{$nouvelleFusion->NUMINSEE}'")
        ;
        $numinseeResult = $modelAdresseCommune->fetchAll($numinseeQuery)->toArray();
        $numinseeCount = count($numinseeResult);

        if (0 === $numinseeCount) {
            error_log("Le numero INSEE {$nouvelleFusion->NUMINSEE} n'existe pas dans la base de donnees, veuillez l'ajouter");

            return true;
        }
        if ($numinseeCount > 1) {
            error_log("Il existe plusieurs lignes avec le numero INSEE {$nouvelleFusion->NUMINSEE}, veuillez en supprimer");

            return true;
        }

        $libelleCommune = $numinseeResult['0']['LIBELLE_COMMUNE'];
        if (0 !== strcmp($nouvelleFusion->nomCommune, $libelleCommune)) {
            error_log("Le numero INSEE {$nouvelleFusion->NUMINSEE} n'a pas pour libelle {$nouvelleFusion->nomCommune}, veuillez faire la mise à jour");

            return true;
        }

        return false;
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

    public function deleteArrayCommissionRegle(array $arrayCommissionRegleToDelete): void
    {
        $modelCommissionRegle = new Model_DbTable_CommissionRegle();

        foreach ($arrayCommissionRegleToDelete as $commissionRegleToDelete) {
            $modelCommissionRegle->delete('NUMINSEE_COMMUNE = '.$commissionRegleToDelete->NUMINSEE);
        }
    }

    public function deleteArrayAdresseCommune(array $arrayAdresseCommuneToDelete): void
    {
        $modelAdresseCommune = new Model_DbTable_AdresseCommune();

        foreach ($arrayAdresseCommuneToDelete as $adresseCommuneToDelete) {
            $modelAdresseCommune->delete('NUMINSEE_COMMUNE = '.$adresseCommuneToDelete->NUMINSEE);
        }
    }
}
