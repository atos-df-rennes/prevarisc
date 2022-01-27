<?php

class Service_Valeur
{
    public function get(int $idChamp, int $idEtablissement)
    {
        $modelValeur = new Model_DbTable_Valeur();
        $modelChamp = new Model_DbTable_Champ();

        $valeur = $modelValeur->getByChampAndEtablissement($idChamp, $idEtablissement);

        if ($valeur !== null) {
            $champ = $modelChamp->find($idChamp)->current();
            switch ($champ['ID_TYPECHAMP']) {
                case 1:
                case 4:
                    $valeur = $valeur['VALEUR_STR'];
                    break;
                case 2:
                case 3:
                    $valeur = $valeur['VALEUR_LONG_STR'];
                    break;
                case 5:
                    $valeur = $valeur['VALEUR_INT'];
                    break;
                case 6:
                    $valeur = $valeur['VALEUR_CHECKBOX'];
                    break;
                default:
                    throw new Exception('Type de champ non supporté.');
                    break;
            }
        }

        return $valeur;
    }

    public function insert(int $idChamp, int $idEtablissement, $value): void
    {
        $modelChamp = new Model_DbTable_Champ();
        $modelValeur = new Model_DbTable_Valeur();

        $champ = $modelChamp->find($idChamp)->current();
        $typeValeur = '';

        switch ($champ['ID_TYPECHAMP']) {
            case 1:
            case 4:
                $typeValeur = 'VALEUR_STR';
                break;
            case 2:
            case 3:
                $typeValeur = 'VALEUR_LONG_STR';
                break;
            case 5:
                $typeValeur = 'VALEUR_INT';
                break;
            case 6:
                $typeValeur = 'VALEUR_CHECKBOX';
                break;
            default:
                throw new Exception('Type de champ non supporté.');
                break;
        }

        $modelValeur->insert(array(
            $typeValeur => $value,
            'ID_CHAMP' => $idChamp,
            'ID_ETABLISSEMENT' => $idEtablissement
        ));
    }

    public function update(int $idChamp, $valueInDB, $newValue): void
    {
        $modelChamp = new Model_DbTable_Champ();

        $champ = $modelChamp->find($idChamp)->current();
        $typeValeur = '';

        switch ($champ['ID_TYPECHAMP']) {
            case 1:
            case 4:
                $typeValeur = 'VALEUR_STR';
                break;
            case 2:
            case 3:
                $typeValeur = 'VALEUR_LONG_STR';
                break;
            case 5:
                $typeValeur = 'VALEUR_INT';
                break;
            case 6:
                $typeValeur = 'VALEUR_CHECKBOX';
                break;
            default:
                throw new Exception('Type de champ non supporté.');
                break;
        }

        $valueInDB->$typeValeur = $newValue;
        $valueInDB->save();
    }
}