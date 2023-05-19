<?php

class Plugin_LibelleDossierObservations extends Zend_Controller_Plugin_Abstract
{
    public function postDispatch(Zend_Controller_Request_Abstract $request): void
    {
        if ('dossier' !== $request->getControllerName()) {
            return;
        }

        if ('index' !== $request->getActionName()) {
            return;
        }

        $view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('view')
        ;

        $view->inlineScript()->appendScript(
            <<<'EOS'
$(document).ready(function() {
    $('#OBSERVATION .libelle').text('Observations introduction')
})
EOS
        );
    }
}
