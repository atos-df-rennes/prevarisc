<?php

class Model_DbTable_CommissionType extends Zend_Db_Table_Abstract
{
    protected $_name = 'commissiontype'; // Nom de la base
    protected $_primary = 'ID_COMMISSIONTYPE'; // Clé primaire
    protected $_referenceMap = [
        'commission' => [
            'columns' => 'ID_COMMISSIONTYPE',
            'refTableClass' => 'Model_DbTable_Commission',
            'refColumns' => 'ID_COMMISSIONTYPE',
        ],
    ];
}
