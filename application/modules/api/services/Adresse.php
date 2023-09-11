<?php

class Api_Service_Adresse
{
    /**
     * Récupération des communes via le nom ou le code postal.
     *
     * @param string $q Code postal ou nom d'une commune
     *
     * @return array
     */
    public function get($q)
    {
        $service_adresse = new Service_Adresse();

        return $service_adresse->get($q);
    }

    /**
     * Retourne les types de voie d'une commune identifiée par son code insee.
     *
     * @param int $code_insee
     *
     * @return array
     */
    public function getTypesVoieParVille($code_insee)
    {
        $service_adresse = new Service_Adresse();

        return $service_adresse->getTypesVoieParVille($code_insee);
    }

    /**
     * Retourne les voies par rapport à une ville.
     */
    public function getVoies(int $code_insee, string $q = ''): array
    {
        $service_adresse = new Service_Adresse();

        return $service_adresse->getVoies($code_insee, $q);
    }

    public function getLibelleCommune($code_insee)
    {
        $service_adresse = new Service_Adresse();

        return $service_adresse->getLibelleCommune($code_insee);
    }

    public function getLibelleRue($idRue)
    {
        $service_adresse = new Service_Adresse();

        return $service_adresse->getLibelleRue($idRue);
    }

    /**
     * Retourne les numéros par rapport à une voie.
     */
    public function getNumeros(int $id_rue): array
    {
        $DB_adresse = new Service_Adresse();

        return $DB_adresse->getNumeros($id_rue);
    }
}
