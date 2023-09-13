<?php

class Service_TextesApplicables
{
    /**
     * @return array[][]
     */
    public function getAll(): array
    {
        $dbTextesAppl = new Model_DbTable_TextesAppl();

        $textes_applicables_non_organises = $dbTextesAppl->recupTextesApplVisible();

        return $this->organize($textes_applicables_non_organises);
    }

    /**
     * @return array[][]
     */
    public function organize(array $unorganizedTexts): array
    {
        $organizedApplicableTexts = [];
        $oldTitle = null;

        foreach ($unorganizedTexts as $applicableText) {
            $newTitle = $applicableText['ID_TYPETEXTEAPPL'];

            if ($oldTitle != $newTitle && !array_key_exists($applicableText['LIBELLE_TYPETEXTEAPPL'], $organizedApplicableTexts)) {
                $organizedApplicableTexts[$applicableText['LIBELLE_TYPETEXTEAPPL']] = [];
            }

            $organizedApplicableTexts[$applicableText['LIBELLE_TYPETEXTEAPPL']][$applicableText['ID_TEXTESAPPL']] = [
                'ID_TEXTESAPPL' => $applicableText['ID_TEXTESAPPL'],
                'LIBELLE_TEXTESAPPL' => $applicableText['LIBELLE_TEXTESAPPL'],
            ];

            $oldTitle = $newTitle;
        }

        return $organizedApplicableTexts;
    }
}
