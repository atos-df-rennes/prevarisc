<?php

class Model_DbTable_ChampValeurListe extends Zend_Db_Table_Abstract
{
    // Nom de la base
    protected $_name = 'champvaleurliste';

    // Clé primaire
    protected $_primary = 'ID_VALEURLISTE';

    public function findAll(): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('champvaleurliste')
            ->order('idx ASC')
            ->order('ID_VALEURLISTE ASC')
        ;

        return $this->fetchAll($select)->toArray();
    }

    public function getValeurListeByChamp(int $idChamp): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['cvl' => 'champvaleurliste'], ['ID_VALEURLISTE', 'VALEUR'])
            ->join(['c' => 'champ'], 'cvl.ID_CHAMP = c.ID_CHAMP', [])
            ->where('c.ID_CHAMP = ?', $idChamp)
            ->order('cvl.idx ASC')
            ->order('cvl.ID_VALEURLISTE ASC')
        ;

        return $this->fetchAll($select)->toArray();
    }
}
