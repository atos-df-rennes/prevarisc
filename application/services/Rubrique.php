<?php

class Service_Rubrique
{
    // Récupère le display utilisateur
    // Ou le default display si pas de display utilisateur
    public function getRubriqueDisplay(int $idRubrique, int $idEtablissement): bool
    {
        $modelRubrique = new Model_DbTable_Rubrique();
        $modelDisplayRubriqueEtablissement = new Model_DbTable_DisplayRubriqueEtablissement();

        $rubrique = $modelRubrique->find($idRubrique)->current();
        $result = $rubrique['DEFAULT_DISPLAY'];

        $userModified = $modelDisplayRubriqueEtablissement->find($idEtablissement, $idRubrique)->current();
        if ($userModified !== null) {
            $result = $userModified['USER_DISPLAY'];
        }
        
        return boolval($result);
    }

    public function updateRubriqueDisplay(int $idRubrique, int $idEtablissement, int $userDisplay): void
    {
        $modelRubrique = new Model_DbTable_Rubrique();
        $modelDisplayRubriqueEtablissement = new Model_DbTable_DisplayRubriqueEtablissement();

        $rubriqueDefaultDisplay = $modelRubrique->find($idRubrique)->current()['DEFAULT_DISPLAY'];
        $userModified = $modelDisplayRubriqueEtablissement->find($idEtablissement, $idRubrique)->current();

        // Aucun intérêt de renseigner l'information si les valeurs sont les mêmes et que l'utilisateur n'a pas modifié
        // Si l'utilisateur a déjà modifié, et qu'il remodifie, on supprime la ligne pour revenir à l'état d'origine
        if (
            ($rubriqueDefaultDisplay !== $userDisplay)
            && ($userModified === null)
        ) {
            $modelDisplayRubriqueEtablissement->insert(array(
                    'ID_ETABLISSEMENT' => $idEtablissement,
                    'ID_RUBRIQUE' => $idRubrique,
                    'USER_DISPLAY' => $userDisplay
                )
            );
        } else if (
            ($rubriqueDefaultDisplay === $userDisplay)
            && ($userModified !== null)
        ) {
            $userModified->delete();
        }
    }
}