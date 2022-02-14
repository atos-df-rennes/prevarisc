<?php

class Model_DbTable_CoucheCarto extends Zend_Db_Table_Abstract
{
    protected $_name = 'couchecarto'; // Nom de la base
    protected $_primary = 'ID_COUCHECARTO'; // Nom de la base

    public function getAll(): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('cc' => 'couchecarto'))
            ->order('ORDRE_COUCHECARTO');

        return $this->fetchAll($select)->toArray();
    }
}
