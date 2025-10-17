<?php

class LegacyController extends Zend_Controller_Action
{
    public function matriceDesDroitsAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $cache->remove('acl');

        $this->redirect('/admin/groupes/matrice-des-droits');
    }
}
