<?php

class Model_DbTable_Champ extends Zend_Db_Table_Abstract
{
    protected $_name = 'champ'; // Nom de la base
    protected $_primary = 'ID_CHAMP'; // Clé primaire

    public function getTypeChamp(int $idChamp): array{
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM', 'ID_TYPECHAMP'])
        ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
        ->where('c.ID_CHAMP = ?', $idChamp)
        ;

    return $this->fetchAll($select)->toArray()[0];
    }

    public function getChampsByRubrique(int $idRubrique): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM', 'ID_TYPECHAMP','ID_RUBRIQUE'])
            ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', [])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->where('r.ID_RUBRIQUE = ?', $idRubrique)
            ->where('c.ID_PARENT IS NULL')
            ;

        return $this->fetchAll($select)->toArray();
    }

    public function getChampsByRubriqueWithParent(int $idRubrique): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP','ID_PARENT', 'NOM', 'ID_TYPECHAMP'])
            ->joinLeft(['v' => 'valeur'], 'c.ID_CHAMP = v.ID_CHAMP')
            ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', [])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->where('r.ID_RUBRIQUE = ?', $idRubrique)
            ->where('c.ID_PARENT IS NOT NULL')
            ;
        
        return $this->fetchAll($select)->toArray();
    }

    public function getChampAndJoins(int $idChamp, bool $hasList = false): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM'])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', ['ID_RUBRIQUE'])
            ->where('c.ID_CHAMP = ?', $idChamp)
            ->where('c.ID_PARENT IS NULL')
        ;

        if (true === $hasList) {
            $select->joinLeft(['cvl' => 'champvaleurliste'], 'c.ID_CHAMP = cvl.ID_CHAMP', ['VALEUR']);
        }

        return $this->fetchAll($select)->toArray();
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
        ->where('c.ID_CHAMP = ?', $idParent);

        return $this->fetchAll($select)->toArray();
    }


    public function getValueChampList(int $idChamp):array{
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'ID_TYPECHAMP'])
            ->join(['cvl' => 'champvaleurliste'], 'c.ID_CHAMP = cvl.ID_CHAMP')
            ->where('c.ID_CHAMP = ?', $idChamp);
        return $this->fetchAll($select)->toArray();
    }

    public function getValueSelectChampList(int $idChamp):String{
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'ID_TYPECHAMP'])
        ->join(['v' => 'valeur'], 'c.ID_CHAMP = v.ID_CHAMP',["VALEUR"=>"VALEUR_STR"])
        ->where('c.ID_CHAMP = ?', $idChamp);
    return isset($this->fetchAll($select)->toArray()[0]['VALEUR']) ? $this->fetchAll($select)->toArray()[0]['VALEUR'] :'';
    }

    public function getSelectListOfChamp(int $idChamp):array{
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'ID_PARENT', 'ID_TYPECHAMP'])
            ->join(['cvl' => 'champvaleurliste'], 'c.ID_CHAMP = cvl.ID_CHAMP')
            ->where('c.ID_CHAMP = ?', $idChamp);
        return $this->fetchAll($select)->toArray();
    }

    public function getChampFilsValue(int $idParent):array{
        $LIST_TYPE_VALEUR = ["VALEUR_STR","VALEUR_LONG_STR","VALEUR_INT","VALEUR_CHECKBOX"];
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP','ID_PARENT', 'NOM', 'ID_TYPECHAMP'])
            ->joinLeft(['v' => 'valeur'], 'v.ID_CHAMP = c.ID_CHAMP', $LIST_TYPE_VALEUR)
            ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', [])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->where('c.ID_PARENT = ?', $idParent)
            ;
        
        $res = [];
        foreach ($this->fetchAll($select)->toArray() as $champ) {
            $tmpChamp = $champ;
            foreach ($LIST_TYPE_VALEUR as $T) {
                if(!empty($champ[$T])){
                    $tmpChamp['VALEUR'] = $champ[$T];
                }
                elseif((!empty($champ['TYPE']) && $champ['TYPE'] === 'Liste') || (!empty($champ['ID_TYPECHAMP']) && $champ['ID_TYPECHAMP'] === 3) ){
                    $tmpChamp['VALEUR'] = $this->getValueSelectChampList($tmpChamp['ID_CHAMP']);
                    $tmpChamp['VALEUR_LISTE'] = $this->getValueChampList($tmpChamp['ID_CHAMP']);
                }
            }
            array_push($res, $tmpChamp);
        }
       
        return $res;
    }
}
