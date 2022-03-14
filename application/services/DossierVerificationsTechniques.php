<?php

class Service_DossierVerificationsTechniques extends Service_Descriptif
{
    function __construct() {
        parent::__construct(
            'descriptifVerificationsTechniques',
            new Model_DbTable_DisplayRubriqueDossier()
        );
    }
}
