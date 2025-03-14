<?php

interface Service_Interface_DossierAvis
{
    public function getAvis();
    
    public function getAvisLibelle($idAvis, $idDossier = null);

}