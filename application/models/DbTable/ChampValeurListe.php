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
        ;

        return $this->fetchAll($select)->toArray();
    }

    // Il faut ajouter une colonne `idx` dans la table `champvaleurliste`
    // Cette colonne permettra de stocker l'ordre des valeurs d'un champ.
    // Grâce à cela, getValeurListeByChamp() pourra récupérer les valeurs
    // triées selon l'ordre défini dans `idx`, ce qui est indispensable
    // pour que l'affichage reflète correctement le drag & drop côté utilisateur.

    public function getValeurListeByChamp(int $idChamp): array
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['cvl' => 'champvaleurliste'], ['ID_VALEURLISTE', 'VALEUR'])
            ->join(['c' => 'champ'], 'cvl.ID_CHAMP = c.ID_CHAMP', [])
            ->where('c.ID_CHAMP = ?', $idChamp)
        ;

        return $this->fetchAll($select)->toArray();
    }
}
