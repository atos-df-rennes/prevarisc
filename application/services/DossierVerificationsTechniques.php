<?php

class Service_DossierVerificationsTechniques extends Service_Descriptif
{
    public function __construct()
    {
        parent::__construct(
            'descriptifVerificationsTechniques',
            new Model_DbTable_DisplayRubriqueDossier(),
            new Service_RubriqueDossier()
        );
    }
}
