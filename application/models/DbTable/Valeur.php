<?php

class Model_DbTable_Valeur extends Zend_Db_Table_Abstract
{
    protected $_name = 'valeur'; // Nom de la base
    protected $_primary = 'ID_VALEUR'; // Clé primaire

    public function getByChampAndObject(int $idChamp, int $idObject, $classObject)
    {

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['v' => 'valeur'])
            ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', [])
            ->where('c.ID_CHAMP = ?', $idChamp);
        if(strpos(strtolower($classObject),'dossier') !== false){
            $select
            ->join(['d' => 'dossierValeur'], 'd.ID_VALEUR = v.ID_VALEUR', [])
            ->where('d.ID_DOSSIER = ?', $idObject);
        }
        $result = $this->fetchRow($select);
        return $result;
    }
}
