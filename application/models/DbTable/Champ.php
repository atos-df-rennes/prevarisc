<?php

class Model_DbTable_Champ extends Zend_Db_Table_Abstract
{
    protected $_name = 'champ'; // Nom de la base
    protected $_primary = 'ID_CHAMP'; // Clé primaire

    public function getTypeChamp(int $idChamp)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM', 'ID_TYPECHAMP', 'tableau'])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->where('c.ID_CHAMP = ?', $idChamp)
        ;

        return $this->fetchRow($select);
    }

    public function getAllFils(int $idParent){
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM', 'ID_TYPECHAMP', 'tableau'])
        ->where('c.ID_PARENT = ?', $$idParent);
        return $this->fetchAll($select)->toArray();
    }

    public function getChampsByRubrique($idRubrique): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM', 'ID_TYPECHAMP', 'ID_RUBRIQUE','tableau'])
            ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', [])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->where('r.ID_RUBRIQUE = ?', $idRubrique)
            ->where('c.ID_PARENT IS NULL')
            ->order('c.idx asc')
            ;

        return $this->fetchAll($select)->toArray();
    }

    public function getChampAndJoins(int $idChamp, bool $hasList = false): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM', 'tableau'])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', ['ID_RUBRIQUE'])
            ->where('c.ID_CHAMP = ?', $idChamp)
            ->where('c.ID_PARENT IS NULL')
            ->order('c.idx asc')
        ;

        if (true === $hasList) {
            $select->joinLeft(['cvl' => 'champvaleurliste'], 'c.ID_CHAMP = cvl.ID_CHAMP', ['VALEUR']);
        }

        return $this->fetchAll($select)->toArray();
    }
/*
    public function getChampFromParent(int $idParent): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'tableau'])
            ->join(['c2' => 'champ'], 'c2.ID_PARENT = c.ID_CHAMP')
            ->join(['ltcr' => 'listetypechamprubrique'], 'ltcr.ID_TYPECHAMP = c2.ID_TYPECHAMP')
            ->where('c.ID_CHAMP = ?', $idParent)
        ;

        return $this->fetchAll($select)->toArray();
    }

    public function getValueChampList(int $idChamp): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'ID_TYPECHAMP'])
            ->join(['cvl' => 'champvaleurliste'], 'c.ID_CHAMP = cvl.ID_CHAMP')
            ->where('c.ID_CHAMP = ?', $idChamp)
        ;

        return $this->fetchAll($select)->toArray();
    }

    public function getValueSelectChampList(int $idChamp): string
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'ID_TYPECHAMP'])
            ->join(['v' => 'valeur'], 'c.ID_CHAMP = v.ID_CHAMP', ['VALEUR' => 'VALEUR_STR'])
            ->where('c.ID_CHAMP = ?', $idChamp)
        ;

        return isset($this->fetchAll($select)->toArray()[0]['VALEUR']) ? $this->fetchAll($select)->toArray()[0]['VALEUR'] : '';
    }

    public function getChampFilsValue(int $idParent, int $idEntity, string $aClass): array
    {
        $LIST_TYPE_VALEUR = ['VALEUR_STR', 'VALEUR_LONG_STR', 'VALEUR_INT', 'VALEUR_CHECKBOX'];
        $select =
            $this->select()
                ->setIntegrityCheck(false)
                ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'NOM', 'ID_TYPECHAMP', 'tableau'])
                ->joinLeft(['v' => 'valeur'], 'v.ID_CHAMP = c.ID_CHAMP', $LIST_TYPE_VALEUR)
                ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', [])
                ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
                ->where('c.ID_PARENT = ?', $idParent)
            ;

        if (false !== strpos('Dossier', $aClass)) {
            $select
                ->join(['dv' => 'dossiervaleur'], 'dv.ID_VALEUR = v.ID_VALEUR')
                ->join(['d' => 'dossier'], 'dv.ID_DOSSIER = d.ID_DOSSIER')
                ->where('d.ID_DOSSIER = ?', $idEntity)
                ;
        }
        if (false !== strpos('Etablissement', $aClass)) {
            $select
                ->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR')
                ->join(['e' => 'etablissement'], 'ev.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT')
                ->where('e.ID_ETABLISSEMENT = ?', $idEntity)
                ;
        }

        $res = [];

        foreach ($this->fetchAll($select)->toArray() as $champ) {
            $tmpChamp = $champ;
            foreach ($LIST_TYPE_VALEUR as $T) {
                if (!empty($champ[$T])) {
                    $tmpChamp['VALEUR'] = $champ[$T];
                } elseif (!empty($champ['TYPE']) && 'Liste' === $champ['TYPE']) {
                    $tmpChamp['VALEUR'] = $this->getValueSelectChampList($tmpChamp['ID_CHAMP']);
                    $tmpChamp['VALEUR_LISTE'] = $this->getValueChampList($tmpChamp['ID_CHAMP']);
                }
            }
            array_push($res, $tmpChamp);
        }

        return $res;
    }
    

    public function getCorpFormulaire(int $idCapsuleRubrique)
    {
        $res = [];

        $modelRubrique = new Model_DbTable_Rubrique();

        foreach ($modelRubrique->getAllRubriqueForm($idCapsuleRubrique) as $rubrique) {
            $res[$rubrique['ID_RUBRIQUE']] = $rubrique;
        }

        $selectRubriqueForm = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'NOM', 'ID_TYPECHAMP', 'tableau'])
            ->join(['r' => 'rubrique'], 'r.ID_RUBRIQUE = c.ID_RUBRIQUE', ['r.ID_RUBRIQUE'])
            ->join(['ltcr' => 'listetypechamprubrique'], 'ltcr.ID_TYPECHAMP = c.ID_TYPECHAMP')
            ->where('c.ID_PARENT IS NULL')
            ->where('r.ID_CAPSULERUBRIQUE = ?', $idCapsuleRubrique)
        ;

        foreach ($this->fetchAll($selectRubriqueForm)->toArray() as $champ) {
            if ('Parent' === $champ['TYPE']) {
                $champ['CHAMP_FILS'] = $this->getChampFromParent($champ['ID_CHAMP']);
                foreach ($champ['CHAMP_FILS'] as &$champFils) {
                    if ('Liste' === $champFils['TYPE']) {
                        $champFils['VALEUR'] = $this->getValueChampList($champFils['ID_CHAMP']);
                    }
                }
            }
            if ('Liste' === $champ['TYPE']) {
                $champ['VALEUR'] = $this->getValueChampList($champ['ID_CHAMP']);
            }
            $res[$champ['ID_RUBRIQUE']]['CHAMPS'][$champ['ID_CHAMP']] = $champ;
        }

        return $res;
    }
    */

    public function getValeurFormulaire(int $idEntity, int $idCapsuleRubrique)
    {
        $select = $this->select();

        switch ($idCapsuleRubrique) {
            case 1:
                $select = $select->setIntegrityCheck(false)
                    ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM_CHAMP' => 'NOM','tableau'])
                    ->join(['v' => 'valeur'], 'v.ID_CHAMP = c.ID_CHAMP', ['VALEUR_STR', 'VALEUR_LONG_STR', 'VALEUR_INT', 'VALEUR_CHECKBOX'])
                    ->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR')
                    ->join(['r' => 'rubrique'], 'r.ID_RUBRIQUE = c.ID_RUBRIQUE')
                    ->where('ev.ID_ETABLISSEMENT = ?', $idEntity)
                    ->where('r.ID_CAPSULERUBRIQUE = ?', $idCapsuleRubrique)
                    ->order('c.idx asc')
                ;

                break;

            case 2:
                $select = $select->setIntegrityCheck(false)
                    ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM_CHAMP' => 'NOM','tableau'])
                    ->join(['v' => 'valeur'], 'v.ID_CHAMP = c.ID_CHAMP', ['VALEUR_STR', 'VALEUR_LONG_STR', 'VALEUR_INT', 'VALEUR_CHECKBOX'])
                    ->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR')
                    ->join(['r' => 'rubrique'], 'r.ID_RUBRIQUE = c.ID_RUBRIQUE')
                    ->where('ev.ID_ETABLISSEMENT = ?', $idEntity)
                    ->where('r.ID_CAPSULERUBRIQUE = ?', $idCapsuleRubrique)
                    ->order('c.idx asc')
                ;

                break;
        }

        $res = [];
        foreach ($this->fetchAll($select)->toArray() as $valeur) {                     
            $res[$valeur['ID_CHAMP']] = $valeur;
        }  


        $s2 = $this->select()->setIntegrityCheck(false);
        $s2->from(['c' => 'champ'], ['ID_CHAMP', 'NOM_CHAMP' => 'NOM','tableau','ID_PARENT','ID_TYPECHAMP'])
            ->join(['v' => 'valeur'], 'v.ID_CHAMP = c.ID_CHAMP', ['v.ID_VALEUR','IDX_VALEUR' => 'v.idx','VALEUR_STR', 'VALEUR_LONG_STR', 'VALEUR_INT', 'VALEUR_CHECKBOX'])
            ->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR')
            ->order('IDX_VALEUR')
            ->where('ev.ID_ETABLISSEMENT = ?', $idEntity)
            ->where('c.ID_PARENT = ?', 124);

        $res['RES_TABLEAU'] = [];
        foreach($this->fetchAll($select)->toArray() as $value){
            if( empty($res['RES_TABLEAU'][$value['ID_CHAMP']])){
                $res['RES_TABLEAU'][$value['ID_CHAMP']] = [];
            }
            foreach(
                array_filter($this->fetchAll($s2)->toArray(),function($v) use($value){
                    return $v['ID_CHAMP'] === $value['ID_CHAMP'];
                }) as $val
            ){
                if(empty($res['RES_TABLEAU'][$val['ID_PARENT']][$val['ID_CHAMP']])){
                    $res['RES_TABLEAU']
                        [$val['IDX_VALEUR']]
                            [$val['ID_PARENT']]
                                [$val['ID_CHAMP']] = [];
                                    //[$val['ID_VALEUR']] = [];
                }
                $res['RES_TABLEAU']
                    [$val['IDX_VALEUR']]
                        [$val['ID_PARENT']]
                            [$val['ID_CHAMP']] = $val;
            }

            //Clear la liste des residu
            $tmpRes = [];
            foreach($res["RES_TABLEAU"] as $k=>$entity){
                if(sizeof($entity) > 0){
                    $tmpRes[$k] = $entity;
                }
            }
            $res["RES_TABLEAU"] = $tmpRes;   
        }
        return $res;
    }

    public function getChampAndJoinsWithParent(int $idChamp, bool $hasList = false): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM'])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', ['ID_RUBRIQUE'])
            ->where('c.ID_CHAMP = ?', $idChamp)
        ;

        if (true === $hasList) {
            $select->joinLeft(['cvl' => 'champvaleurliste'], 'c.ID_CHAMP = cvl.ID_CHAMP', ['VALEUR']);
        }
        return $this->fetchAll($select)->toArray();
    }

    public function getChampFromParent(int $idParent):array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT'])
            ->join(['c2' => 'champ'], 'c2.ID_PARENT = c.ID_CHAMP')
            ->join(['ltcr' => 'listetypechamprubrique'], 'ltcr.ID_TYPECHAMP = c2.ID_TYPECHAMP')
            ->where('c.ID_CHAMP = ?', $idParent)
            ->order('c2.idx asc')

        ;

        return $this->fetchAll($select)->toArray();
    }

    public function getValueChampList(int $idChamp): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'ID_TYPECHAMP'])
            ->join(['cvl' => 'champvaleurliste'], 'c.ID_CHAMP = cvl.ID_CHAMP')
            ->where('c.ID_CHAMP = ?', $idChamp)
        ;

        return $this->fetchAll($select)->toArray();
    }

    public function getValueSelectChampList(int $idChamp): string
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'ID_TYPECHAMP'])
            ->join(['v' => 'valeur'], 'c.ID_CHAMP = v.ID_CHAMP', ['VALEUR' => 'VALEUR_STR'])
            ->where('c.ID_CHAMP = ?', $idChamp)
        ;

        return isset($this->fetchAll($select)->toArray()[0]['VALEUR']) ? $this->fetchAll($select)->toArray()[0]['VALEUR'] : '';
    }

    public function getChampFilsValue(int $idParent, int $idEntity, string $aClass): array
    {
        $LIST_TYPE_VALEUR = ['VALEUR_STR', 'VALEUR_LONG_STR', 'VALEUR_INT', 'VALEUR_CHECKBOX'];
        $select =
            $this->select()
                ->setIntegrityCheck(false)
                ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'NOM', 'ID_TYPECHAMP', 'tableau'])
                ->joinLeft(['v' => 'valeur'], 'v.ID_CHAMP = c.ID_CHAMP', $LIST_TYPE_VALEUR)
                ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', [])
                ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
                ->where('c.ID_PARENT = ?', $idParent)
                ->order('c.idx asc')
            ;

        if (false !== strpos('Dossier', $aClass)) {
            $select
                ->join(['dv' => 'dossiervaleur'], 'dv.ID_VALEUR = v.ID_VALEUR')
                ->join(['d' => 'dossier'], 'dv.ID_DOSSIER = d.ID_DOSSIER')
                ->where('d.ID_DOSSIER = ?', $idEntity)
                ->order('c.idx asc')
                ;
        }
        if (false !== strpos('Etablissement', $aClass)) {
            $select
                ->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR')
                ->join(['e' => 'etablissement'], 'ev.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT')
                ->where('e.ID_ETABLISSEMENT = ?', $idEntity)
                ;
        }

        $res = [];

        foreach ($this->fetchAll($select)->toArray() as $champ) {
            $tmpChamp = $champ;
            foreach ($LIST_TYPE_VALEUR as $T) {
                if (!empty($champ[$T])) {
                    $tmpChamp['VALEUR'] = $champ[$T];
                } elseif (!empty($champ['TYPE']) && 'Liste' === $champ['TYPE']) {
                    $tmpChamp['VALEUR'] = $this->getValueSelectChampList($tmpChamp['ID_CHAMP']);
                    $tmpChamp['VALEUR_LISTE'] = $this->getValueChampList($tmpChamp['ID_CHAMP']);
                }
            }
            array_push($res, $tmpChamp);
        }

        return $res;
    }

    public function getCorpFormulaire(int $idCapsuleRubrique)
    {
        $res = [];

        $modelRubrique = new Model_DbTable_Rubrique();

        foreach ($modelRubrique->getAllRubriqueForm($idCapsuleRubrique) as $rubrique) {
            $res[$rubrique['ID_RUBRIQUE']] = $rubrique;
        }

        //Requete recuperant tous les champs des rubriques
        $selectRubriqueForm = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'NOM', 'ID_TYPECHAMP', 'tableau'])
            ->join(['r' => 'rubrique'], 'r.ID_RUBRIQUE = c.ID_RUBRIQUE', ['r.ID_RUBRIQUE','r.idx'])
            ->join(['ltcr' => 'listetypechamprubrique'], 'ltcr.ID_TYPECHAMP = c.ID_TYPECHAMP')
            ->where('c.ID_PARENT IS NULL')
            ->where('r.ID_CAPSULERUBRIQUE = ?', $idCapsuleRubrique)
            ->order('c.idx asc')
        ;

        foreach ($this->fetchAll($selectRubriqueForm)->toArray() as $champ) {          
            if ('Parent' === $champ['TYPE']) {
                $champ['CHAMP_FILS'] = $this->getChampFromParent($champ['ID_CHAMP']);
                foreach ($champ['CHAMP_FILS'] as &$champFils) {
                    if ('Liste' === $champFils['TYPE']) {
                        $champFils['VALEUR'] = $this->getValueChampList($champFils['ID_CHAMP']);
                    }
                }
            }
            if ('Liste' === $champ['TYPE']) {
                $champ['VALEUR'] = $this->getValueChampList($champ['ID_CHAMP']);
            }

            $res[$champ['ID_RUBRIQUE']]['CHAMPS'][$champ['ID_CHAMP']] = $champ;
        }

        function sortByIdx($a,$b){
            return $a['idx'] - $b['idx'];
        }

        usort($res,'sortByIdx');
        return $res;
    }
/*
    public function getValeurFormulaire(int $idEntity, int $idCapsuleRubrique)
    {
        $select = $this->select();

        switch ($idCapsuleRubrique) {
            case 1:
                $select = $select->setIntegrityCheck(false)
                    ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM_CHAMP' => 'NOM'])
                    ->join(['v' => 'valeur'], 'v.ID_CHAMP = c.ID_CHAMP', ['VALEUR_STR', 'VALEUR_LONG_STR', 'VALEUR_INT', 'VALEUR_CHECKBOX'])
                    ->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR')
                    ->join(['r' => 'rubrique'], 'r.ID_RUBRIQUE = c.ID_RUBRIQUE')
                    ->where('ev.ID_ETABLISSEMENT = ?', $idEntity)
                    ->where('r.ID_CAPSULERUBRIQUE = ?', $idCapsuleRubrique)
                    ->order('c.idx asc')
                ;

                break;

            case 2:
                $select = $select->setIntegrityCheck(false)
                    ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM_CHAMP' => 'NOM'])
                    ->join(['v' => 'valeur'], 'v.ID_CHAMP = c.ID_CHAMP', ['VALEUR_STR', 'VALEUR_LONG_STR', 'VALEUR_INT', 'VALEUR_CHECKBOX'])
                    ->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR')
                    ->join(['r' => 'rubrique'], 'r.ID_RUBRIQUE = c.ID_RUBRIQUE')
                    ->where('ev.ID_ETABLISSEMENT = ?', $idEntity)
                    ->where('r.ID_CAPSULERUBRIQUE = ?', $idCapsuleRubrique)
                    ->order('c.idx asc')
                ;

                break;
        }

        $res = [];
        foreach ($this->fetchAll($select)->toArray() as $valeur) {
            $res[$valeur['ID_CHAMP']] = $valeur;
        }

        return $res;
    }
    */

    //postParam => ['idx' = nouvelle idx champ, 'ID_CHAMP' => ID du champ]
    public function updateNewIdx($postParam): void
    {
        $champ = $this->find($postParam['ID'])->current();
        $champ->idx = $postParam['idx'];
        $champ->save();
    }

    public function getNbChampOfRubrique(int $idRubrique): int
    {
        $select = $this->select();

        $select->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT'])
            ->where('c.ID_PARENT IS NULL')
            ->where('c.ID_RUBRIQUE = ?', $idRubrique)
        ;

        return sizeof($this->fetchAll($select)->toArray());
    }

    public function getNbChampOfParent(int $idParent): int
    {
        $select = $this->select();

        $select->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT'])
            ->where('c.ID_PARENT = ?', $idParent)
        ;

        return sizeof($this->fetchAll($select)->toArray());
    }
}
