<?php

class Service_Valeur
{
    public function get(int $idChamp, int $idObject, string $classObject)
    {
        $modelValeur = new Model_DbTable_Valeur();
        $valeur = $modelValeur->getByChampAndObject($idChamp, $idObject, $classObject);

        if (null !== $valeur) {
            $typeValeur = $this->getTypeValeur($idChamp);
            $valeur = $valeur[$typeValeur];
        }

        return $valeur;
    }




    public function getAll(int $idChamp, int $idObject, string $classObject)
    {
        $modelValeur = new Model_DbTable_Valeur();
        $valeurs = $modelValeur->getAllByChampAndObject($idChamp, $idObject, $classObject);
        $retourValeurs = [];
        //TODO check valeur tableau
        if (!empty($valeurs)) {
            foreach ($valeurs as $valeur) {
                $strData = 'valeur-'.$valeur['idx'].'-'.$valeur['ID_PARENT'].'-'.$valeur['ID_CHAMP'].'-';
                isset($valeur['ID_VALEUR']) ? $strData .= $valeur['ID_VALEUR']  : $strData .= 'NULL';

                $retourValeurs[] =
                    [
                        'VALEUR' => $valeur[$this->getTypeValeur($idChamp)],
                        'ID_VALEUR' => $valeur['ID_VALEUR'],
                        'IDX_VALEUR' => $valeur['idx'],
                        'ID_PARENT' => $valeur['ID_PARENT'],
                        'ID_TYPECHAMP' => $valeur['ID_TYPECHAMP'],
                        'ID_CHAMP' => $valeur['ID_CHAMP'],
                        'STR_DATA' => $strData

                    ];
            }
        }
        return $retourValeurs;
    }


    public function getAllValueTable(int $idChamp, int $idObject, string $classObject){

    }

    public function insert(int $idChamp, int $idObject, string $classObject, $value, $idx=null): void
    {
        if ('' !== $value) {
            $modelValeur = new Model_DbTable_Valeur();

            $typeValeur = $this->getTypeValeur($idChamp);
            $idValeurInsert = $modelValeur->insert([
                $typeValeur => $value,
                'ID_CHAMP' => $idChamp,
                'idx' => $idx
            ]);

            if (false !== strpos($classObject, 'Dossier')) {
                $modelDossierValeur = new Model_DbTable_DossierValeur();

                $modelDossierValeur->insert(
                    [
                        'ID_DOSSIER' => $idObject,
                        'ID_VALEUR' => $idValeurInsert,
                    ]
                );
            }
            if (false !== strpos($classObject, 'Etablissement')) {
                $modelEtablissementValeur = new Model_DbTable_EtablissementValeur();

                $modelEtablissementValeur->insert(
                    [
                        'ID_ETABLISSEMENT' => $idObject,
                        'ID_VALEUR' => $idValeurInsert,
                    ]
                );
            }
        }
    }

    public function update(int $idChamp, $valueInDB, $newValue, int $idx = null): void
    {
        if ('' === $newValue) {
            $valueInDB->delete();
        } else {
            $typeValeur = $this->getTypeValeur($idChamp);
            $valueInDB->{$typeValeur} = $newValue;
            $valueInDB->idx = $idx;
            $valueInDB->save();
        }
    }

    private function getTypeValeur(int $idChamp): string
    {
        $modelChamp = new Model_DbTable_Champ();
        $typeValeur = '';

        $champ = $modelChamp->find($idChamp)->current();

        switch ($champ['ID_TYPECHAMP']) {
            case 1:
            case 3:
                $typeValeur = 'VALEUR_STR';

                break;

            case 2:
                $typeValeur = 'VALEUR_LONG_STR';

                break;

            case 4:
                $typeValeur = 'VALEUR_INT';

                break;

            case 5:
                $typeValeur = 'VALEUR_CHECKBOX';

                break;

            default:
                throw new Exception('Type de champ non supporté.');

                break;
        }

        return $typeValeur;
    }
}
