<?php

class Model_DbTable_Rubrique extends Zend_Db_Table_Abstract
{
    protected $_name = 'rubrique'; // Nom de la base
    protected $_primary = 'ID_RUBRIQUE'; // Clé primaire

    public function getRubriquesByCapsuleRubrique(string $capsuleRubrique, ?string $classObject, bool $adminView = false): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['r' => 'rubrique'], ['ID_RUBRIQUE', 'NOM'])
            ->join(['cr' => 'capsulerubrique'], 'r.ID_CAPSULERUBRIQUE = cr.ID_CAPSULERUBRIQUE', [])
            ->where('cr.NOM_INTERNE = ?', $capsuleRubrique)
            ->order('r.Id_RUBRIQUE')
        ;

        if (true === $adminView) {
            $select->columns(
                ['DEFAULT_DISPLAY' => 'r.DEFAULT_DISPLAY']
            );
        } else {
            $alias = null;
            $tableName = null;

            if(strpos(strtolower($classObject), 'dossier') !== false){
                $alias = 'drd';
                $tableName = 'displayrubriqueDossier';
            }

            if(strpos(strtolower($classObject), 'etablissement') !== false){
                $alias = 'dre';
                $tableName = 'displayrubriqueetablissement';
            }

            $select
                ->joinLeft([$alias => $tableName], sprintf('r.ID_RUBRIQUE = %s.ID_RUBRIQUE', $alias), [])
                ->columns(
                [
                    'DISPLAY' => new Zend_Db_Expr(
                        sprintf(
                            'CASE WHEN
                                %1$s.USER_DISPLAY IS NULL THEN r.DEFAULT_DISPLAY
                            ELSE
                                %1$s.USER_DISPLAY
                            END',
                            $alias
                        )
                    ),
                ]
            );
        }

        return $this->fetchAll($select)->toArray();
    }
}
