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

    public function viderCacheDossierAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $dossierId = (int) $this->getRequest()->getParam('id');
        $etablissementsJson = $this->getRequest()->getParam('etablissements', '[]');

        // Décoder JSON des IDs établissements
        $etablissementsIds = json_decode($etablissementsJson, true);
        if (!is_array($etablissementsIds)) {
            $etablissementsIds = [];
        }

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $dbEtablissement = new Model_DbTable_Etablissement();

        // Vider cache pour chaque établissement modifié + son parent
        foreach ($etablissementsIds as $etablissementId) {
            $etablissementId = filter_var($etablissementId, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

            if (null === $etablissementId) {
                continue;
            }

            // Cache établissement
            $cache->remove('etablissement_id_'.$etablissementId);

            // Cache parent (logique legacy ligne 902-907 Service_Dossier.php)
            $parent = $dbEtablissement->getParent($etablissementId);
            if ($parent) {
                $cache->remove('etablissement_id_'.$parent['ID_ETABLISSEMENT']);
            }
        }

        // Rediriger vers page dossier Symfony
        $this->redirect('/dossier/'.$dossierId);
    }

    public function clearSearchCacheAndRedirectAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // Nettoyage du cache de recherche
        Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch')->clean(Zend_Cache::CLEANING_MODE_ALL);

        // Récupération de l'URL cible depuis les paramètres
        $target = $this->getRequest()->getParam('target', '/');

        $this->redirect($target);
    }

    public function clearCacheAndRedirectAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache')->clean(Zend_Cache::CLEANING_MODE_ALL);

        $target = $this->getRequest()->getParam('target', '/');

        $this->redirect($target);
    }
}
