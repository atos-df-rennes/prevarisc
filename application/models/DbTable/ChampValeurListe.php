<?php

class Model_DbTable_ChampValeurListe extends Zend_Db_Table_Abstract
{
    protected $_name = 'champvaleurliste'; // Nom de la base
    protected $_primary = 'ID_VALEURLISTE'; // Clé primaire

    public function findAll(): array
    {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from('champvaleurliste');

        return $this->fetchAll($select)->toArray();
    }

    public function getValeurListeByChamp(int $idChamp): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('cvl' => 'champvaleurliste'), array('ID_VALEURLISTE', 'VALEUR'))
            ->join(array('c' => 'champ'), 'cvl.ID_CHAMP = c.ID_CHAMP', array())
            ->where('c.ID_CHAMP = ?', $idChamp);

        return $this->fetchAll($select)->toArray();
    }
}