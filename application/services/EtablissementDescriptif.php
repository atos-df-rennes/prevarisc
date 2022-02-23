<?php

// FIXME Faire un service Formulaire plutôt que Etablissement ?
// Tout ce qui est là est générique peu importe l'objet
class Service_EtablissementDescriptif
{
    public const CAPSULE_RUBRIQUE = 'descriptifEtablissement';

    public function getRubriques(int $idEtablissement): array
    {
        $modelRubrique = new Model_DbTable_Rubrique();
        $modelChamp = new Model_DbTable_Champ();

        $serviceValeur = new Service_Valeur();

        $rubriques = $modelRubrique->getRubriquesByCapsuleRubrique(self::CAPSULE_RUBRIQUE);
        foreach ($rubriques as &$rubrique) {
            $rubrique['CHAMPS'] = $modelChamp->getChampsByRubrique($rubrique['ID_RUBRIQUE']);

            foreach ($rubrique['CHAMPS'] as &$champ) {
                $champ['VALEUR'] = $serviceValeur->get($champ['ID_CHAMP'], $idEtablissement);
            }
        }

        return $rubriques;
    }

    public function getValeursListe(): array
    {
        $modelChampValeurListe = new Model_DbTable_ChampValeurListe();

        $champsValeurListe = $modelChampValeurListe->findAll();

        $sortedChampValeurListe = [];
        foreach ($champsValeurListe as $champValeurListe) {
            $sortedChampValeurListe[$champValeurListe['ID_CHAMP']][] = $champValeurListe;
        }

        return $sortedChampValeurListe;
    }

    public function saveRubriqueDisplay(string $key, int $idEtablissement, int $value): void
    {
        $serviceRubrique = new Service_Rubrique();

        $explodedRubrique = explode('-', $key);
        $idRubrique = end($explodedRubrique);

        $serviceRubrique->updateRubriqueDisplay($idRubrique, $idEtablissement, $value);
    }

    public function saveValeurChamp(string $key, int $idEtablissement, $value): void
    {
        $explodedChamp = explode('-', $key);
        $idChamp = end($explodedChamp);

        $this->saveValeur($idChamp, $idEtablissement, $value);
    }

    private function saveValeur(int $idChamp, int $idEtablissement, $value): void
    {
        $modelValeur = new Model_DbTable_Valeur();
        $serviceValeur = new Service_Valeur();

        $valueInDB = $modelValeur->getByChampAndEtablissement($idChamp, $idEtablissement);

        if (null === $valueInDB) {
            $serviceValeur->insert($idChamp, $idEtablissement, $value);
        } else {
            $serviceValeur->update($idChamp, $valueInDB, $value);
        }
    }
}
