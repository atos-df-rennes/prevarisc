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
            $this->saveValeur($idChamp, $idObject, $classObject, $value, $idx);
    }


    //TODO Boucler sur toute la liste des input et tester sur chacun si une valeur a etait change que ce soit idx ou valeur propre
    //TODO Si un seule valeur est change alors on delete toute les valeurs et on recrit tout 
    //TODO pour delete appeler la route suivante 'deleteRowTable' provenant de serviceFormulaire

    public function saveValeursChamp(array $arrayInputValue, int $idObject, string $classObject):void{
       try {
        $serviceFormulaire = new Service_Formulaire();
        // $serviceFormulaire->deleteRowTable()
         foreach ($arrayInputValue as $inputName => $value) {
             $idxInput = null;
             $idChamp = null; 
 
             if( sizeof(explode('-',$inputName)) === 4 && !empty(explode('-',$inputName)[2]) && explode('-',$inputName)[1] !== '0'){
                 var_dump(explode('-',$inputName));
 
                 $idxInput = explode('-',$inputName)[1];
                 $idChamp =  explode('-',$inputName)[3];
             }
 
             $valueInDB = $this->modelValeur->getByChampAndObject($idChamp, $idObject, $classObject, $idxInput);
             var_dump('IDX : '.$idxInput.' Value '.$value);
             $this->saveValeur($idChamp, $idObject, $classObject, $value, $idxInput);
 
             //FIXME des etats false sortent alors qu'ils devraient etre true
         }       
        } catch (\Throwable $th) {
            var_dump($th);       
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
        array_shift($initArrayValue['RES_TABLEAU']);
        //var_dump($initArrayValue['RES_TABLEAU']);

        $tableauDeComparaison = [];
        $tableauIDValeurUpdate = [];
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
        var_dump($tableauIDValeurCheck);
        $listDBModel = [
            'Dossier' => new Model_DbTable_DossierValeur(),
            'Etablissement' => new Model_DbTable_EtablissementValeur()
        ];

        foreach ($tableauIDValeurCheck as $idValueToDelete) {
            try {
            //Suppression de la fk
            //$listDBModel[$classObject]->delete('ID_VALEUR = ?', $idValueToDelete);
            //Suppression de la valeur 
            var_dump($idValueToDelete);
            $this->modelValeur->delete('ID_VALEUR  = '.$idValueToDelete);    
            } catch (\Throwable $th) {
                var_dump($th);
                die(1);
            }

        }
        //die(1);

        
        //array_diff_key($init, array_flip($toDelete))
        
    }
}
