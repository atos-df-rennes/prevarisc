<?php

class Service_EtablissementDescriptif extends Service_Descriptif
{
    function __construct() {
        parent::__construct(
            'descriptifEtablissement',
            new Model_DbTable_DisplayRubriqueEtablissement()
        );
    }
}
