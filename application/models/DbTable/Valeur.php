<?php

class Model_DbTable_Valeur extends Zend_Db_Table_Abstract
{
    protected $_name = 'valeur'; // Nom de la base
    protected $_primary = 'ID_VALEUR'; // Clé primaire

    public function getByChampAndObject(int $idChamp, int $idObject, string $classObject, int $idx = null)
    {
        $select = $this->getSelect($idChamp, $idObject, $classObject);
        $idx === null ? $select->where('v.idx IS NULL') : $select->where('v.idx = ?',$idx) ;

        return $this->fetchRow($select);
    }

    public function getAllByChampAndObject(int $idChamp, int $idObject, string $classObject)
    {
        $select = $this->getSelect($idChamp, $idObject, $classObject);

        return $this->fetchAll($select);
    }

    private function getSelect(int $idChamp, int $idObject, string $classObject){
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['v' => 'valeur'])
            ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', ['c.ID_PARENT', 'c.ID_TYPECHAMP', 'c.ID_CHAMP'])
            ->where('c.ID_CHAMP = ?', $idChamp)
            ->order('v.idx');

        if (false !== strpos($classObject, 'Dossier')) {
            $select->join(['dv' => 'dossiervaleur'], 'dv.ID_VALEUR = v.ID_VALEUR', [])
                ->where('dv.ID_DOSSIER = ?', $idObject)
            ;
        }

        if (false !== strpos($classObject, 'Etablissement')) {
            $select->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR', [])
                ->where('ev.ID_ETABLISSEMENT = ?', $idObject)
            ;
        }
        $select;
        return $select;
    }

    public function deleteValeur(int $idValeur):void
    {

    }
}
