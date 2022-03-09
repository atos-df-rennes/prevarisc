<?php

// FIXME Faire un service Formulaire plutôt que Etablissement ?
// Tout ce qui est là est générique peu importe l'objet
abstract class Service_Descriptif
{
    public const CAPSULE_RUBRIQUE = 'NA';

    public function setCapsuleRubrique($newValue) : void{
        $this->CAPSULE_RUBRIQUE = $newValue;
    }

    public function getRubriques(int $idObject, $classObject): array
    {
        $modelChamp = new Model_DbTable_Champ();
        $serviceValeur = new Service_Valeur();

        $modelRubrique = new Model_DbTable_Rubrique();
        $rubriques = $modelRubrique->getRubriquesByCapsuleRubrique($this->CAPSULE_RUBRIQUE, $classObject);

        foreach ($rubriques as &$rubrique) {
            $rubrique['CHAMPS'] = $modelChamp->getChampsByRubrique($rubrique['ID_RUBRIQUE']);

            foreach ($rubrique['CHAMPS'] as &$champ) {
                $champ['VALEUR'] = $serviceValeur->get($champ['ID_CHAMP'], $idObject, $classObject);
            }
        }

        return $rubriques;
    }

    public function getValeursListe(): array
    {
        $modelChampValeurListe = new Model_DbTable_ChampValeurListe();
        $champsValeurListe = $modelChampValeurListe->findAll();
        $sortedChampValeurListe = [];
        foreach ($champsValeurListe as $champValeurListe) {
            $sortedChampValeurListe[$champValeurListe['ID_CHAMP']][] = $champValeurListe;
        }

        return $sortedChampValeurListe;
    }

    public function saveRubriqueDisplay(string $key, int $idElement, $classObject,int $value): void
    {
        $serviceRubrique = NULL;
        if(strpos(strtolower($classObject),'dossier') !== false){
            $serviceRubrique = new Service_RubriqueDossier();
        }
        if(strpos(strtolower($classObject),'etablissement') !== false){
            $serviceRubrique = new Service_Rubrique();
        }

        $explodedRubrique = explode('-', $key);
        $idRubrique = end($explodedRubrique);

        $serviceRubrique->updateRubriqueDisplay($idRubrique, $idElement, $value);
    }

    public function saveValeurChamp(string $key, int $idObject, $classObject, $value): void
    {
        $explodedChamp = explode('-', $key);
        $idChamp = end($explodedChamp);

        $this->saveValeur($idChamp, $idObject, $classObject, $value);
    }

    private function saveValeur(int $idChamp, int $idObject, $classObject, $value): void
    {
        $modelValeur = new Model_DbTable_Valeur();
        $serviceValeur = new Service_Valeur();

        $valueInDB = $modelValeur->getByChampAndObject($idChamp, $idObject, $classObject);

        if (null === $valueInDB) {
            $serviceValeur->insert($idChamp, $idObject, $classObject, $value);
        } else {
            $serviceValeur->update($idChamp, $valueInDB, $value);
        }
    }
}
