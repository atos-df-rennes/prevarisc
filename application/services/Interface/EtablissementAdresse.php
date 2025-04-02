<?php

interface Service_Interface_EtablissementAdresse
{
    public function get($id_etablissement);

    public function save($adresse, $etablissementID);

    public function delete($id_etablissement);
}
