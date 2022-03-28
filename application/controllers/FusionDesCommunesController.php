<?php

class FusionDesCommunesController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('menu_admin');
    }

    public function indexAction()
    {
        $form = new Form_FusionCommunes();
        $this->view->form = $form;

        /** @var $request Zend_Controller_Request_Http */
        $request = $this->getRequest();

        if ($request->isPost()) {
            if (!$form->isValid($request->getPost())) {
                return $this->render('index');
            }

            if (!$form->fusioncommunes->receive()) {
                throw new Exception('Erreur lors de la réception du fichier');
            }

            $this->view->downloadComplete = true;

            $service = new Service_FusionCommand();
            $service->mergeArrayCommune(
                json_decode(
                    file_get_contents($form->fusioncommunes->getFileName())
                )
            );

            unlink($form->fusioncommunes->getFileName());
        }
    }
}
