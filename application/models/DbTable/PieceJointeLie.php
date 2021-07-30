<?php

class Model_DbTable_PieceJointeLie extends Zend_Db_Table_Abstract
{
    protected $_name = 'piecejointelie'; // Nom de la base
    protected $_primary = array('ID_PIECEJOINTE', 'ID_FILS_PIECEJOINTE');

    /**
     * @return array
     */
    public function recupPieceJointeCellule($idCellule)
    {
        //retourne le/les pj qui sont père de la cellule
        $select = $this->select()
            ->from(array('piecejointelie' => 'piecejointelie'))
            ->where('ID_FILS_PIECEJOINTE = ?', $idCellule);

        return $this->getAdapter()->fetchAll($select);
    }

    /**
     * @return array
     */
    public function recupPieceJointeFille($id)
    {
        //retourne le/les pj fille/s
        $select = $this->select()
            ->from(array('pjl' => 'piecejointelie'), array('ID_FILS_PIECEJOINTE'))
            ->where("pjl.ID_PIECEJOINTE =  $id");

        return $this->getAdapter()->fetchAll($select);
    }
}
