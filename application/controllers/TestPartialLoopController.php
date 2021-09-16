<?php

class TestPartialLoopController extends Zend_Controller_Action
{


    public function indexAction(){
        /*
        $this->view->table =  
        array(
            array('key' => 'Mammal', 'value' => 'Camel'),
            array('key' => 'Bird', 'value' => 'Penguin'),
            array('key' => 'Reptile', 'value' => 'Asp'),
            array('key' => 'Fish', 'value' => 'Flounder'),
        );
        */

        $service_etablissement = new Service_Etablissement();



        //$this->view->avis = $service_etablissement->getAvisEtablissement($this->view->etablissement['general']['ID_ETABLISSEMENT'], $this->view->etablissement['general']['ID_DOSSIER_DONNANT_AVIS']);

        //$dossiers = $service_etablissement->getDossiers($this->_request->id);
   
        /*
        $dossiers = $service_etablissement->getAfterNDossiers(2);
        $this->view->dossiers = $dossiers;
        */

        $service_etablissement = new Service_Etablissement();
         $this->view->avis = $service_etablissement->getAvisEtablissement($this->view->etablissement['general']['ID_ETABLISSEMENT'], $this->view->etablissement['general']['ID_DOSSIER_DONNANT_AVIS']);


        $dossiers = $service_etablissement->getNbDossierTypeEtablissement(11046,"visites")[0]["nbdossier"];

        $this->view->dossiers =   $dossiers;




    }


}
