<?php

class Service_EtablissementEffectifsDegagements extends Service_Descriptif
{
    public function __construct()
    {
        parent::__construct(
            'effectifsDegagementsEtablissement',
            new Model_DbTable_DisplayRubriqueEtablissement(),
            new Service_RubriqueEtablissement()
        );
    }

    /**
     * Copie les valeurs de l'onglet "Effectifs et dégagements" d'un dossier vers l'onglet "Effectifs et dégagements" de l'établissement lié.
     * Si une rubrique ou un champ n'a pas le même nom dans la configuration, on ignore et on loggue.
     */
    public function copyValeursFromDossier(int $idEtablissement, array $rubriquesDossier, array $rubriquesEtablissement): void
    {
        $serviceChamp = new Service_Champ();

        foreach ($rubriquesDossier as $rubriqueDossier) {
            $rubriqueForCopy = $this->searchElementToCopy($rubriquesEtablissement, $rubriqueDossier['NOM'], 'NOM');

            if (null === $rubriqueForCopy) {
                error_log(\sprintf('Copie des valeurs effectifs et degagements du dossier vers l\'etablissement : La rubrique %s n\'existe pas.', $rubriqueDossier['NOM']));

                continue;
            }

            $champsDossier = $rubriqueDossier['CHAMPS'];
            foreach ($champsDossier as $champDossier) {
                $champForCopy = $this->searchElementToCopy($rubriqueForCopy['CHAMPS'], $champDossier['NOM'], 'NOM');

                if (null === $champForCopy) {
                    error_log(
                        \sprintf(
                            'Copie des valeurs effectifs et degagements du dossier vers l\'etablissement : Le champ %s n\'existe pas. (rubrique: %s)',
                            $champDossier['NOM'],
                            $rubriqueDossier['NOM']
                        )
                    );

                    continue;
                }

                if ('Parent' !== $champForCopy['TYPE']) {
                    $this->saveValeurChamp('champ-'.$champForCopy['ID_CHAMP'], $idEtablissement, 'Etablissement', $champDossier['VALEUR']);

                    continue;
                }

                if (!$serviceChamp->isTableau($champForCopy)) {
                    foreach ($champDossier['FILS'] as $enfant) {
                        $enfantToCopy = $this->searchElementToCopy($champForCopy['FILS'], $enfant['NOM'], 'NOM');

                        if (null === $enfantToCopy) {
                            error_log(
                                \sprintf(
                                    'Copie des valeurs effectifs et degagements du dossier vers l\'etablissement : Le champ enfant %s n\'existe pas. (champ: %s, rubrique: %s',
                                    $enfant['NOM'],
                                    $champDossier['NOM'],
                                    $rubriqueDossier['NOM']
                                )
                            );

                            continue;
                        }

                        $this->saveValeurChamp(
                            implode('-', ['champ', $enfantToCopy['ID_CHAMP']]),
                            $idEtablissement,
                            'Etablissement',
                            $enfant['VALEUR']
                        );
                    }

                    continue;
                }

                foreach ($champDossier['FILS']['VALEURS'] as $index => $champs) {
                    foreach ($champs as $idChamp => $data) {
                        $nomChamp = $this->searchElementToCopy($champDossier['FILS'], $idChamp, 'ID_CHAMP')['NOM'];

                        $enfantTableuToCopy = $this->searchElementToCopy($champForCopy['FILS'], $nomChamp, 'NOM');

                        if (null === $enfantTableuToCopy) {
                            error_log(
                                \sprintf(
                                    'Copie des valeurs effectifs et degagements du dossier vers l\'etablissement : Le champ enfant %s n\'existe pas. (champ: %s, rubrique: %s',
                                    $nomChamp,
                                    $champDossier['NOM'],
                                    $rubriqueDossier['NOM']
                                )
                            );

                            continue;
                        }

                        $this->saveValeurChamp(
                            implode('-', ['champ', $enfantTableuToCopy['ID_CHAMP'], $index]),
                            $idEtablissement,
                            'Etablissement',
                            $data['VALEUR']
                        );
                    }
                }
            }
        }
    }

    /**
     * @param int|string $search
     */
    private function searchElementToCopy(array $elementToSearchIn, $search, string $column): ?array
    {
        if (false === array_search($search, array_column($elementToSearchIn, $column), true)) {
            return null;
        }

        return $elementToSearchIn[array_search($search, array_column($elementToSearchIn, $column), true)];
    }
}
