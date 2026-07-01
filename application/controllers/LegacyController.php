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

        // Vider le cache de recherche pour rester à jour (iso-fonctionnel saveAction legacy)
        Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch')->clean(Zend_Cache::CLEANING_MODE_ALL);

        // Rediriger vers la cible demandée, ou vers la page dossier par défaut
        $target = $this->getRequest()->getParam('target', '/dossier/'.$dossierId);
        $this->redirect($target);
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

    /**
     * Vide le cache Zend (etablissement_id_<id>) pour une liste d'établissements.
     *
     * Utilisé depuis Symfony après une action modifiant les données d'établissements
     * (ex : suppression d'un dossier, recalcul du dossier donnant avis).
     * Le cache établissement n'étant pas encore implémenté côté Symfony, ce nettoyage
     * passe par le legacy qui détient ce cache.
     *
     * Paramètres GET :
     *   ids    JSON array d'identifiants entiers (ex : "[1,2,3]")
     *   target URL cible après nettoyage (défaut : '/')
     */
    public function viderCacheEtablissementAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $ids = json_decode($this->getRequest()->getParam('ids', '[]'), true);
        if (!is_array($ids)) {
            $ids = [];
        }

        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        foreach ($ids as $id) {
            $id = filter_var($id, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

            if (null === $id) {
                continue;
            }

            $cache->remove('etablissement_id_'.$id);
        }

        // Nettoyage du cache de recherche legacy (iso-fonctionnel DossierController::deleteAction())
        Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch')->clean(Zend_Cache::CLEANING_MODE_ALL);

        $target = $this->getRequest()->getParam('target', '/');

        $this->redirect($target);
    }

    public function viderCacheCouchesCartographiquesAction(): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache')->remove('couches_cartographiques');

        $this->redirect('/admin/couches-cartographiques');
    }
}
