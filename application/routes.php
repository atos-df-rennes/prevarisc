<?php

$router = Zend_Controller_Front::getInstance()->getRouter();

$matriceDesDroits = new Zend_Controller_Router_Route('legacy/admin/groupes/matrice-des-droits', [
    'controller' => 'legacy',
    'action' => 'matrice-des-droits',
    'resource' => 'gestion_parametrages',
    'privilege' => 'admin',
    'uri' => '/legacy/admin/groupes/matrice-des-droits',
]);
$sauvegarderUtilisateur = new Zend_Controller_Router_Route('legacy/admin/utilisateurs/sauvegarder', [
    'controller' => 'legacy',
    'action' => 'save-user',
    'resource' => 'gestion_parametrages',
    'privilege' => 'admin',
    'uri' => '/legacy/admin/utilisateurs/sauvegarder',
]);
$viderCacheDossier = new Zend_Controller_Router_Route('legacy/dossier/vider-cache', [
    'controller' => 'legacy',
    'action' => 'vider-cache-dossier',
    'uri' => '/legacy/dossier/vider-cache',
]);
$clearSearchCacheAndRedirect = new Zend_Controller_Router_Route('legacy/admin/clear-search-cache-and-redirect', [
    'controller' => 'legacy',
    'action' => 'clear-search-cache-and-redirect',
    'uri' => '/legacy/admin/clear-search-cache-and-redirect',
]);
$clearCacheAndRedirect = new Zend_Controller_Router_Route('legacy/admin/clear-cache-and-redirect', [
    'controller' => 'legacy',
    'action' => 'clear-cache-and-redirect',
    'uri' => '/legacy/admin/clear-cache-and-redirect',
]);
$viderCacheEtalissement = new Zend_Controller_Router_Route('legacy/etablissement/vider-cache', [
    'controller' => 'legacy',
    'action' => 'vider-cache-etablissement',
    'uri' => '/legacy/etablissement/vider-cache',
]);

$router->addRoute('legacy_matrice_des_droits', $matriceDesDroits);
$router->addRoute('legacy_sauvegarder_utilisateur', $sauvegarderUtilisateur);
$router->addRoute('legacy_vider_cache_dossier', $viderCacheDossier);
$router->addRoute('legacy_clear_search_cache_and_redirect', $clearSearchCacheAndRedirect);
$router->addRoute('legacy_clear_cache_and_redirect', $clearCacheAndRedirect);
$router->addRoute('legacy_vider_cache_etablissement', $viderCacheEtalissement);
