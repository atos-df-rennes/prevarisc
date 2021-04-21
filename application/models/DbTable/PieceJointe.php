<?php

class Model_DbTable_PieceJointe extends Zend_Db_Table_Abstract
{
    protected $_name = 'piecejointe'; // Nom de la base
    protected $_primary = 'ID_PIECEJOINTE'; // Clé primaire

    /**
     * @param array|string|int|float|Zend_Db_Expr $table
     * @param string|int                          $champ
     * @param string|int                          $identifiant
     *
     * @return array|null
     */
    public function affichagePieceJointe($table, $champ, $identifiant)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('piecejointe')
            ->join($table, "piecejointe.ID_PIECEJOINTE = $table.ID_PIECEJOINTE")
            ->where($champ.' = '.$identifiant )
            ->where('piecejointe.SIGNE_PIECEJOINTE IS NULL')
            ->order('piecejointe.ID_PIECEJOINTE DESC');

        return ($this->fetchAll($select) != null) ? $this->fetchAll($select)->toArray() : null;
    }

    /**
     * @param array|string|int|float|Zend_Db_Expr $table
     * @param string|int                          $champ
     * @param string|int                          $identifiant
     *
     * @return array|null
     */
    public function affichagePieceJointeSigne($table, $champ, $identifiant)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('piecejointe')
            ->join($table, "piecejointe.ID_PIECEJOINTE = $table.ID_PIECEJOINTE")
            ->where($champ.' = '.$identifiant )
            ->where('piecejointe.SIGNE_PIECEJOINTE = 1')
            ->order('piecejointe.ID_PIECEJOINTE DESC');

        return ($this->fetchAll($select) != null) ? $this->fetchAll($select)->toArray() : null;
    }

    public function maxPieceJointe()
    {
        $select = 'SELECT MAX(ID_PIECEJOINTE)
        FROM piecejointe
        ;';

        return $this->getAdapter()->fetchRow($select);
    }
}
