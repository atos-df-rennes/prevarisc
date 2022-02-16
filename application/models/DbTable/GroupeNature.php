<?php

class Model_DbTable_GroupeNature extends Zend_Db_Table_Abstract
{
    protected $_name = 'groupenature';
    protected $_primary = ['ID_DOSSIERNATURE', 'ID_GROUPE'];
}
