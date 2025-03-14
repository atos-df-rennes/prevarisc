<?php

class Service_DossierManager
{
    private $DossierAvis;

    public function getDossierAvis(): Service_Interface_DossierAvis
    {
        if (!getenv('PREVARISC_NOMENCLATURE_AVIS_COMMISSION')) { 
            $this->DossierAvis = new Model_DbTable_Avis();
        } else {
            $this->DossierAvis = new Service_Platau();
        }

        return $this->DossierAvis;
    }
}