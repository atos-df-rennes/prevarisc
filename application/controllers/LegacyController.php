<?php

class LegacyController extends Zend_Controller_Action
{
    public function matriceDesDroitsAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache')->remove('acl');

        $this->redirect('/admin/groupes/matrice-des-droits');
    }

    public function saveUserAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $userId = $this->getRequest()->getParam('userId');

        Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch')->clean(Zend_Cache::CLEANING_MODE_ALL);
        Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache')->remove('user_id_'.$userId);

        $this->redirect('/admin/utilisateurs');
    }

    public function viderCacheCouchesCartographiquesAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache')->remove('couches_cartographiques');

        $this->redirect('/admin/couches-cartographiques');
    }
}
