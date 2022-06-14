<?php

class Service_Formulaire
{
    public function getAllCapsuleRubrique(): array
    {
        $modelCapsuleRubrique = new Model_DbTable_CapsuleRubrique();

        return $modelCapsuleRubrique->fetchAll()->toArray();
    }

    public function getAllListeTypeChampRubrique(): array
    {
        $modelListeTypeChampRubrique = new Model_DbTable_ListeTypeChampRubrique();

        return $modelListeTypeChampRubrique->fetchAll()->toArray();
    }

    public function insertRubrique(array $rubrique): int
    {
        $modelCapsuleRubrique = new Model_DbTable_CapsuleRubrique();
        $modelRubrique = new Model_DbTable_Rubrique();

        $idCapsuleRubriqueArray = $modelCapsuleRubrique->getCapsuleRubriqueIdByName($rubrique['capsule_rubrique']);
        $idCapsuleRubrique = $idCapsuleRubriqueArray['ID_CAPSULERUBRIQUE'];

        $idRubrique = $modelRubrique->insert([
            'NOM' => $rubrique['nom_rubrique'],
            'DEFAULT_DISPLAY' => intval($rubrique['afficher_rubrique']),
            'ID_CAPSULERUBRIQUE' => $idCapsuleRubrique,
            'idx' => intval($rubrique['idx']),
        ]);

        return intval($idRubrique);
    }

    public function insertChamp(array $champ, array $rubrique): array
    {
        $modelChamp = new Model_DbTable_Champ();
        $modelChampValeurListe = new Model_DbTable_ChampValeurListe();
        $modelListeTypeChampRubrique = new Model_DbTable_ListeTypeChampRubrique();

        $idTypeChamp = intval($champ['type_champ']);
        $idListe = $modelListeTypeChampRubrique->getIdTypeChampByName('Liste')['ID_TYPECHAMP'];

        $dataToInsert = [
            'NOM' => $champ['nom_champ'],
            'ID_TYPECHAMP' => $idTypeChamp,
            'ID_RUBRIQUE' => $rubrique['ID_RUBRIQUE'],
            'idx' => $champ['idx'],
        ];

        if (!empty($champ['ID_CHAMP_PARENT'])) {
            $dataToInsert['ID_PARENT'] = $champ['ID_CHAMP_PARENT'];
        }

        $idChamp = $modelChamp->insert($dataToInsert);

        if ($idTypeChamp === $idListe) {
            // On récupère les valeurs de la liste séparément des autres champs
            $listValueArray = array_filter($champ, function ($key) {
                return 0 === strpos($key, 'valeur-ajout-');
            }, ARRAY_FILTER_USE_KEY);

            foreach ($listValueArray as $listValue) {
                $modelChampValeurListe->insert([
                    'VALEUR' => $listValue,
                    'ID_CHAMP' => $idChamp,
                ]);
            }
        }

        return $modelChamp->find($idChamp)->current()->toArray();
    }

    public function addRowTable(int $idChamp, int $idEntity, string $nomEntity,$idx = null):array{
        $res = [];
        //Recuperation structure ligne tableau
        $modelChamp = new Model_DbTable_Champ();
 
        $structureLigneTableau = $modelChamp->getAllFils($idChamp);        
        
        foreach ($structureLigneTableau as $champ) {
            $serviceValeur = new Service_Valeur();
            $res = $serviceValeur->insert($champ['ID_CHAMP'],$idEntity,$nomEntity,NULL,$idx);
        }
        return $res;
    }


    public function deleteRowTable(int $idChampParent, string $entity, int $idEntity,int $idx):void{
        //suppression des fk


        switch ($entity) {
            case 'Etablissement':
                $modelEtablissementValeur = new Model_DbTable_Valeur();
                $select = 
                    $modelEtablissementValeur->select()
                        ->setIntegrityCheck(false)
                        ->from(['ev' => 'etablissementvaleur'],['ev.ID_ETABLISSEMENT','ev.ID_VALEUR'])
                        ->join(['v' => 'valeur'], 'ev.ID_VALEUR = v.ID_VALEUR',[])
                        ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP',[])
                        ->where('c.ID_PARENT = ?', $idChampParent)
                        ->where('ev.ID_ETABLISSEMENT = ? ',$idEntity)
                        ->where('v.idx = ?', $idx);      
                    
                foreach($modelEtablissementValeur->fetchAll($select)->toArray() as $ev){
                    $toDelete = $modelEtablissementValeur->find(['ID_ETABLISSEMENT' => $ev['ID_ETABLISSEMENT'],'ID_VALEUR' => $ev['ID_VALEUR']])->current();
                    $toDelete->delete();
                }


                break;
            case 'Dossier':
                $modelDossierValeur = new Model_DbTable_Valeur();
                $select = 
                    $modelDossierValeur->select()
                        ->setIntegrityCheck(false)
                        ->from(['dv' => 'dossiervaleur'],['dv.ID_DOSSIER','dv.ID_VALEUR'])
                        ->join(['v' => 'valeur'], 'ev.ID_VALEUR = v.ID_VALEUR',[])
                        ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP',[])
                        ->where('dv.ID_DOSSIER = ? ',$idEntity)
                        ->where('c.ID_PARENT = ?', $idChampParent)
                        ->where('v.idx = ?', $idx);      
                    
                foreach($modelDossierValeur->fetchAll($select)->toArray() as $ev){
                    $toDelete = $modelDossierValeur->find(['ID_DOSSIER' => $ev['ID_DOSSIER'],'ID_VALEUR' => $ev['ID_VALEUR']])->current();
                    $toDelete->delete();
                }
                break;
            
        }

        
        //Suppression des valeurs 
        $modelValeur = new Model_DbTable_Valeur();
        $select = $modelValeur->select()
                    ->setIntegrityCheck(false)
                    ->from(['v' => 'valeur'])
                    ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', [''])
                    ->where('c.ID_CHAMP = ?', $idChampParent)
                    ->where('v.idx = ?', $idx);

        foreach($modelValeur->fetchAll($select)->toArray() as $ev){
            $toDelete = $modelValeur->find(['ID_VALEUR' => $ev['ID_VALEUR']])->current();
            $toDelete->delete();
        }
    }
}
