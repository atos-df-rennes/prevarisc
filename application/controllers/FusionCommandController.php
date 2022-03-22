<?php

class FusionCommandController extends Zend_Controller_Action
{

    public function init(){
        $this->_helper->layout->setLayout('menu_admin');
        
    }
    public function indexAction()
    {
        
        if ($this->_request->isPost()) {
            $url =  "/home/prv/current/prevarisc/application/command/";
            
            $adapter = new Zend_File_Transfer_Adapter_Http();
            $nameFile = $adapter->getFileInfo()['fileInput']['name'];
            $adapter->setDestination($url);
            
            if (!$adapter->receive()) {
                $messages = $adapter->getMessages();
                echo implode("\n", $messages);
            }else{
                $this->view->downloadComplete = true;
                $this->view->contentFile = file_get_contents($url.$nameFile);
        
                $service = new Service_FusionCommand();
                $service->mergeArrayCommune(json_decode(file_get_contents($url.$nameFile)));
            }
        }
    }
}

?>