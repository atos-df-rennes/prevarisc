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

    // TODO A déplacer dans le Service Rubrique
    public function insertRubrique(array $rubrique): int
    {
        $modelCapsuleRubrique = new Model_DbTable_CapsuleRubrique();
        $modelRubrique = new Model_DbTable_Rubrique();

        $idCapsuleRubriqueArray = $modelCapsuleRubrique->getCapsuleRubriqueIdByName($rubrique['capsule_rubrique']);
        $idCapsuleRubrique = $idCapsuleRubriqueArray['ID_CAPSULERUBRIQUE'];

        $idRubrique = $modelRubrique->insert(array(
            'NOM' => $rubrique['nom_rubrique'],
            'DEFAULT_DISPLAY' => intval($rubrique['afficher_rubrique']),
            'ID_CAPSULERUBRIQUE' => $idCapsuleRubrique
        ));

        return intval($idRubrique);
    }

    // TODO A déplacer dans le Service Champ
    public function insertChamp(array $champ, array $rubrique): array
    {
        $modelChamp = new Model_DbTable_Champ();
        $modelChampValeurListe = new Model_DbTable_ChampValeurListe();
        $modelListeTypeChampRubrique = new Model_DbTable_ListeTypeChampRubrique();

        $idTypeChamp = intval($champ['type_champ']);
        $idListe = $modelListeTypeChampRubrique->getIdTypeChampByName('Liste')['ID_TYPECHAMP'];

        $idChamp = $modelChamp->insert(array(
            'NOM' => $champ['nom_champ'],
            'ID_TYPECHAMP' => $idTypeChamp,
            'ID_RUBRIQUE' => $rubrique['ID_RUBRIQUE']
        ));

        if ($idTypeChamp === $idListe) {
            // On récupère les valeurs de la liste séparément des autres champs
            $listValueArray = array_filter($champ, function ($key) {
                return strpos($key, 'valeur-') === 0;
            }, ARRAY_FILTER_USE_KEY);

            foreach ($listValueArray as $listValue) {
                $modelChampValeurListe->insert(array(
                    'VALEUR' => $listValue,
                    'ID_CHAMP' => $idChamp
                ));
            }
        }

        return $modelChamp->find($idChamp)->current()->toArray();
    }
}
