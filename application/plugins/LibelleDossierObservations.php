<?php

class Plugin_LibelleDossierObservations extends Zend_Controller_Plugin_Abstract
{
    public function postDispatch(Zend_Controller_Request_Abstract $request): void
    {
        if ($request->getControllerName() !== 'dossier' || $request->getActionName() !== 'index') {
            return;
        }

        $view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('view');

        $view->inlineScript()->appendScript(
<<<EOS
$(document).ready(function() {
    $('#OBSERVATION .libelle').text('Observations introduction')
})
EOS
        );
    }
}
