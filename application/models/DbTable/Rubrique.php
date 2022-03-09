<?php

class Model_DbTable_Rubrique extends Zend_Db_Table_Abstract
{
    protected $_name = 'rubrique'; // Nom de la base
    protected $_primary = 'ID_RUBRIQUE'; // Clé primaire

    public function getRubriquesByCapsuleRubrique(string $capsuleRubrique, $classObject, bool $adminView = false): array
    {
        $alias = NULL;
        $tableName = NULL;

        if(strpos(strtolower($classObject),'dossier') !== false){
            $alias = 'drd';
            $tableName = 'displayrubriqueDossier';
        }
        if(strpos(strtolower($classObject),'etablissement') !== false){
            $alias = 'dre';
            $tableName = 'displayrubriqueetablissement';
        }

        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['r' => 'rubrique'], ['ID_RUBRIQUE', 'NOM'])
            ->join(['cr' => 'capsulerubrique'], 'r.ID_CAPSULERUBRIQUE = cr.ID_CAPSULERUBRIQUE', [])
            ->joinLeft([$alias => $tableName], 'r.ID_RUBRIQUE = '.$alias.'.ID_RUBRIQUE', [])
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
                    'DISPLAY' => new Zend_Db_Expr(sprintf(
                        'CASE WHEN
                        %s.USER_DISPLAY IS NULL THEN r.DEFAULT_DISPLAY
                    ELSE
                        %s.USER_DISPLAY
                    END'
                    ,
                    $alias,$alias
                    )
                    ),
                ]
            );
        }

        return $this->fetchAll($select)->toArray();
    }
}
