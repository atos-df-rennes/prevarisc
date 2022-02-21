<?php

class Model_DbTable_Resource extends Zend_Db_Table_Abstract
{
    protected $_name = 'resources';

    protected $_referenceMap = [
        'Privilege' => [
            'columns' => ['id_privilege'],
            'refTableClass' => 'Model_DbTable_Privilege',
            'refColumns' => ['id_privilege'],
        ],
    ];

    protected $_dependentTables = ['Model_DbTable_Privilege'];
}
