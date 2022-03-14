<?php

abstract class Service_Descriptif
{
    private $modelChamp;
    private $modelChampValeurListe;
    private $modelRubrique;
    private $modelValeur;
    
    private $serviceValeur;

    private $capsuleRubrique;
    private $modelDisplayRubrique;

    public function __construct(string $capsuleRubrique, Zend_Db_Table_Abstract $modelDisplayRubrique)
    {
        // Services communs
        $this->modelChamp = new Model_DbTable_Champ();
        $this->modelChampValeurListe = new Model_DbTable_ChampValeurListe();
        $this->modelRubrique = new Model_DbTable_Rubrique();
        $this->modelValeur = new Model_DbTable_Valeur();
        
        $this->serviceValeur = new Service_Valeur();

        // Services spécifiques à l'objet, setter dans le Service correspondant
        $this->capsuleRubrique = $capsuleRubrique;
        $this->modelDisplayRubrique = $modelDisplayRubrique;
    }

    public function getRubriques(int $idObject, string $classObject): array
    {
        $rubriques = $this->modelRubrique->getRubriquesByCapsuleRubrique($this->capsuleRubrique);

        foreach ($rubriques as &$rubrique) {
            $userDisplay = $this->modelDisplayRubrique->getUserDisplay($idObject, $rubrique['ID_RUBRIQUE']);

            if (!empty($userDisplay)) {
                $rubrique['DISPLAY'] = $userDisplay;
            }

            $rubrique['CHAMPS'] = $this->modelChamp->getChampsByRubrique($rubrique['ID_RUBRIQUE']);

            foreach ($rubrique['CHAMPS'] as &$champ) {
                $champ['VALEUR'] = $this->serviceValeur->get($champ['ID_CHAMP'], $idObject, $classObject);
            }
        }

        return $rubriques;
    }

    public function getValeursListe(): array
    {
        $champsValeurListe = $this->modelChampValeurListe->findAll();
        $sortedChampValeurListe = [];

        foreach ($champsValeurListe as $champValeurListe) {
            $sortedChampValeurListe[$champValeurListe['ID_CHAMP']][] = $champValeurListe;
        }

        return $sortedChampValeurListe;
    }

    public function saveRubriqueDisplay(string $key, int $idElement, $classObject,int $value): void
    {
        // A faire dans les services propres comme pour le userDisplay
        $serviceRubrique = null;

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
        $valueInDB = $this->modelValeur->getByChampAndObject($idChamp, $idObject, $classObject);

        if (null === $valueInDB) {
            $this->serviceValeur->insert($idChamp, $idObject, $classObject, $value);
        } else {
            $this->serviceValeur->update($idChamp, $valueInDB, $value);
        }
    }
}