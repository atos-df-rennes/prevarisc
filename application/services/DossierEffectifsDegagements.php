<?php

class Service_DossierEffectifsDegagements extends Service_Descriptif
{
    public function __construct()
    {
        parent::__construct(
            'effectifsDegagementsDossier',
            new Model_DbTable_DisplayRubriqueDossier(),
            new Service_RubriqueDossier()
        );
    }
}
