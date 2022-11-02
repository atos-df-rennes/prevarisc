<?php

class RetablirController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');
        $this->view->headScript()->appendFile('js/tinymce.min.js');

        $DBDossier = new Model_DbTable_Dossier();
        $DBEtablissement = new Model_DbTable_Etablissement();

        $dossierSupprimer = $DBDossier->getDeleteDossier();
        $etablissementSupprimer = $DBEtablissement->getDeleteEtablissement();

        $this->view->assign('dossierSupprimer',$dossierSupprimer);
        $this->view->assign('etablissementSupprimer',$etablissementSupprimer);
    }

}
