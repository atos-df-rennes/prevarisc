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

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $this->view->is_allowed_retablir = unserialize($cache->load('acl'))->isAllowed(Zend_Auth::getInstance()->getIdentity()['group']['LIBELLE_GROUPE'], 'rétablissement', 'rétablir');

        $this->view->assign('dossierSupprimer', $dossierSupprimer);
        $this->view->assign('etablissementSupprimer', $etablissementSupprimer);
    }
}
