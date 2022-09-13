<?php

class Service_Descriptif
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
                if ('Parent' === $champ['TYPE']) {
                    $champ['FILS'] = $this->modelChamp->getChampsFromParent($champ['ID_CHAMP']);

                    foreach ($champ['FILS'] as &$champFils) {
                        $champFils['VALEUR'] = $this->serviceValeur->get($champFils['ID_CHAMP'], $idObject, $classObject);
                    }
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
            $this->saveValeur($idChamp, $idObject, $classObject, $value, $idx);
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

    /**
     * compare l array initiale et celui pousse dans le post
     *
     * si une valeur a change de valeur reel alors on update
     * si une valeur n est plus dans l array pousse alors on le supprime
     * si une valeur est presente dans l array final alors on fait un insert
     *
     * si des id sont restant dans tableauIDValeurUpdate alors cest qu ils n ont pas ete update a la fin des boucles, on procede donc a la suppression de ces valeurs
     */
    public function saveChangeTable(array $initArrayValue, array $newArrayValue, $classObject, $idObject):void
    {

        //On enleve les inputs hidden
        array_shift($initArrayValue['RES_TABLEAU']);

        $tableauDeComparaison = [];
        $tableauIDValeurCheck = [];
        $listTypeValeur = ['VALEUR_STR', 'VALEUR_LONG_STR', 'VALEUR_INT', 'VALEUR_CHECKBOX'];

        //On mets dans le tableau de comparaison toutes les valeurs initiale (ID_VALEUR, STR_VALEUR, STR_LONG_VALEUR etc ....)
        foreach ($initArrayValue['RES_TABLEAU'] as $idxLigne => $arrayFils) {
            foreach ($arrayFils as $idChamp => $values) {
                foreach ($values as &$value) {
                    foreach ($listTypeValeur as $typeValeur) {
                        if(isset($value[$typeValeur])){
                            $value['VALEUR'] = $value[$typeValeur];
                        }
                    }
                    $tableauDeComparaison[$value['ID_VALEUR']] = $value;
                    $tableauIDValeurCheck[] = $value['ID_VALEUR'];
                }
            }
        }

        //On parcours les valeurs poussee dans le post, de cette maniere on applique le changement de valeur l insertion ou l update
        foreach ($newArrayValue as $idParent => $idxFils) {
            foreach ($idxFils as $idx => $arrayFils) {
                foreach ($arrayFils as $champ =>$newValue) {
                    //Si l ID est definie alors on check s il y a eu un changement
                    if(isset($newValue['ID_VALEUR']) && $newValue['ID_VALEUR'] !== 'NULL'){
                        //Si l idx ou la valeur a change alors on update
                        if($idx !== $tableauDeComparaison[$newValue['ID_VALEUR']]['IDX_VALEUR'] || $newValue['VALEUR'] !== $tableauDeComparaison[$newValue['ID_VALEUR']]['VALEUR']){
                            $this->saveValeur($champ,$idObject, $classObject, $newValue['VALEUR'], $idx);
                        }
                        //On retire le marquage dans le tableau d update pour bien mentionne qu on est passe sur cette valeur
                        unset($tableauIDValeurCheck[array_search($newValue['ID_VALEUR'], $tableauIDValeurCheck)]);

                    }else{
                        //L ID valeur n est pas defini on procede donc a une insertion de la valeur
                        //Si la valeur est pas null et different de '' alors on insert
                        $this->saveValeur($champ,$idObject, $classObject, $newValue['VALEUR'], $idx);
                    }
                }
            }
        }

        //On supprime les valeurs via les identifiants restant dans tableauIDValeurCheck
        foreach ($tableauIDValeurCheck as $idValueToDelete) {
            try {
            $this->modelValeur->delete('ID_VALEUR  = '.$idValueToDelete);
            } catch (\Throwable $th) {
                var_dump($th);
                die(1);
            }
        }
    }

    public function groupInputByOrder(array $initialList){
        $newList = [];

        foreach ($initialList as $inputName => $value) {
            if( sizeof(explode('-',$inputName)) === 5 && !empty(explode('-',$inputName)[2]) && explode('-',$inputName)[1] !== '0'){
                $idxInput = explode('-',$inputName)[1];
                $idParent =  explode('-',$inputName)[2];
                $idInput =  explode('-',$inputName)[3];
                $idValeur =  explode('-',$inputName)[4];

                if(!array_key_exists($idParent,$newList)){
                    $newList[$idParent] = [];
                }
                if(!array_key_exists($idxInput,$newList[$idParent])){
                    $newList[$idParent][$idxInput] = [];
                }
                $newList[$idParent][$idxInput][$idInput]['VALEUR'] = $value;
                $newList[$idParent][$idxInput][$idInput]['ID_VALEUR'] = $idValeur;
            }
        }

        $tmpList =[];
        foreach ($newList as $parent => $listIdx) {
            foreach ($listIdx as $idx => $input) {
                foreach($input as $idChamp => $valeur){
                    $tmpList[$parent][intval(array_search($idx,array_keys($listIdx)) +1)][$idChamp] = $valeur;
                }
            }
        }
        $newList = $tmpList;
        return $newList;
    }

}
