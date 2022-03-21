<?php

class Service_EtablissementDescriptif extends Service_Descriptif
{
    public function __construct()
    {
        parent::__construct(
            'descriptifEtablissement',
            new Model_DbTable_DisplayRubriqueEtablissement(),
            new Service_RubriqueEtablissement()
        );
    }
}
