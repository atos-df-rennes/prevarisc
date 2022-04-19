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
        ];

        if(!empty($champ['ID_CHAMP_PARENT'])){
            $dataToInsert["ID_PARENT"] = $champ['ID_CHAMP_PARENT'];
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
}
