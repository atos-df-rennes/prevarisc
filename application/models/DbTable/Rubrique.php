<?php

class Model_DbTable_Rubrique extends Zend_Db_Table_Abstract
{
    protected $_name = 'rubrique'; // Nom de la base
    protected $_primary = 'ID_RUBRIQUE'; // Clé primaire

    public function getRubriquesByCapsuleRubrique(string $capsuleRubrique): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['r' => 'rubrique'], ['ID_RUBRIQUE', 'NOM', 'idx'])
            ->columns(
                ['DISPLAY' => 'r.DEFAULT_DISPLAY']
            )
            ->join(['cr' => 'capsulerubrique'], 'r.ID_CAPSULERUBRIQUE = cr.ID_CAPSULERUBRIQUE', [])
            ->where('cr.NOM_INTERNE = ?', $capsuleRubrique)
            ->order('r.idx asc')
        ;

        return $this->fetchAll($select)->toArray();
    }

    public function getAllRubriqueForm($idCaps)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['r' => 'rubrique'])
            ->where('r.ID_CAPSULERUBRIQUE = ? ', $idCaps)
        ;

        return $this->fetchAll($select)->toArray();
    }

    //postParam => ['idx' = nouvelle idx champ, 'ID_CHAMP' => ID du champ]
    public function updateNewIdx($postParam): void
    {
        $rubrique = $this->find($postParam['ID'])->current();
        $rubrique->idx = $postParam['idx'];
        $rubrique->save();
    }

    public function getNbRubriqueOfDesc(string $nomCaps): int
    {
        $select = $this->select();

        $select
            ->setIntegrityCheck(false)
            ->from(['r' => 'rubrique'], ['r.ID_RUBRIQUE', 'r.ID_CAPSULERUBRIQUE', 'r.NOM'])
            ->join(['cr' => 'capsulerubrique'], 'r.ID_CAPSULERUBRIQUE = cr.ID_CAPSULERUBRIQUE')
            ->where('cr.NOM_INTERNE = ?', $nomCaps)
        ;

        return sizeof($this->fetchAll($select)->toArray());
    }
}
