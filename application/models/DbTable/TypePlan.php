<?php

/*
    Type de plan

    Cette classe sert pour r�cup�rer les cat�gories, et les administrer

*/

class Model_DbTable_TypePlan extends Zend_Db_Table_Abstract
{
    protected $_name = 'typeplan'; // Nom de la base
    protected $_primary = 'ID_TYPEPLAN'; // Clé primaire

    public function fetchAllPK(): array
    {
        $all = $this->fetchAll()->toArray();
        $result = [];
        foreach ($all as $row) {
            $result[$row['ID_TYPEPLAN']] = $row;
        }

        return $result;
    }
}
