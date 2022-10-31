<?php

class Model_DbTable_AdresseCommune extends Zend_Db_Table_Abstract
{
    protected $_name = 'adressecommune'; // Nom de la base
    protected $_primary = 'NUMINSEE_COMMUNE'; // Clé primaire

    /**
     * @param int|string $q
     *
     * @return array
     */
    public function get($q)
    {
        $select = $this->select()->setIntegrityCheck(false);

        $select->from('adressecommune')
            ->where('LIBELLE_COMMUNE LIKE ?', '%'.$q.'%')
            ->order('LENGTH(LIBELLE_COMMUNE)')
        ;

        return $this->fetchAll($select)->toArray();
    }

    /**
     * @param int|string $numinsee
     */
    public function getMairieInformation($numinsee)
    {
        $select = 'SELECT * '
                .'FROM adressecommune as commune '
                .'INNER JOIN utilisateurinformations as user '
                .'ON commune.ID_UTILISATEURINFORMATIONS = '
                .'user.ID_UTILISATEURINFORMATIONS '
                ."WHERE commune.NUMINSEE_COMMUNE = '".$numinsee."'";

        $result = $this->getAdapter()->fetchAll($select);
        if (!empty($result)) {
            return $result[0];
        }

        return null;
    }
}
