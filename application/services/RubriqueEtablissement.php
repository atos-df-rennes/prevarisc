<?php

class Service_RubriqueEtablissement
{
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
            && (!$userModified instanceof \Zend_Db_Table_Row_Abstract)
        ) {
            $modelDisplayRubriqueEtablissement->insert(
                [
                    'ID_ETABLISSEMENT' => $idEtablissement,
                    'ID_RUBRIQUE' => $idRubrique,
                    'USER_DISPLAY' => $userDisplay,
                ]
            );
        } elseif (
            ($rubriqueDefaultDisplay === $userDisplay)
            && ($userModified instanceof \Zend_Db_Table_Row_Abstract)
        ) {
            $userModified->delete();
        }
    }
}
