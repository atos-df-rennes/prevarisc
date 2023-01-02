<?php

class RetablirController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $DBDossier = new Model_DbTable_Dossier();
        $DBEtablissement = new Model_DbTable_Etablissement();
        $servicePrivilege = new Service_Privilege();

        $dossierSupprimer = $DBDossier->getDeleteDossier();
        $etablissementSupprimer = $DBEtablissement->getDeleteEtablissement();

        $this->view->assign('dossierSupprimer', $dossierSupprimer);
        $this->view->assign('etablissementSupprimer', $etablissementSupprimer);
        $this->view->assign('is_allowed_delete_dossier', $servicePrivilege->isAllowed('suppression', 'delete_dossier'));
        $this->view->assign('is_allowed_delete_etablissement', $servicePrivilege->isAllowed('suppression', 'delete_etablissement'));
    }
}
