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
    private $serviceFormulaire;

    public function __construct(string $capsuleRubrique, Zend_Db_Table_Abstract $modelDisplayRubrique, $serviceRubrique)
    {
        // Services communs
        $this->modelChamp = new Model_DbTable_Champ();
        $this->modelChampValeurListe = new Model_DbTable_ChampValeurListe();
        $this->modelRubrique = new Model_DbTable_Rubrique();
        $this->modelValeur = new Model_DbTable_Valeur();

        $this->serviceValeur = new Service_Valeur();
        $this->serviceFormulaire = new Service_Formulaire();

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
                    if (1 === $champ['tableau']) {
                        $listValeurs = [];

                        foreach ($champ['FILS'] as &$champFils) {
                            $listValeurs[$champFils['ID_CHAMP']] = $this->serviceValeur->getAll($champFils['ID_CHAMP'], $idObject, $classObject);
                        }

                        $inputs = $this->serviceFormulaire->getInputs($champ);

                        //Affectation des valeurs
                        $champ['FILS']['VALEURS'] = $this->serviceFormulaire->getArrayValuesWithPattern($listValeurs, $inputs);

                        //Affectation des modeles type des inputs sans valeurs
                        $champ['FILS']['INPUTS'] = $inputs;
                    } else {
                        foreach ($champ['FILS'] as &$champFils) {
                            $champFils['VALEUR'] = $this->serviceValeur->get($champFils['ID_CHAMP'], $idObject, $classObject)['VALEUR'];
                            $champFils['ID_VALEUR'] = $this->serviceValeur->get($champFils['ID_CHAMP'], $idObject, $classObject)['ID_VALEUR'];

                        }
                    }
                } else {
                    $champ['VALEUR'] = $this->serviceValeur->get($champ['ID_CHAMP'], $idObject, $classObject)['VALEUR'];
                    $champ['ID_VALEUR'] = $this->serviceValeur->get($champ['ID_CHAMP'], $idObject, $classObject)['ID_VALEUR'];
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

    public function saveValeurChamp(string $key, int $idObject, string $classObject, $value, int $idx = null): void
    {
        $explodedChamp = explode('-', $key);
        $idChamp = end($explodedChamp);
        $this->saveValeur($idChamp, $idObject, $classObject, $value, $idx);
    }

    /**
     * compare l array initiale et celui pousse dans le post.
     *
     * si une valeur a change de valeur reel alors on update
     * si une valeur n est plus dans l array pousse alors on le supprime
     * si une valeur est presente dans l array final alors on fait un insert
     *
     * si des id sont restant dans tableauIDValeurUpdate alors cest qu ils n ont pas ete update a la fin des boucles, on procede donc a la suppression de ces valeurs
     *
     * @param mixed $classObject
     * @param mixed $idObject
     */
    public function saveChangeTable(array $initArrayValue, array $newArrayValue, $classObject, $idObject): void
    {
        //On enleve les inputs hidden
        //array_shift($initArrayValue['RES_TABLEAU']);

        $tableauDeComparaison = [];
        $tableauIDValeurCheck = [];

        //On mets dans le tableau de comparaison toutes les valeurs initiale (ID_VALEUR, STR_VALEUR, STR_LONG_VALEUR etc ....) pour chaque ID valeur

        foreach ($initArrayValue as $idxRubrique => $rubrique) {
           foreach ($rubrique['CHAMPS'] as $champ) {
                if($champ['tableau'] === 1){
                    foreach($champ['FILS']['VALEURS'] as $valeursFils){
                        foreach($valeursFils as $valeur){
                            $tableauDeComparaison[$valeur['ID_VALEUR']] = $valeur;
                            $tableauIDValeurCheck[$valeur['ID_VALEUR']] = $valeur['ID_VALEUR'];
                        }
                    }
                }
            }
        }

        foreach($newArrayValue as $champParent){
            foreach($champParent as $newIdxValeur => $valeurs){
                foreach($valeurs as $idChamp => $valeur){
                    //On retire le marquage des valeurs sur lequel on est deja passee
                    unset($tableauIDValeurCheck[array_search($valeur['ID_VALEUR'], $tableauIDValeurCheck)]);
                    //Si la valeur vient d etre ajoute alors on insert en DB
                    //Ou
                    if($valeur['ID_VALEUR'] === 'NULL'){
                        $this->serviceValeur->insert($idChamp, $idObject, $classObject, $valeur['VALEUR'],$newIdxValeur);
                    }else{
                        if(//Si l index a change
                            $newIdxValeur !== $tableauDeComparaison[$valeur['ID_VALEUR']]['IDX_VALEUR']
                            ||
                            //Ou la valeur brut a change
                            $valeur['VALEUR'] !== $tableauDeComparaison[$valeur['ID_VALEUR']]['VALEUR']
                            //Alors on update

                            ){
                                $valueInDB = $this->modelValeur->getByChampAndObject($idChamp, $idObject, $classObject, $tableauDeComparaison[$valeur['ID_VALEUR']]['IDX_VALEUR']);
                                $valueInDB = $this->modelValeur->find($valueInDB['ID_VALEUR'])->current();
                                $this->serviceValeur->update($idChamp, $valueInDB, $valeur['VALEUR'], $newIdxValeur);
                        }
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
                exit(1);
            }
        }
    }

    public function groupInputByOrder(array $initialList)
    {
        $newList = [];

        foreach ($initialList as $inputName => $value) {
            if (5 === sizeof(explode('-', $inputName)) && !empty(explode('-', $inputName)[2]) && '0' !== explode('-', $inputName)[1]) {
                $idxInput = explode('-', $inputName)[1];
                $idParent = explode('-', $inputName)[2];
                $idInput = explode('-', $inputName)[3];
                $idValeur = explode('-', $inputName)[4];

                if (!array_key_exists($idParent, $newList)) {
                    $newList[$idParent] = [];
                }
                if (!array_key_exists($idxInput, $newList[$idParent])) {
                    $newList[$idParent][$idxInput] = [];
                }
                $newList[$idParent][$idxInput][$idInput]['VALEUR'] = $value;
                $newList[$idParent][$idxInput][$idInput]['ID_VALEUR'] = $idValeur;
            }
        }

        $tmpList = [];
        foreach ($newList as $parent => $listIdx) {
            foreach ($listIdx as $idx => $input) {
                foreach ($input as $idChamp => $valeur) {
                    $tmpList[$parent][intval(array_search($idx, array_keys($listIdx)) + 1)][$idChamp] = $valeur;
                }
            }
        }

        return $tmpList;
    }

    private function saveValeur(int $idChamp, int $idObject, string $classObject, $value, int $idx = null): void
    {
        $valueInDB = $this->modelValeur->getByChampAndObject($idChamp, $idObject, $classObject, $idx);
        $valueInDB = $this->modelValeur->find($valueInDB['ID_VALEUR'])->current();

        if (null === $valueInDB) {
            $this->serviceValeur->insert($idChamp, $idObject, $classObject, $value, $idx);
        } else {
            $this->serviceValeur->update($idChamp, $valueInDB, $value, $idx);
        }
    }

}
