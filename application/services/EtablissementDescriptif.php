<?php

class Service_EtablissementDescriptif
{
    public function getRubriques(int $idEtablissement): array
    {
        $modelRubrique = new Model_DbTable_Rubrique();
        $serviceRubrique = new Service_Rubrique();

        $rubriques = $modelRubrique->getRubriquesByCapsuleRubrique(EtablissementController::CAPSULE_RUBRIQUE);
        foreach ($rubriques as &$rubrique) {
            $rubrique['DISPLAY'] = $serviceRubrique->getRubriqueDisplay($rubrique['ID_RUBRIQUE'], $idEtablissement);
        }

        return $rubriques;
    }

    public function getChamps($idEtablissement): array
    {
        $modelChamp = new Model_DbTable_Champ();
        $serviceValeur = new Service_Valeur();

        $champs = $modelChamp->findAll();
        foreach ($champs as &$champ) {
            $champ['VALEUR'] = $serviceValeur->get($champ['ID_CHAMP'], $idEtablissement);
        }

        $sortedChamps =  [];
        foreach ($champs as &$champ) {
            $sortedChamps[$champ['ID_RUBRIQUE']][] = $champ;
        }

        return $sortedChamps;
    }

    public function getValeursListe(): array
    {
        $modelChampValeurListe = new Model_DbTable_ChampValeurListe();

        $champsValeurListe = $modelChampValeurListe->findAll();
        
        $sortedChampValeurListe =  [];
        foreach ($champsValeurListe as $champValeurListe) {
            $sortedChampValeurListe[$champValeurListe['ID_CHAMP']][] = $champValeurListe;
        }

        return $sortedChampValeurListe;
    }

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
            if ($value !== '') { 
                $serviceValeur->insert($idChamp, $idEtablissement, $value);
            }
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