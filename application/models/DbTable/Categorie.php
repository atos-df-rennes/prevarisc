<?php

class Model_DbTable_Categorie extends Zend_Db_Table_Abstract
{
    protected $_name = 'categorie'; // Nom de la base
    protected $_primary = 'ID_CATEGORIE'; // Clé primaire

    // Donne la liste des catégories
    /**
     * @param string|int|float $id
     *
     * @return array
     */
    public function getCategories($id = null)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('categorie');

        if ($id != null) {
            $select->where("ID_CATEGORIE = $id");

            return $this->fetchRow($select)->toArray();
        } else {
            return $this->fetchAll($select)->toArray();
        }
    }

    public function fetchAllPK(): array
    {
        $all = $this->fetchAll()->toArray();
        $result = [];
        foreach ($all as $row) {
            $result[$row['ID_CATEGORIE']] = $row;
        }

        return $result;
    }
}
