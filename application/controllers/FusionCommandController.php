<?php

class FusionCommandController extends Zend_Controller_Action
{

    public function init(){
        $this->_helper->viewRenderer->setNoRender(true);
    }
    public function indexAction()
    {   
        $service = new Service_FusionCommand();
        $service->mergeArrayCommune(json_decode(file_get_contents("/home/prv/current/prevarisc/application/command/fichier.json")));
    }
}

?>