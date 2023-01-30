<?php

class Model_DbTable_PieceJointe extends Zend_Db_Table_Abstract
{
    protected $_name = 'piecejointe'; // Nom de la base
    protected $_primary = 'ID_PIECEJOINTE'; // Clé primaire

    /**
     * @param array|float|int|string|Zend_Db_Expr $table
     * @param int|string                          $champ
     * @param int|string                          $identifiant
     *
     * @return null|array
     */
    public function affichagePieceJointe($table, $champ, $identifiant)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['pj' => 'piecejointe'])
            ->join($table, "pj.ID_PIECEJOINTE = {$table}.ID_PIECEJOINTE")
            ->joinLeft(['pjs' => 'piecejointestatut'], 'pj.ID_PIECEJOINTESTATUT = pjs.ID_PIECEJOINTESTATUT', ['NOM_STATUT'])
            ->where($champ.' = '.$identifiant)
            ->order('pj.ID_PIECEJOINTE DESC')
        ;

        return (null != $this->fetchAll($select)) ? $this->fetchAll($select)->toArray() : null;
    }

    public function maxPieceJointe()
    {
        $select = 'SELECT MAX(ID_PIECEJOINTE)
        FROM piecejointe
        ;';

        return $this->getAdapter()->fetchRow($select);
    }

    public function updatePlatauStatus(int $id, int $status): void
    {
        $this->update(["ID_PIECEJOINTESTATUT" => $status], "ID_PIECEJOINTE = $id");
    }
}
