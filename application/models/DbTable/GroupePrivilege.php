<?php

class Model_DbTable_GroupePrivilege extends Zend_Db_Table_Abstract
{
    protected $_name = 'groupe-privileges';

    protected $_referenceMap = [
        'Groupe' => [
            'columns' => ['ID_GROUPE'],
            'refTableClass' => 'Model_DbTable_Groupe',
            'refColumns' => ['ID_GROUPE'],
        ],
        'Privilege' => [
            'columns' => ['id_privilege'],
            'refTableClass' => 'Model_DbTable_Privilege',
            'refColumns' => ['id_privilege'],
        ],
    ];
}
