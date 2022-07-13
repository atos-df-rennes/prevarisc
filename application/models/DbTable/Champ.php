<?php

class Model_DbTable_Champ extends Zend_Db_Table_Abstract
{
    protected $_name = 'champ'; // Nom de la base
    protected $_primary = 'ID_CHAMP'; // Clé primaire

    public function getTypeChamp(int $idChamp)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM', 'ID_TYPECHAMP'])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->where('c.ID_CHAMP = ?', $idChamp)
        ;

        return $this->fetchRow($select);
    }

    public function getChampsByRubrique(int $idRubrique): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM', 'ID_TYPECHAMP', 'ID_RUBRIQUE'])
            ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', [])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->where('r.ID_RUBRIQUE = ?', $idRubrique)
            ->where('c.ID_PARENT IS NULL')
            ;

        return $this->fetchAll($select)->toArray();
    }

    public function getChampAndJoins(int $idChamp, bool $hasList = false): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], ['ID_CHAMP', 'NOM', 'ID_TYPECHAMP'])
            ->join(['ltcr' => 'listetypechamprubrique'], 'c.ID_TYPECHAMP = ltcr.ID_TYPECHAMP', ['TYPE'])
            ->join(['r' => 'rubrique'], 'c.ID_RUBRIQUE = r.ID_RUBRIQUE', ['ID_RUBRIQUE'])
            ->where('c.ID_CHAMP = ?', $idChamp)
        ;

        if (true === $hasList) {
            $select->joinLeft(['cvl' => 'champvaleurliste'], 'c.ID_CHAMP = cvl.ID_CHAMP', ['VALEUR']);

            return $this->fetchAll($select)->toArray();
        }

        return $this->fetchRow($select)->toArray();
    }

    public function getChampsFromParent(int $idParent): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['c' => 'champ'], [])
            ->join(['c2' => 'champ'], 'c2.ID_PARENT = c.ID_CHAMP', ['ID_CHAMP', 'NOM', 'ID_TYPECHAMP'])
            ->join(['ltcr' => 'listetypechamprubrique'], 'ltcr.ID_TYPECHAMP = c2.ID_TYPECHAMP', ['TYPE'])
            ->where('c.ID_CHAMP = ?', $idParent)
        ;

        return $this->fetchAll($select)->toArray();
    }

    public function getInfosParent(int $idChampEnfant): array
    {
        $select = $this->select()
            ->from(['c' => 'champ'], [])
            ->join(['c2' => 'champ'], 'c.ID_PARENT = c2.ID_CHAMP', ['ID_CHAMP', 'NOM'])
            ->where('c.ID_CHAMP = ?', $idChampEnfant)
        ;

        return $this->fetchRow($select)->toArray();
    }
}
