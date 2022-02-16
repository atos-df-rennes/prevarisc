<?php

class Model_DbTable_Privilege extends Zend_Db_Table_Abstract
{
    protected $_name = 'privileges';

    protected $_referenceMap = [
        'Privilege' => [
            'columns' => ['id_resource'],
            'refTableClass' => 'Model_DbTable_Resource',
            'refColumns' => ['id_resource'],
        ],
    ];

    protected $_dependentTables = ['Model_DbTable_GroupePrivilege', 'Model_DbTable_Resource'];
}
