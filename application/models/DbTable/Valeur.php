<?php

class Model_DbTable_Valeur extends Zend_Db_Table_Abstract
{
    // Nom de la base
    protected $_name = 'valeur';

    // Clé primaire
    protected $_primary = 'ID_VALEUR';

    /**
     * Retourne l'unique valeur d'un champ n'étant pas un enfant d'un champ `tableau`
     *  OU
     * Retourne la valeur à l'index spécifié d'un champ étant un enfant d'un champ `tableau`.
     */
    public function getByChampAndObject(int $idChamp, int $idObject, string $classObject, ?int $idx = null): ?Zend_Db_Table_Row_Abstract
    {
        $select = $this->getSelect($idChamp, $idObject, $classObject);
        null === $idx ? $select->where('v.idx IS NULL') : $select->where('v.idx = ?', $idx);

        return $this->fetchRow($select);
    }

    /**
     * Retourne toutes les valeurs d'un champ étant un enfant d'un champ `tableau`.
     */
    public function getAllByChampAndObject(int $idChamp, int $idObject, string $classObject): Zend_Db_Table_Rowset_Abstract
    {
        $select = $this->getSelect($idChamp, $idObject, $classObject);

        return $this->fetchAll($select);
    }

    /**
     * Supprime toutes les valeurs des champs enfants d'un champ parent pour un objet donné.
     *
     * @param int    $idChampParent ID du champ parent
     * @param int    $idObject      ID de l'objet (dossier ou établissement)
     * @param string $classObject   Type d'objet ('Dossier' ou 'Etablissement')
     *
     * @return int Nombre de lignes supprimées
     */
    public function deleteValeursChampParent(int $idChampParent, int $idObject, string $classObject): int
    {
        $db = $this->getAdapter();

        // Récupérer les IDs des valeurs à supprimer via sous-requête
        $subSelect = $db->select()
            ->from(['v' => 'valeur'], ['v.ID_VALEUR'])
            ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', [])
            ->where('c.ID_PARENT = ?', $idChampParent)
        ;

        if (false !== strpos($classObject, 'Dossier')) {
            $subSelect->join(['dv' => 'dossiervaleur'], 'dv.ID_VALEUR = v.ID_VALEUR', [])
                ->where('dv.ID_DOSSIER = ?', $idObject)
            ;
        }

        if (false !== strpos($classObject, 'Etablissement')) {
            $subSelect->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR', [])
                ->where('ev.ID_ETABLISSEMENT = ?', $idObject)
            ;
        }

        // Récupérer les IDs sous forme de tableau
        $idsToDelete = $db->fetchCol($subSelect);

        if ([] === $idsToDelete) {
            return 0;
        }

        // Suppression directe via DELETE SQL
        return $this->delete(['ID_VALEUR IN (?)' => $idsToDelete]);
    }

    private function getAllOfParent(int $idObject, string $classObject)
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from(['v' => 'valeur'])
            ->join(['c' => 'champ'], 'v.ID_CHAMP = c.ID_CHAMP', ['c.ID_PARENT', 'c.ID_TYPECHAMP', 'c.ID_CHAMP', 'c.NOM'])
            ->order('v.idx')
        ;

        if (false !== strpos($classObject, 'Dossier')) {
            $select->join(['dv' => 'dossiervaleur'], 'dv.ID_VALEUR = v.ID_VALEUR', ['v.ID_VALEUR'])
                ->where('dv.ID_DOSSIER = ?', $idObject)
            ;
        }

        if (false !== strpos($classObject, 'Etablissement')) {
            $select->join(['ev' => 'etablissementvaleur'], 'ev.ID_VALEUR = v.ID_VALEUR', ['v.ID_VALEUR'])
                ->where('ev.ID_ETABLISSEMENT = ?', $idObject)
            ;
        }

        return $select;
    }

    private function getSelect(int $idChamp, int $idObject, string $classObject)
    {
        return $this->getAllOfParent($idObject, $classObject)->where('c.ID_CHAMP = ?', $idChamp);
    }
}
