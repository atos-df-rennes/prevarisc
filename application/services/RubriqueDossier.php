<?php

class Service_RubriqueDossier
{
    public function updateRubriqueDisplay(int $idRubrique, int $idDossier, int $userDisplay): void
    {
        $modelRubrique = new Model_DbTable_Rubrique();
        $modelDisplayRubriqueDossier = new Model_DbTable_DisplayRubriqueDossier();

        $rubriqueDefaultDisplay = $modelRubrique->find($idRubrique)->current()['DEFAULT_DISPLAY'];
        $userModified = $modelDisplayRubriqueDossier->find($idDossier, $idRubrique)->current();

        // Aucun intérêt de renseigner l'information si les valeurs sont les mêmes et que l'utilisateur n'a pas modifié
        // Si l'utilisateur a déjà modifié, et qu'il remodifie, on supprime la ligne pour revenir à l'état d'origine
        if (
            ($rubriqueDefaultDisplay !== $userDisplay)
            && (null === $userModified)
        ) {
            $modelDisplayRubriqueDossier->insert(
                [
                    'ID_DOSSIER' => $idDossier,
                    'ID_RUBRIQUE' => $idRubrique,
                    'USER_DISPLAY' => $userDisplay,
                ]
            );
        } elseif (
            ($rubriqueDefaultDisplay === $userDisplay)
            && (null !== $userModified)
        ) {
            $userModified->delete();
        }
    }
}
