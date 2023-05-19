<?php

class Plugin_LibelleDossierPrescriptions extends Zend_Controller_Plugin_Abstract
{
    public function postDispatch(Zend_Controller_Request_Abstract $request): void
    {
        if ('dossier' !== $request->getControllerName()) {
            return;
        }

        if (!in_array($request->getActionName(), ['prescription', 'prescription-edit'], true)) {
            return;
        }

        $view = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('view')
        ;

        $view->inlineScript()->appendScript(
            <<<'EOS'
$(document).ready(function() {
    $('#prescRappelRegTitle').text('Essais réalisés')
    $('select[name="TYPE_PRESCRIPTION_DOSSIER"] option:first').text('Essais réalisés')
})
EOS
        );
    }
}
