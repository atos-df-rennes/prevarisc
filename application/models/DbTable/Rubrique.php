<?php

class Model_DbTable_Rubrique extends Zend_Db_Table_Abstract
{
    protected $_name = 'rubrique'; // Nom de la base
    protected $_primary = 'ID_RUBRIQUE'; // Clé primaire

    public function getRubriquesByCapsuleRubrique(string $capsuleRubrique, bool $adminView = false): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['r' => 'rubrique'], ['ID_RUBRIQUE', 'NOM'])
            ->join(['cr' => 'capsulerubrique'], 'r.ID_CAPSULERUBRIQUE = cr.ID_CAPSULERUBRIQUE', [])
            ->joinLeft(['dre' => 'displayrubriqueetablissement'], 'r.ID_RUBRIQUE = dre.ID_RUBRIQUE', [])
            ->where('cr.NOM_INTERNE = ?', $capsuleRubrique)
            ->order('r.Id_RUBRIQUE')
        ;

        if (true === $adminView) {
            $select->columns(
                ['DEFAULT_DISPLAY' => 'r.DEFAULT_DISPLAY']
            );
        } else {
            $select->columns(
                [
                    'DISPLAY' => new Zend_Db_Expr(
                        'CASE WHEN
                            dre.USER_DISPLAY IS NULL THEN r.DEFAULT_DISPLAY
                        ELSE
                            dre.USER_DISPLAY
                        END'
                    ),
                ]
            );
        }

        return $this->fetchAll($select)->toArray();
    }
}
