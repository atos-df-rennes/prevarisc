<?php

class Service_TextesApplicables
{
    /**
     * @return array[][]
     *
     * @psalm-return array<mixed, array<int, array{ID_TEXTESAPPL:mixed, LIBELLE_TEXTESAPPL:mixed}>>
     */
    public function getAll(): array
    {
        $dbTextesAppl = new Model_DbTable_TextesAppl();

        $textes_applicables = [];
        $textes_applicables_non_organises = $dbTextesAppl->recupTextesApplVisible();

        $old_titre = null;

        foreach ($textes_applicables_non_organises as $texte_applicable) {
            $new_titre = $texte_applicable['ID_TYPETEXTEAPPL'];

            if ($old_titre != $new_titre && !array_key_exists($texte_applicable['LIBELLE_TYPETEXTEAPPL'], $textes_applicables)) {
                $textes_applicables[$texte_applicable['LIBELLE_TYPETEXTEAPPL']] = [];
            }

            $textes_applicables[$texte_applicable['LIBELLE_TYPETEXTEAPPL']][] = [
                'ID_TEXTESAPPL' => $texte_applicable['ID_TEXTESAPPL'],
                'LIBELLE_TEXTESAPPL' => $texte_applicable['LIBELLE_TEXTESAPPL'],
            ];

            $old_titre = $new_titre;
        }

        return $textes_applicables;
    }
}
