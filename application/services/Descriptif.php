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
    private $serviceRubrique;

    public function __construct(string $capsuleRubrique, Zend_Db_Table_Abstract $modelDisplayRubrique, $serviceRubrique)
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
        $this->serviceRubrique = $serviceRubrique;
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
                if ('Parent' == $champ['TYPE']) {
                    $champ['FILS'] = $this->modelChamp->getChampFilsValue($champ['ID_CHAMP'], $idObject, $classObject);
                } else {
                    $champ['VALEUR'] = $this->serviceValeur->get($champ['ID_CHAMP'], $idObject, $classObject);
                }
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

    public function saveRubriqueDisplay(string $key, int $idElement, int $value): void
    {
        $explodedRubrique = explode('-', $key);
        $idRubrique = end($explodedRubrique);

        $this->serviceRubrique->updateRubriqueDisplay($idRubrique, $idElement, $value);
    }

    public function  saveValeurChamp(string $key, int $idObject, string $classObject, $value, int $idx = null): void
    {
            $explodedChamp = explode('-', $key);
            $idChamp = end($explodedChamp);
            $idxInput = explode('-',$inputName)[1];
            $idParent =  explode('-',$inputName)[2];
            $this->saveValeur($idChamp, $idObject, $classObject, $value, $idx);
    }


    //TODO Boucler sur toute la liste des input et tester sur chacun si une valeur a etait change que ce soit idx ou valeur propre
    //TODO Si un seule valeur est change alors on delete toute les valeurs et on recrit tout 
    //TODO pour delete appeler la route suivante 'deleteRowTable' provenant de serviceFormulaire

    public function saveValeursChamp(array $arrayInputValue, int $idObject, string $classObject):void{
        $serviceFormulaire = new Service_Formulaire();
       // $serviceFormulaire->deleteRowTable()
        foreach ($arrayInputValue as $inputName => $value) {
            $idxInput = null;
            $idChamp = null; 
            if( sizeof(explode('-',$inputName)) === 4 && !empty(explode('-',$inputName)[2]) && explode('-',$inputName)[1] !== '0'){
                $idxInput = explode('-',$inputName)[1];
                $idChamp =  explode('-',$inputName)[3];
            }
            $valueInDB = $this->modelValeur->getByChampAndObject($idChamp, $idObject, $classObject, $idxInput);
            echo('<pre>'.var_dump($valueInDB['VALEUR_STR']).'</pre>');
            echo('<pre>'.var_dump($valueInDB['VALEUR_STR'] === $value).'</pre>');
            //FIXME des etats false sortent alors qu'ils devraient etre true
        }
    }

    private function saveValeur(int $idChamp, int $idObject, string $classObject, $value, int $idx = null): void
    {
        $valueInDB = $this->modelValeur->getByChampAndObject($idChamp, $idObject, $classObject, $idx);

        if (null === $valueInDB) {
            $this->serviceValeur->insert($idChamp, $idObject, $classObject, $value,$idx);
        } else {
            $this->serviceValeur->update($idChamp, $valueInDB, $value, $idx);
        }
    }
}
