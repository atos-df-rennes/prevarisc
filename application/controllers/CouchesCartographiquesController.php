<?php

class CouchesCartographiquesController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('menu_admin');
        $this->view->headLink()->appendStylesheet('/js/geoportail/sdk-ol/GpSDK2D.css', 'all');
        $this->view->headScript()->appendFile('/js/geoportail/sdk-ol/GpSDK2D.js', 'text/javascript');
        $this->view->headScript()->appendFile('/js/geoportail/manageMap.js', 'text/javascript');

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getCapabilities', 'json')
                    ->initContext();

        $this->view->key_ign = getenv('PREVARISC_PLUGIN_IGNKEY');
        $this->serviceCarto = new Service_Carto();
    }

    public function listAction()
    {
        $this->view->couches_cartographiques = $this->serviceCarto->getAll();

        $this->view->geoconcept_url = getenv('PREVARISC_PLUGIN_GEOCONCEPT_URL');
        $this->view->key_googlemap = getenv('PREVARISC_PLUGIN_GOOGLEMAPKEY');
        $this->view->default_lon = getenv('PREVARISC_CARTO_DEFAULT_LON') ?: '2.71490430425517';
        $this->view->default_lat = getenv('PREVARISC_CARTO_DEFAULT_LAT') ?: '50.4727273438818';
    }

    // FIXME Corriger cette action pour qu'on ne puisse ajouter que des couches persos
    // i.e. Pas de requête vers l'IGN ici
    public function addAction()
    {
        // FIXME Test
        // $this->view->key_ign = explode(',', getenv('PREVARISC_PLUGIN_IGNKEY'))[0];

        if ($this->_request->isPost()) {
            try {
                $data = $this->getRequest()->getPost();
                $this->serviceCarto->save($data);

                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Ajout réussi !', 'message' => 'La couche cartographique a été ajoutée.'));
                $this->_helper->redirector('list');
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => '', 'message' => 'La couche cartographique n\'a pas été ajoutée. Veuillez rééssayez. ('.$e->getMessage().')'));
            }
        }
    }

    public function addCoucheIgnAction()
    {
        $this->view->key_ign = explode(',', getenv('PREVARISC_PLUGIN_IGNKEY'));

        $this->addAction();
    }

    public function editAction()
    {
        $this->view->row = $this->serviceCarto->findById($this->getRequest()->getParam('id'));

        if ($this->_request->isPost()) {
            try {
                $data = $this->getRequest()->getPost();
                $this->serviceCarto->save($data, $this->getRequest()->getParam('id'));

                $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Ajout réussi !', 'message' => 'La couche cartographique a été ajoutée.'));
                $this->_helper->redirector('list');
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error', 'title' => '', 'message' => 'La couche cartographique n\'a pas été ajoutée. Veuillez rééssayez. ('.$e->getMessage().')'));
            }
        }

        $this->render('add');
    }

    public function deleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        try {
            $this->serviceCarto->delete($this->getRequest()->getParam('id'));
            $this->_helper->flashMessenger(array('context' => 'success', 'title' => 'Ajout réussi !', 'message' => 'La couche cartographique a été supprimée.'));
        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'error', 'title' => '', 'message' => 'La couche cartographique n\'a pas été supprimée. Veuillez rééssayez. ('.$e->getMessage().')'));
        }

        $this->_helper->redirector('list');
    }
}
