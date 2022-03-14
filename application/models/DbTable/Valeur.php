<?php

class Model_DbTable_Valeur extends Zend_Db_Table_Abstract
{
    protected $_name = 'valeur'; // Nom de la base
    protected $_primary = 'ID_VALEUR'; // Clé primaire

    public function getByChampAndObject(int $idChamp, int $idObject, string $classObject): ?Zend_Db_Table_Row
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['v' => 'valeur'])
            ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', [])
            ->where('c.ID_CHAMP = ?', $idChamp)
        ;

        if (false !== strpos($classObject, 'Dossier')) {
            $select->join(['dv' => 'dossierValeur'], 'dv.ID_VALEUR = v.ID_VALEUR', [])
                ->where('dv.ID_DOSSIER = ?', $idObject)
            ;
        }
        if (false !== strpos($classObject, 'Etablissement')) {
            $select->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR', [])
                ->where('ev.ID_ETABLISSEMENT = ?', $idObject)
            ;
        }

        return $this->fetchRow($select);
    }
}
