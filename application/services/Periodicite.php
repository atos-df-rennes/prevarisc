<?php

class Service_Periodicite
{
    /**
     * Récupération de l'ensemble des periodicites.
     *
     * @return array
     */
    public function getAll()
    {
        $DB_periodicite = new Model_DbTable_Categorie();

        return $DB_periodicite->fetchAllPK();
    }
}
