<?php

class Service_Formulaire
{
    private $modelChamp;

    public function __construct()
    {
        $this->modelChamp = new Model_DbTable_Champ();
    }

    public function getAllCapsuleRubrique(): array
    {
        $modelCapsuleRubrique = new Model_DbTable_CapsuleRubrique();

        return $modelCapsuleRubrique->fetchAll()->toArray();
    }

    public function getAllListeTypeChampRubrique(): array
    {
        $modelListeTypeChampRubrique = new Model_DbTable_ListeTypeChampRubrique();

        return $modelListeTypeChampRubrique->fetchAll()->toArray();
    }

    public function insertRubrique(array $rubrique): int
    {
        $modelCapsuleRubrique = new Model_DbTable_CapsuleRubrique();
        $modelRubrique = new Model_DbTable_Rubrique();

        $idCapsuleRubriqueArray = $modelCapsuleRubrique->getCapsuleRubriqueIdByName($rubrique['capsule_rubrique']);
        $idCapsuleRubrique = $idCapsuleRubriqueArray['ID_CAPSULERUBRIQUE'];

        $idRubrique = $modelRubrique->insert([
            'NOM' => $rubrique['nom_rubrique'],
            'DEFAULT_DISPLAY' => intval($rubrique['afficher_rubrique']),
            'ID_CAPSULERUBRIQUE' => $idCapsuleRubrique,
            'idx' => $rubrique['idx'],
        ]);

        return intval($idRubrique);
    }

    public function insertChamp(array $champ, array $rubrique, bool $isParent = false): array
    {
        $modelChamp = new Model_DbTable_Champ();
        $modelChampValeurListe = new Model_DbTable_ChampValeurListe();
        $modelListeTypeChampRubrique = new Model_DbTable_ListeTypeChampRubrique();

        $typeChamp = 'type_champ';
        $nomChamp = 'nom_champ';

        if ($isParent) {
            $typeChamp = 'type_champ_enfant';
            $nomChamp = 'nom_champ_enfant';
        }

        $idTypeChamp = intval($champ[$typeChamp]);
        $idListe = $modelListeTypeChampRubrique->getIdTypeChampByName('Liste')['ID_TYPECHAMP'];

        $dataToInsert = [
            'NOM' => $champ[$nomChamp],
            'ID_TYPECHAMP' => $idTypeChamp,
            'ID_RUBRIQUE' => $rubrique['ID_RUBRIQUE'],
            'idx' => $champ['idx'],
        ];

        if (!empty($champ['ID_CHAMP_PARENT'])) {
            $dataToInsert['ID_PARENT'] = $champ['ID_CHAMP_PARENT'];
        }

        $idChamp = $modelChamp->insert($dataToInsert);

        if ($idTypeChamp === $idListe) {
            // On récupère les valeurs de la liste séparément des autres champs
            $listValueArray = array_filter($champ, function ($key) {
                return 0 === strpos($key, 'valeur-ajout-');
            }, ARRAY_FILTER_USE_KEY);

            foreach ($listValueArray as $listValue) {
                $modelChampValeurListe->insert([
                    'VALEUR' => $listValue,
                    'ID_CHAMP' => $idChamp,
                ]);
            }
        }

        return $modelChamp->find($idChamp)->current()->toArray();
    }

    public function addRowTable(int $idChamp, int $idEntity, string $nomEntity, $idx = null): void
    {
        //Recuperation structure ligne tableau
        $modelChamp = new Model_DbTable_Champ();

        $structureLigneTableau = $modelChamp->getAllFils($idChamp);

        foreach ($structureLigneTableau as $champ) {
            $serviceValeur = new Service_Valeur();
            $res = $serviceValeur->insert($champ['ID_CHAMP'], $idEntity, $nomEntity, null, $idx);
        }
    }

    public function deleteRowTable(int $idChampParent, string $entity, int $idEntity, $idx): void
    {
        //suppression des fk

        switch ($entity) {
            case 'Etablissement':
                $modelEtablissementValeur = new Model_DbTable_Valeur();
                $select =
                    $modelEtablissementValeur->select()
                        ->setIntegrityCheck(false)
                        ->from(['ev' => 'etablissementvaleur'], ['ev.ID_ETABLISSEMENT', 'ev.ID_VALEUR'])
                        ->join(['v' => 'valeur'], 'ev.ID_VALEUR = v.ID_VALEUR', [])
                        ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', [])
                        ->where('c.ID_PARENT = ?', $idChampParent)
                        ->where('ev.ID_ETABLISSEMENT = ? ', $idEntity)
                        ->where('v.idx = ?', $idx)
                        ;

                        //is_integer($idx) ? $select->where('v.idx = ?', $idx) : $select->where('v.idx IS NULL');

                foreach ($modelEtablissementValeur->fetchAll($select)->toArray() as $ev) {
                    $toDelete = $modelEtablissementValeur->find(['ID_ETABLISSEMENT' => $ev['ID_ETABLISSEMENT'], 'ID_VALEUR' => $ev['ID_VALEUR']])->current();
                    $toDelete->delete();
                }

                break;

            case 'Dossier':
                $modelDossierValeur = new Model_DbTable_Valeur();
                $select =
                    $modelDossierValeur->select()
                        ->setIntegrityCheck(false)
                        ->from(['dv' => 'dossiervaleur'], ['dv.ID_DOSSIER', 'dv.ID_VALEUR'])
                        ->join(['v' => 'valeur'], 'ev.ID_VALEUR = v.ID_VALEUR', [])
                        ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', [])
                        ->where('dv.ID_DOSSIER = ? ', $idEntity)
                        ->where('c.ID_PARENT = ?', $idChampParent)
                        ->where('v.idx = ?', $idx)
                        ;
                        //is_integer($idx) ? $select->where('v.idx = ?', $idx) : $select->where('v.idx IS NULL');

                foreach ($modelDossierValeur->fetchAll($select)->toArray() as $ev) {
                    $toDelete = $modelDossierValeur->find(['ID_DOSSIER' => $ev['ID_DOSSIER'], 'ID_VALEUR' => $ev['ID_VALEUR']])->current();
                    $toDelete->delete();
                }

                break;
        }

        //Suppression des valeurs
        $modelValeur = new Model_DbTable_Valeur();
        $select = $modelValeur->select()
            ->setIntegrityCheck(false)
            ->from(['v' => 'valeur'])
            ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', [''])
            ->where('c.ID_CHAMP = ?', $idChampParent)
            ->where('v.idx = ?', $idx)
                    ;
        //is_integer($idx) ? $select->where('v.idx = ?', $idx) : $select->where('v.idx IS NULL');

        foreach ($modelValeur->fetchAll($select)->toArray() as $ev) {
            $toDelete = $modelValeur->find(['ID_VALEUR' => $ev['ID_VALEUR']])->current();
            $toDelete->delete();
        }
    }

    /**
     * Retourne la liste des pattern des champs fils d un champ parent.
     */
    //TODO Ajouter la génération du timstamp pour les valeurs non affecté
    public function getInputs(array $champParent): array
    {
        $listChampPattern = [];
        foreach (array_column($champParent['FILS'], 'ID_CHAMP') as $IdChamp) {
            $champDb = $this->modelChamp->getTypeChamp($IdChamp);
            $patternParam = [
                'VALEUR' => null,
                'ID_VALEUR' => null,
                'IDX_VALEUR' => null,
                'ID_PARENT' => $champParent['ID_CHAMP'],
                'ID_TYPECHAMP' => $champDb['ID_TYPECHAMP'],
                'ID_CHAMP' => $champDb['ID_CHAMP'],
            ];
            $listChampPattern[$IdChamp] = $patternParam;
        }

        return $listChampPattern;
    }

    public function getArrayValuesWithPattern(array $listValeurs, array $listChampPattern): array
    {
        $arrayReturn = [];
        foreach ($listValeurs as $idChampFils => $valeurs) {
            foreach ($valeurs as $valeur) {
                if (empty($arrayReturn[$valeur['IDX_VALEUR']])) {
                    foreach ($listChampPattern as $idChampPattern => $pattern) {
                        $arrayReturn[$valeur['IDX_VALEUR']][$idChampPattern] = $pattern;
                    }
                }
                $arrayReturn[$valeur['IDX_VALEUR']][$idChampFils] = $valeur;
            }
        }

        //TODO surement optimisable en la faisant passer dans la boucle precedente
        foreach($arrayReturn as $idxLigne => &$inputs){
            foreach($inputs as &$input){
                if($input['ID_VALEUR'] === NULL){
                    $input['IDX_VALEUR'] = $idxLigne;
                    $input['STR_DATA'] = 'valeur-'.$idxLigne.'-'.$input['ID_PARENT'].'-'.$input['ID_CHAMP'].'-NULL';
                }
            }
        }
        ksort($arrayReturn);
        return $arrayReturn ;
    }
}
