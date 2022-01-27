<?php

class Service_EtablissementDescriptif
{
    public function saveRubriqueDisplay(string $key, int $idEtablissement, int $value): void
    {
        $serviceRubrique = new Service_Rubrique();

        $explodedRubrique = explode('-', $key);
        $idRubrique = end($explodedRubrique);

        $serviceRubrique->updateRubriqueDisplay($idRubrique, $idEtablissement, $value);
    }

    public function saveValeurChamp(string $key, int $idEtablissement, $value): void
    {
        $modelValeur = new Model_DbTable_Valeur();
        $serviceValeur = new Service_Valeur();

        $explodedChamp = explode('-', $key);
        $idChamp = end($explodedChamp);

        $valueInDB = $modelValeur->getByChampAndEtablissement($idChamp, $idEtablissement);
        
        if ($valueInDB === null) {
            $serviceValeur->insert($idChamp, $idEtablissement, $value);
        } else {
            $serviceValeur->update($idChamp, $valueInDB, $value);
        }
    }

    public function saveValeurWYSIWYG(string $lastKey, int $idEtablissement, $value): void
    {
        $modelValeur = new Model_DbTable_Valeur();
        $serviceValeur = new Service_Valeur();

        $explodedChamp = explode('-', $lastKey);
        $idChamp = intval(end($explodedChamp)) + 1;

        $valueInDB = $modelValeur->getByChampAndEtablissement($idChamp, $idEtablissement);
        
        if ($valueInDB === null) {
            $serviceValeur->insert($idChamp, $idEtablissement, $value);
        } else {
            $serviceValeur->update($idChamp, $valueInDB, $value);
        }
    }
}