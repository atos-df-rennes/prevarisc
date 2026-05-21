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
     */
    public function copyValeursFromDossier(int $idEtablissement, array $rubriquesDossier, array $rubriquesEtablissement): void
    {
        $this->copyValeursDossierEtablissement($idEtablissement, 'Etablissement', $rubriquesDossier, $rubriquesEtablissement);
    }

    /**
     * Copie les valeurs de l'onglet "Effectifs et dégagements" d'un établissement vers l'onglet "Effectifs et dégagements" du dossier courant.
     */
    public function copyValeursToDossier(int $idDossier, array $rubriquesDossier, array $rubriquesEtablissement): void
    {
        $this->copyValeursDossierEtablissement($idDossier, 'Dossier', $rubriquesEtablissement, $rubriquesDossier);
    }

    /**
     * Copie les valeurs de l'onglet "Effectifs et dégagements" d'un dossier ou établissement vers l'onglet "Effectifs et dégagements" de l'autre entité.
     * Si une rubrique ou un champ n'a pas le même nom dans la configuration, on ignore et on loggue.
     */
    private function copyValeursDossierEtablissement(int $idObject, string $object, array $rubriquesFrom, array $rubriquesTo): void
    {
        $serviceChamp = new Service_Champ();
        $serviceValeur = new Service_Valeur();

        foreach ($rubriquesFrom as $rubriqueFrom) {
            $rubriqueForCopy = $this->searchElementToCopy($rubriquesTo, $rubriqueFrom['NOM'], 'NOM');

            if (null === $rubriqueForCopy) {
                error_log(\sprintf("Copie des valeurs effectifs et degagements entre dossier et etablissement : La rubrique %s n'existe pas.", $rubriqueFrom['NOM']));

                continue;
            }

            $champsFrom = $rubriqueFrom['CHAMPS'];
            foreach ($champsFrom as $champFrom) {
                $champForCopy = $this->searchElementToCopy($rubriqueForCopy['CHAMPS'], $champFrom['NOM'], 'NOM');

                if (null === $champForCopy) {
                    error_log(
                        \sprintf(
                            "Copie des valeurs effectifs et degagements entre dossier et etablissement : Le champ %s n'existe pas. (rubrique: %s)",
                            $champFrom['NOM'],
                            $rubriqueFrom['NOM']
                        )
                    );

                    continue;
                }

                // Copie des champs simples
                if ('Parent' !== $champForCopy['TYPE']) {
                    $this->saveValeurChamp('champ-'.$champForCopy['ID_CHAMP'], $idObject, $object, $champFrom['VALEUR']);

                    continue;
                }

                // Suppression des valeurs existantes du champ parent avant copie
                $serviceValeur->deleteValeursChampParent($champForCopy['ID_CHAMP'], $idObject, $object);

                // Copie des champs parents non tableaux
                if (!$serviceChamp->isTableau($champForCopy)) {
                    foreach ($champFrom['FILS'] as $enfant) {
                        $enfantToCopy = $this->searchElementToCopy($champForCopy['FILS'], $enfant['NOM'], 'NOM');

                        if (null === $enfantToCopy) {
                            error_log(
                                \sprintf(
                                    "Copie des valeurs effectifs et degagements entre dossier et etablissement : Le champ enfant %s n'existe pas. (champ: %s, rubrique: %s",
                                    $enfant['NOM'],
                                    $champFrom['NOM'],
                                    $rubriqueFrom['NOM']
                                )
                            );

                            continue;
                        }

                        $this->saveValeurChamp(
                            implode('-', ['champ', $enfantToCopy['ID_CHAMP']]),
                            $idObject,
                            $object,
                            $enfant['VALEUR']
                        );
                    }

                    continue;
                }

                // Copie des champs parents tableaux
                foreach ($champFrom['FILS']['VALEURS'] as $index => $champs) {
                    foreach ($champs as $idChamp => $data) {
                        $nomChamp = $this->searchElementToCopy($champFrom['FILS'], $idChamp, 'ID_CHAMP')['NOM'];

                        $enfantTableuToCopy = $this->searchElementToCopy($champForCopy['FILS'], $nomChamp, 'NOM');

                        if (null === $enfantTableuToCopy) {
                            error_log(
                                \sprintf(
                                    "Copie des valeurs effectifs et degagements entre dossier et etablissement : Le champ enfant %s n'existe pas. (champ: %s, rubrique: %s",
                                    $nomChamp,
                                    $champFrom['NOM'],
                                    $rubriqueFrom['NOM']
                                )
                            );

                            continue;
                        }

                        $this->saveValeurChamp(
                            implode('-', ['champ', $enfantTableuToCopy['ID_CHAMP'], $index]),
                            $idObject,
                            $object,
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
        $normalizedSearch = is_string($search) ? $this->normalizeString($search) : $search;

        $normalizedColumnValues = array_map(function ($value) {
            return is_string($value) ? $this->normalizeString($value) : $value;
        }, array_column($elementToSearchIn, $column));

        $index = array_search($normalizedSearch, $normalizedColumnValues, true);

        if (false === $index) {
            return null;
        }

        return $elementToSearchIn[$index];
    }

    private function normalizeString(string $str): string
    {
        return (string) preg_replace('/\s+/', ' ', trim($str));
    }
}
