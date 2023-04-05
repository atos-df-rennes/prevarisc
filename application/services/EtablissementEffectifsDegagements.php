<?php

class Service_EtablissementEffectifsDegagements extends Service_Descriptif
{
    public function __construct()
    {
        parent::__construct(
            'effectifsDegagementsEtablissement',
            new Model_DbTable_DisplayRubriqueEtablissement(),
            new Service_RubriqueEtablissement()
        );
    }
}
